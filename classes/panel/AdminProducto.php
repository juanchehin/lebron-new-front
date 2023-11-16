<?php

class AdminProducto extends AdminMain
{
    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuProductos);
    }

    public function index()
    {
        $this->setPageTitle("Catálogo");
        $this->controlPermiso(Permiso::permisoVer);
        #--
        $export = new ExportOpts();
        $export->setExcelUrl($url = "!AdminProducto/exportar");
        $export->setPdfUrl($url . "?pdf=1");
        $this->setBotonNuevo("Agregar", self::sysUrl . "/productos/nuevo", "<span class='pull-right' id='dv-export'></span>");
        $columns[] = "C&oacute;digo";
        $columns[] = "Producto";
        $columns[] = "Detalle";
        $columns[] = "Mínimo.text-center";
        $columns[] = "Cantidad";
        $columns[] = "Precio.text-center";
        $columns[] = "Acci&oacute;n.col-md-2.text-center";
        #--
        $control = $this->_selectMarca();
        $control .= "<label for='alerta' class='btn btn-warning' style='padding:2px 4px'><input type='checkbox' id='alerta' />&nbsp;Stock mínimo</label>";
        $log_url = "<a href='" . self::sysUrl . "/productos/log' class='pull-right'>Historial</a>";
        $table = new HDataTable();
        $table->setColumns($columns);
        $table->setHideDateRange();
        $table->setHtmlControl($control);
        $table->setRows($this->getRows());
        $values['_table'] = $table->drawTable();
        $values['log_url'] = $log_url;
        $this->setParams($values);
        #--
        $this->setBody("producto-index");
    }

    private function _selectMarca()
    {
        $control = "<select id='id_marca' name='id_marca' class='form-control' required>";
        $control .= "<option value=''>Marca</option>";
        foreach (Categoria::where('tipo', Categoria::tipoMarca)->orderBy("nombre")->get() as $marca)
        {
            $control .= "<option value='{$marca->id_item}'>{$marca->titulo}</option>";
        }
        $control .= "</select>";
        return $control;
    }

    public function getRows()
    {
        $text = trim($_POST['search_box']);
        $alerta = intval($_POST['alerta']);
        #--
        $where['borrado'] = 0;
        if ( $id_marca = floatval($_POST['id_marca']) )
        {
            $where['id_marca'] = $id_marca;
        }
        #--
        $query = Articulo::where($where);
        if ( $alerta )
        {
            foreach (array_keys(Local::$_LOCALES) as $v)
            {
                $where[] = "CAST(JSON_EXTRACT(`cantidad`,'$.\"{$v}\"') AS UNSIGNED) <= `stock_alerta`";
            }
            $where = implode(" OR ", $where);
            $query = $query->whereRaw("({$where})");
        }
        $query = $query->where(function ($sql) use ($text) {
            $sql->where('codigo', "{$text}")->orWhere('producto', 'LIKE', "%{$text}%");
        });
        $articulos = $query->orderBy("id_producto", "DESC");
        $this->reporte($articulos->get());
        $count = $articulos->count();
        $result = $articulos->paginate($this->x_page);
        $data = $table = null;
        foreach ($result as $index => $articulo)
        {
            $cantidades = $articulo->cantidad_string;
            /*if ( strlen($articulo->codigo) < 14 )
            {
                $articulo->codigo = $this->zeroFill($articulo->codigo);
                $articulo->save();
            }*/
            #--
            $data .= "<tr id='" . ($id = $articulo->id_producto) . "'>";
            $data .= "<td>{$articulo->codigo}</td>";
            $data .= "<td>";
            if ( $this->controlPermiso(Permiso::permisoEditar, false) )
            {
                $data .= "<a href='" . self::sysUrl . "/productos/editar/{$articulo->id_producto}'><i class='fa fa-pencil-alt'></i></a>&nbsp;";
            }
            $data .= $id . " - " . $articulo->nombre;
            $data .= "</td>";
            $data .= "<td>" . ($articulo->detalle ?: " - ") . "</td>";
            $data .= "<td class='text-center'>{$articulo->stock_alerta}</td>";
            $data .= "<td>{$cantidades}</td>";
            $data .= "<td class='amount'>" . HFunctions::formatPrice($articulo->precio) . "</td>";
            $data .= "<td class='text-center'>";
            $data .= "<a href='" . self::PANEL_URI . "/productos/log/{$id}' title='Movimientos'><i class='fa fa-list-alt text-warning'></i></a>";
            if ( $this->es_admin )
            {
                $btn_cls = !$articulo->id_parent ? "primary" : "default";
                $data .= "<a href='" . self::sysUrl . "/productos/{$id}/online' class='btn btn-{$btn_cls}'><i class='fa fa-laptop'></i></a>";
            }
            if ( $this->controlPermiso(Permiso::permisoStock, false) )
            {
                $data .= "<a href='javascript:void(0)' class='btn btn-info' onclick='get_form(this)'><i class='fa fa-calculator'></i> Stock</a>";
            }
            #--
            if ( $this->controlPermiso(Permiso::permisoBorrar, false) )
            {
                $data .= "<a href='javascript:void(0)' onclick='dt_delete(this)' class='btn btn-danger'><i class='fa fa-trash'></i></a>";
            }
            $data .= "</td>";
            $data .= "</tr>";
        }
        #--
        $data .= "<tr class='not' data-count='{$count}'><td colspan='12'>{$this->replaceLinks($result->links())}</td></tr>";
        #--
        if ( self::isXhrRequest() )
        {
            die($data);
        }
        return $data;
    }

    public function cantidadesForm()
    {
        $id_producto = intval($_POST['id']);
        $params = array(
            "locales" => Local::$_LOCALES,
            "data" => Articulo::find($id_producto),
            'adminCp' => static::$_adminCp
        );
        $this->setParams($params);
        $form = $this->loadView("admin/producto-stock-form");
        HArray::jsonResponse("body", $form);
    }

    public function actualizarStock()
    {
        $id_producto = floatval($_POST['id_producto']);
        $codigo = trim($_POST['codigo']);
        $id_local = trim($_POST['local']);
        $cantidad = floatval($_POST['cantidad']);
        $precio = floatval($_POST['precio']);
        $destino = floatval($_POST['destino']);
        $accion = $_POST['accion'];
        $es_compra = ($accion == 'SUMA');
        $es_venta = (!$destino || !Local::esDeposito($id_local));
        $con_log = $_POST['con_log'];
        #--
        if ( $cantidad )
        {
            if ( !key_exists($id_local, Local::$_LOCALES) )
            {
                HArray::jsonError("Seleccionar el Local", "local");
            }
            #--
            if ( Local::esDeposito($id_local) && !$es_compra && !$destino )
            {
                HArray::jsonError("Seleccionar Destino", "destino");
            }
        }

        if ( !$precio )
        {
            HArray::jsonError("Indicar el precio", "precio");
        }


        $articulo = Articulo::find($id_producto);
        if ( $cantidad > 0 )
        {
            $tipo = $es_venta ? Venta::tpVenta : Venta::tpCompra;
            #-- Obtener Stock actual
            $stock = $articulo->cantidad_array;
            #-- Restar la cantidad correspondiente al local seleccionado si es traspaso o venta
            $actual = $stock[$id_local];
            $stock[$id_local] = $es_compra ? (intval($actual) + $cantidad) : ($actual - $cantidad);
            if ( $stock[$id_local] < 0 )
            {
                HArray::jsonError("La cantidad ingresada es mayor al stock disponible", "cantidad");
            }
            #--
            if ( !$es_venta && !$es_compra )
            {
                #-- trapaso de depósito a Negocio
                $tipo = Venta::tpTraspaso;
                $actual_destino = $stock[$id_destino = ($destino ?: Local::mitreNegocio)];
                $stock[$id_destino] = $destino ? ($actual_destino + $cantidad) : ($actual_destino - $cantidad);
                if ( $stock[$id_destino] < 0 )
                {
                    $stock[$id_destino] = 0;
                }
            }
            #-- Guardar el nuevo valor de cantidad
            $articulo->cantidad_json = $stock;
            if ( $con_log )
            {
                if ( $es_compra )
                {
                    $flag = "Ingreso";
                }
                elseif ( $es_venta )
                {
                    $flag = "Egreso";
                }
                else // traspaso
                {
                    $flag = "Traspaso";
                }
                $arr_flag = ['origen' => $id_local, 'destino' => $destino, 'tipo' => $flag];
                $this->_setLog($tipo, $id_local, $id_producto, $arr_flag, $cantidad, $articulo->precio);
            }
        }
        #--
        if ( $codigo )
        {
            $articulo->codigo = $codigo;
        }
        $articulo->precio = $precio;
        $precio_online = round(($precio * 1.05) + 5);
        if ( $articulo->precio_online < $precio_online )
        {
            if ( $parent = $articulo->hasParent )
            {
                $parent->precio_online = $precio_online;
                $parent->save();
            }
            else
            {
                $articulo->precio_online = $precio_online;
            }
        }
        $articulo->save();
        HArray::jsonResponse('ok', true);
    }

    private function _setLog($tipo, $id_local, $id_producto, $flag, $cantidad, $precio)
    {
        $id_usuario = $this->admin_user->id_usuario;
        $nro_operacion = 0;
        if ( $tipo <> Venta::tpTraspaso )
        {
            $where[] = "tipo='{$tipo}'";
            $where[] = "id_usuario='{$id_usuario}'";
            $where[] = "id_sucursal='{$id_local}'";
            //$where[] = "DATE(fecha_hora)='" . HDate::today(false) . "'";
            $where[] = "fecha_hora LIKE '" . date('Y-m-d H') . "%'";
            if ( !$log = Venta::whereRaw(implode(" AND ", $where))->first() )
            {
                $log = new Venta();
            }
            $log->tipo = $tipo;
            $log->id_usuario = $id_usuario;
            $log->id_sucursal = $id_local;
            $log->save();
            #--
            $nro_operacion = $log->id_venta;
        }
        #--
        //if ( !$linea_log = LineaVenta::where(['id_venta' => $log->id_venta, 'id_producto' => $id_producto])->first() )
        //{
        $linea_log = new LineaVenta();
        //}
        $linea_log->cantidad += $cantidad;
        $linea_log->id_venta = $nro_operacion ?: $id_usuario;
        $linea_log->id_producto = $id_producto;
        $linea_log->flag = json_encode($flag);
        if ( $flag['tipo'] == "Egreso" )
        {
            $linea_log->subtotal = ($linea_log->cantidad * $precio);
        }
        #--
        $linea_log->save();
    }

    private function zeroFill($value, $length = 14)
    {
        return str_pad($value, $length, 0, STR_PAD_LEFT);
    }

    public function productoForm($id_producto = null)
    {
        $locales = Local::$_LOCALES;
        $params['alerta'] = $this->config['stock_alerta'];
        if ( self::isXhrRequest() && $_POST )
        {
            $id_articulo = floatval($_POST['id_producto']);
            $codigo = trim($_POST['codigo']);
            $nombre = trim($_POST['nombre']);
            $id_marca = floatval($_POST['id_marca']);
            $marca = trim($_POST['id_marca']);
            $sabor = trim($_POST['sabor']);
            $peso = trim($_POST['peso']);
            $unidad = trim($_POST['unidad']);
            #-- Nuevos campos 01/06/2018
            $alerta = intval($_POST['alerta']);// ?: $params['alerta']);
            $precio = floatval($_POST['precio']);
            $cantidad = (array)$_POST['stock'];
            $texto = trim($_POST['texto']);
            //HArray::varDump($id_marca);
            #--
            $articulo = Articulo::findOrNew($id_articulo);
            $original = $articulo['original'];
            #--
            if ( !$articulo->codigo && !$codigo )
            {
                HArray::jsonError("Ingresar un código", "codigo");
            }

            $code_exists = Articulo::where('id_producto', '<>', $id_articulo)->where('codigo', $codigo)->first();
            if ( $code_exists )
            {
                HArray::jsonError("Ingresar un código distinto", "codigo");
            }

            if ( !$nombre )
            {
                HArray::jsonError("Ingresar el nombre de Producto", "nombre");
            }

            if ( !$id_marca && !$marca )
            {
                HArray::jsonError("Seleccionar una marca");
            }
            #--
            if ( !$peso )
            {
                HArray::jsonError("Ingresar contenido", "peso");
            }

            /*if ( !$sabor || !key_exists($sabor, Articulo::$_SABORES) )
            {
                HArray::jsonError("Seleccionar Sabor", "sabor");
            }

            if ( !$cantidad || count($cantidad) != count($locales) )
            {
                HArray::jsonError("Ingresar las cantidades por local", "stock[1]");
            }*/

            /*if ( !$precio )
            {
                HArray::jsonError("Indicar el precio", "precio");
            }*/
            #--
            if ( $codigo )
            {
                $articulo->codigo = $codigo;
            }
            #-- 25/04/2019 --
            if ( !$id_marca && $marca )
            {
                /*if ( Categoria::where('tipo', Categoria::tipoMarca)->where('nombre', "LIKE", "{$marca}%")->first() )
                {
                    HArray::jsonError("La marca ya existe, puedes seleccionarla de la lista");
                }*/
                $new_marca = new Categoria();
                $new_marca->id_item_padre = 0;
                $new_marca->tipo = Categoria::tipoMarca;
                $new_marca->nombre = mb_strtolower($marca);
                $new_marca->save();
                #--
                $id_marca = $new_marca->id_item;
            }
            #--
            $articulo->producto = mb_strtolower($nombre);
            $articulo->id_marca = $id_marca;
            //$articulo->marca = mb_strtolower($marca);
            $articulo->sabor = mb_strtolower($sabor);
            $articulo->peso = $peso . " {$unidad}";
            $articulo->precio = $precio;
            $precio_online = round(($precio * 1.05) + 5);
            if ( $precio_online > $articulo->precio_online )
            {
                if ( $parent = $articulo->hasParent )
                {
                    $parent->precio_online = $precio_online;
                    $parent->save();
                }
                else
                {
                    $articulo->precio_online = $precio_online;
                }
            }
            $articulo->stock_alerta = $alerta;
            $articulo->save();
            //HArray::varDump($articulo);
            #-- 21/08/2019 -- Log de ingreso de Artículo
            if ( $cantidad && !$id_articulo )
            {
                foreach ($cantidad as $id_local => $cnt)
                {
                    if ( !$cnt )
                    {
                        continue;
                    }
                    //$tipo =
                    $arr_flag = ['origen' => $id_local, 'destino' => 0, 'tipo' => "Ingreso"];
                    $this->_setLog(Venta::tpCompra, $id_local, $articulo->id_producto, $arr_flag, $cnt, $precio);
                }
                #--
                $articulo->cantidad_json = $cantidad;
                $articulo->save();
            }
            //$articulo->texto = $texto;
            //HArray::varDump(array_diff($articulo['attributes'],$original));
            $json['success'] = true;
            IF ( $id_articulo )
            {
                $json['location'] = self::sysUrl . "/productos";
            }
            if ( array_diff($articulo['attributes'], $original) )
            {
                $json['notice'] = "Los datos han sido guardados de manera correcta";
            }
            //$json['location'] = self::PANEL_URI . "productos";
            HArray::jsonResponse($json);
        }
        $this->addStyle("static/plugin/jodit/jodit.min.css");
        $this->addScript("static/plugin/jodit/jodit.min.js");
        $form_title = "Registro de Producto";
        $permiso = Permiso::permisoCrear;
        $articulo = Articulo::find($id_producto);
        if ( $articulo )
        {
            $permiso = Permiso::permisoEditar;
            $form_title = "Editar #{$id_producto} - \"{$articulo->producto}\"";
            $this->setParams('articulo', $articulo);
        }
        $this->setPageTitle($form_title);
        $this->controlPermiso($permiso);
        $params['locales'] = $locales;
        $params['sabores'] = Articulo::select('sabor')->whereRaw("!borrado AND sabor <> ''")->distinct('sabor')->orderBy('sabor')->get();
        $params["unidades"] = Articulo::$_UNIDADES;
        $params['selectMarca'] = $this->_selectMarca();
        $this->setParams($params);
        $this->setBody("producto-form");
    }

    public function exportar()
    {
        //include "classes/controls/mpdf/Mpdf.php";
        ini_set("display_errors", "On");
        $pdf = isset($_GET['pdf']);
        $this->setParams(array('articulos' => $this->reporte(), 'bc' => $_GET['bc']));
        $html = $this->loadView("admin/producto-exportar");
        die($html);
        //ExportOpts::exportar($html, $pdf, true);
    }

    public function select()
    {
        $txt = trim($_POST['q']);
        $result = array();
        $conds['borrado'] = 0;
        //$conds['id_sucursal'] = $this->id_sucursal;
        $productos = Articulo::where($conds)->where(function ($q) use ($txt) {
            $q->where('codigo', 'LIKE', "{$txt}%")->orWhere('producto', 'LIKE', "%{$txt}%");
        })->get();

        foreach ($productos as $producto)
        {
            $result[] = array('id' => $producto->id_producto, 'text' => "{$producto->id_producto} - " . $producto->nombre);
        }

        die(json_encode($result));
    }

    public function setEstado($key = null, $value = null)
    {
        $id = floatval($_POST['id_articulo']);
        $valor = trim($value ?: $_POST['estado']);
        $attr = trim($key ?: $_POST['attr']);
        if ( $articulo = Articulo::find($id) )
        {
            $articulo->{$attr} = $valor;
            $articulo->save();
        }
    }

    public function eliminar()
    {
        #-- Realiza la baja lógica
        $id_producto = floatval($_POST['id']);
        if ( ($articulo = Articulo::find($id_producto)) )
        {
            $articulo->borrado = 1;
            $articulo->codigo = time();
            $articulo->save();
        };
        return;
    }
}