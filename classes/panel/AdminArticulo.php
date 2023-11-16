<?php



class AdminArticulo extends AdminMain

{

    static $_columns = array(

        "C&oacute;digo",

        "Producto",

        "Detalle",

        //$columns[] = "Mínimo.text-center";

        "Cantidad",

        "Precio.text-center",

        "Vencimiento",

        "Acci&oacute;n.col-md-2.text-center"

    );

    static $_columns_admin = array(

        "C&oacute;digo",

        "Producto",

        "Detalle",

        //$columns[] = "Mínimo.text-center";

        "Cantidad",

        "Precio.text-center",

        "Vencimiento",

        "IVA",

        "Desc.",

        "Costo",

        "Acci&oacute;n.col-md-2.text-center"

    );



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

        $export->setExcelUrl($url = "!AdminArticulo/exportar");

        $export->setPdfUrl($url . "?pdf=1");

        //$this->setBotonNuevo("Registrar", self::sysUrl . "/productos/nuevo", "<span class='pull-right' id='dv-export'></span>");

        $this->setBotonNuevo("Registrar", "javascript:void(0)", "<span class='pull-right' id='dv-export'></span>");

        #--

        $control = $this->_selectMarca();

        //$control .= $this->_selectMarca(Categoria::tipoCategoria);

        $control .= "<label for='alerta' class='btn btn-warning' style='padding:2px 4px'><input type='checkbox' id='alerta' />&nbsp;Stock mínimo</label>";

        $log_url = "<div class='pull-right' style='text-align:right;text-transform: uppercase'>";

        $log_url .= "<a href='" . self::appUrl . "/lista-de-precios.html' target='_blank' style='display: block;margin-bottom:0'>Lista</a>";

        $log_url .= "<a href='" . self::sysUrl . "/productos/log'>Historial</a>";

        $log_url .= "</div>";

        $table = new HDataTable();

        if($this->admin_user->id_usuario == 1 || $this->admin_user->id_usuario == 25)
        {

            $table->setColumns(static::$_columns_admin);
        }else{
            $table->setColumns(static::$_columns);
        }

        
        $table->setHideDateRange();

        $table->setHtmlControl($control . $log_url);

        $table->setRows($this->getRows());

        $values['_table'] = $table->drawTable();

        $values['log_url'] = $log_url;

        $this->setParams($values);

        #--

        $this->setBody("articulo-index");

    }



    private function _selectMarca($tipo = Categoria::tipoMarca)
    {
        $control = "<select id='id_{$tipo}' name='id_{$tipo}' class='form-control' required>";

        $control .= "<option value=''>" . ucfirst($tipo) . "</option>";

        foreach (Categoria::where('tipo', $tipo)->where('borrado','=',0)->orderBy("nombre")->get() as $marca)

        {

            $control .= "<option value='{$marca->id_item}'>{$marca->titulo}</option>";

        }

        $control .= "</select>";

        return $control;

    }



    public function getRows($reporte = false)
    {

        $text = trim($_POST['search_box']);

        $alerta = intval($_POST['alerta']);

        #--

        $where[] = "!`borrado`";

        if ( $id_marca = floatval($_POST['id_marca']) )

        {

            $where[] = "`id_marca` = '{$id_marca}'";

        }

        #--

        if ( ($categoria = floatval($_POST['categoria'])) )

        {

            $where[] = "`id_categoria` = '{$categoria}'";

        }

        #--

        $query = Articulo::whereRaw(implode(" AND ", $where));

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

        $articulos = $query->orderBy("id_marca")->orderBy('producto')->orderBy("id_producto", "DESC");

        $this->reporte($articulos->get());

        $count = $query->count();

        $result = $articulos->paginate($this->x_page);

        $data = $table = null;

        $currentDate = date('Y-m-d');

        foreach ($result as $index => $articulo)

        {

            $cantidades = $articulo->cantidad_string;

            /*if ( strlen($articulo->codigo) < 14 )

            {

                $articulo->codigo = $this->zeroFill($articulo->codigo);

                $articulo->save();

            }*/

            $data .= "<tr id='" . ($id = $articulo->id_producto) . "'>";

            $data .= "<td>{$articulo->codigo}</td>";

            $data .= "<td>";

            if ( $this->controlPermiso(Permiso::permisoEditar, false) )

            {

                //$data .= "<a href='" . self::sysUrl . "/productos/editar/{$articulo->id_producto}'><i class='fa fa-pencil-alt'></i></a>&nbsp;";

                $data .= "<a href='javascript:void(0)' rel='1' onclick='get_form(\"{$id}\", 1)'><i class='fa fa-pencil-alt'></i></a>&nbsp;";

            }

            $data .= $id . " - " . $articulo->nombre;

            $data .= "</td>";

            $data .= "<td>" . ($articulo->detalle ?: " - ") . "</td>";

            //$data .= "<td class='text-center'>{$articulo->stock_alerta}</td>";

            $data .= "<td>{$cantidades}</td>";

            $data .= "<td class='amount'>";

            foreach ($articulo->array_precios as $label => $precio)

            {

                $data .= strtoupper($label . " {$precio}") . "<br/>";

            }


            $today = strtotime($currentDate);

            $venc = strtotime($articulo->fecha_vencimiento);

          

            if ( ($today >= $venc) && ($articulo->fecha_vencimiento) )
            {
                // vencido
                $data .= "<td class='alert alert-danger'>" . HDate::dateFormat($articulo->fecha_vencimiento) . "</td>";
            }else{

                if ( $articulo->fecha_vencimiento )
                {
                    $data .= "<td>" . HDate::dateFormat($articulo->fecha_vencimiento) . "</td>";
                }else{
                    $data .= "<td>" . " - " . "</td>";
                }

            }

            if($this->admin_user->id_usuario == 1 || $this->admin_user->id_usuario == 25)
            {

                $data .= "<td>{$articulo->iva}</td>";

                $data .= "<td>{$articulo->descuento_prov}</td>";

                $data .= "<td>{$articulo->costo}</td>";

            }



            $data .= "</td>";

            if ( !$reporte ):

                $data .= "<td class='text-center'>";

                $data .= "<a href='" . self::PANEL_URI . "/productos/log/{$id}' title='Movimientos'><i class='fa fa-list-alt text-warning'></i></a>";

                if ( $this->controlPermiso(Permiso::permisoPublicar, false) )

                {

                    $btn_cls = !$articulo->id_parent ? "primary" : "default";

                    $data .= "<a href='" . self::sysUrl . "/productos/{$id}/online' class='btn btn-{$btn_cls}'><i class='fa fa-laptop'></i></a>";

                }

                #--

                if ( $this->controlPermiso(Permiso::permisoStock, false) || ($this->admin_user->id_usuario == 41))

                {

                    $data .= "<a href='javascript:void(0)' class='btn btn-info' onclick='get_form(\"{$id}\")'><i class='fa fa-calculator'></i> Stock</a>";

                }

                #--

                if ( $this->controlPermiso(Permiso::permisoBorrar, false) )

                {

                    $data .= "<a href='javascript:void(0)' onclick='dt_delete(this)' class='btn btn-danger'><i class='fa fa-trash'></i></a>";

                }

                $data .= "</td>";

            endif;

            $data .= "</tr>";

        }

        #--

        if ( !$reporte )
        {
            $data .= "<tr class='not' data-count='{$count}'><td colspan='12'>{$this->replaceLinks($result->links())}</td></tr>";

            #--

            if ( self::isXhrRequest() )
            {
                die($data);
            }

        }
        return $data;

    }



    public function cantidadesForm()
    {

        $id_producto = floatval($_POST['id']);

        $params = array(

            "locales" => Local::$_LOCALES,

            "data" => ($data = Articulo::find($id_producto)),

            'adminCp' => static::$_adminCp,

            'motivos' => $this->motivosStock()

        );

        $array_pc = $data->arr_precio_compra;

        $utilidad = $data->arr_utilidad;

        $precios = new PreciosBlock();

        $precios->setMayorista($array_pc['valor'], $array_pc['unidad']);

        $precios->setUtilidad($utilidad['valor'], $utilidad['unidad']);

        $precios->isParent($data->id_parent);

        $params['preciosBlock'] = $precios->draw();

        $this->setParams($params);

        $form = $this->loadView("admin/articulo-stock-form");

        HArray::jsonResponse("body", $form);

    }



    public function motivosStock()
    {

        $accion = trim($_GET['accion']);

        $sql = "atributo LIKE '{$accion}_%'";

        if ( !$accion )

        {

            $sql = "atributo LIKE 'egreso_%' OR atributo LIKE 'ingreso_%'";

        }

        $lineas = LineaVenta::whereRaw($sql)->distinct('atributo')->pluck('atributo');

        foreach ($lineas as $linea)

        {

            list($operacion, $motivo) = explode("_", $linea);

            $arr[$operacion][] = $motivo;

        }

        return $arr;

    }



    public function selectArticulo()
    {

        $where = $items = array();

        $exc = (array)json_decode($_POST['exc'], true);

        $where[] = "!borrado AND `id_categoria` <> '" . Categoria::ctgPromo . "'";

        $where[] = "!id_parent AND id_producto NOT IN ('" . implode("','", $exc) . "')";

        if ( $id_marca = intval($_POST['id_marca']) )

        {

            $where[] = "`id_marca`='{$id_marca}'";

        }

        #--

        $result = Articulo::whereRaw(implode(" AND ", $where))->orderBy('producto')->get();

        foreach ($result as $res)

        {

            $items[$res->id_producto] = array('label' => $res->nombre_producto . " ({$res->cantidad_online} Un.)", 'precio' => $res->ecommerce_precio);

        }

        HArray::jsonResponse($items);

    }



    public function guardar()
    {

        //ini_set("display_errors", "On");

        $id_producto = floatval($_POST['id_producto']);

        $stockUpdate = isset($_GET['sf']); //stock form

        $codigo = trim($_POST['codigo']);

        $nombre = trim($_POST['nombre']);

        $id_categoria = floatval($_POST['id_categoria']);

        $id_marca = floatval($_POST['id_marca']);

        $marca = trim($_POST['id_marca']);

        $sabor = trim($_POST['sabor']);

        $peso = trim($_POST['peso']);

        $unidad = trim($_POST['unidad']);

        #-- Nuevos campos 01/06/2018

        $alerta = intval($_POST['alerta']);

        $id_local = trim($_POST['local']);

        $cantidad = $_POST['stock'];

        $precio_compra = (array)$_POST['precio_compra'];

        $utilidad = array_filter((array)$_POST['utilidad']);

        $destino = floatval($_POST['destino']);

        $accion = $_POST['accion'];

        $con_log = $_POST['con_log'];

        $es_traspaso = ($accion == "Traspaso");

        $es_venta = ($accion == "Egreso");

        $esCompra = ($accion == ucfirst(Venta::tpIngreso));

        $importado = isset($_POST['importado']);

        $fecha_vencimiento = $_POST['fecha_vencimiento'];

        #-- 17/01/2021

        $observacion = trim($_POST['nota']);

        #--

        $articulo = Articulo::find($id_producto);

        $stock_bkp = 0;

        #--

        if ( !$codigo )

        {

            HArray::jsonError("Ingresar un código", "codigo");

        }

        #--

        $code_exists = Articulo::whereRaw("!`borrado` AND id_producto <> '{$id_producto}' AND codigo ='{$codigo}'")->first();

        if ( $code_exists )

        {

            HArray::jsonError("Ingresar un código distinto", "codigo");

        }

        #--

        if ( !$stockUpdate )

        {

            if ( $code_exists )

            {

                HArray::jsonError("Ingresar un código distinto", "codigo");

            }



            if ( !$nombre )

            {

                HArray::jsonError("Ingresar el nombre de Producto", "nombre");

            }



            if ( !$id_categoria )

            {

                HArray::jsonError("seleccionar una categoría", "id_categoria");

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

            #--

            if ( !$articulo )

            {

                $articulo = new Articulo();

            }

        }

        elseif ( $cantidad )

        {

            if ( !key_exists($id_local, Local::$_LOCALES) )

            {

                HArray::jsonError("Seleccionar el Local", "local");

            }

            #--

            if ( $es_traspaso && !$destino )

            {

                HArray::jsonError("Seleccionar Destino", "destino");

            }

        }



        if ( !$articulo->hasParent && count($precio_compra) < 2 )

        {

            HArray::jsonError("Indicar el precio y seleccionar unidad", "precio_compra[]");

        }

        #--

        if ( count($utilidad) > 0 && count($utilidad) < 2 )

        {

            HArray::jsonError("Indicar valor y seleccionar unidad", "utilidad[]");

        }

        #--

        $json['ok'] = 1;

        if ( $codigo )

        {

            $articulo->codigo = $codigo;

        }

        #--

        $articulo->precio_compra = implode("|", array_filter($precio_compra));

        $articulo->utilidad = implode("|", array_filter($utilidad));

        $articulo->id_sucursal = $importado;
	
    	if($fecha_vencimiento)
        {
            $item->fecha_vencimiento = $fecha_vencimiento;
        }


        if ( !$stockUpdate )

        {

            $original = $articulo['original'];

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

            $articulo->producto = mb_strtolower($nombre);

            $articulo->id_categoria = $id_categoria;

            $articulo->id_marca = $id_marca;

            //$articulo->marca = mb_strtolower($marca);

            $articulo->sabor = mb_strtolower($sabor);

            $articulo->peso = $peso . " {$unidad}";

            $articulo->stock_alerta = $alerta;

            if ( $cantidad && !$id_producto )

            {

                foreach ($cantidad as $id_local => $cnt)

                {

                    if ( !$cnt )

                    {

                        continue;

                    }

                    //$tipo =

                    $arr_flag = ['origen' => $id_local, 'destino' => 0, 'tipo' => "Ingreso"];

                    $linea = $this->_setLog(Venta::tpCompra, $id_local, $articulo->id_producto, 0, $cnt, $articulo->array_precios['mayorista']);

                }

                #--

                $articulo->cantidad_json = $cantidad;

                //$articulo->save();

            }

            #--

            if ( !array_diff($articulo['attributes'], $original) )

            {

                $json['notice'] = "Los datos han sido guardados de manera correcta";

            }

        }

        elseif ( floatval($cantidad) > 0 )

        {

            $arr_stck = array();

            #-- Obtener Stock actual

            $stock = $articulo->cantidad_array;

            #-- Restar la cantidad correspondiente al local seleccionado si es traspaso o venta

            $actual = $stock_bkp = floatval($stock[$id_local]);

            //$stock[$id_local] = ($es_venta || $es_traspaso) ? ($actual - $cantidad) : (intval($actual) + $cantidad);

            //$actual = $stock[$id_local];

            if ( !$esCompra && (($actual - $cantidad) < 0) )

            {

                HArray::jsonError("La cantidad ingresada es mayor al stock disponible", "cantidad");

            }

            #--

            if ( $esCompra )

            {

                $actual += $cantidad;

            }

            else

            {

                $actual -= $cantidad;

                $stock_bkp = $actual;

                if ( $es_traspaso )

                {

                    #-- trapaso de depósito a Negocio

                    $arr_stck[2] = $stock[$id_destino = ($destino ?: Local::mitreNegocio)];

                    //$stock[$id_destino] = $destino ? ($actual_destino + $cantidad) : ($actual_destino - $cantidad);

                    $stock[$id_destino] += $cantidad;

                }

            }

            #-- Guardar el nuevo valor de cantidad

            if ( $actual <= 0 )

            {

                $actual = $stock_bkp = 0;

            }

            $stock[$id_local] = $actual;

            //HArray::varDump($stock);

            $arr_stck[1] = $stock_bkp;

            $articulo->cantidad_json = $stock;

            if ( $con_log )

            {

                if ( $motivo = trim($_POST['motivo']) )

                {

                    $accion .= "_" . ucfirst(mb_strtolower($motivo));

                }

                ksort($arr_stck);

                //$arr_flag = ['origen' => $id_local, 'destino' => $destino, 'tipo' => $accion];

                $linea = $this->_setLog($accion, $id_local, $id_producto, $destino, $cantidad);

                $linea->stock = implode("&", $arr_stck);

                $linea->nota = mb_strtoupper($observacion);

                $linea->save();

            }

        }

        $articulo->save();

        #--

        $json['ok'] = $articulo->id_producto;

        $json['codigo'] = $articulo->codigo;

        $json['label'] = $articulo->nombre_producto;

        HArray::jsonResponse($json);

    }



    private function _setLog($tipo, $id_local, $id_producto, $id_local_destino, $cantidad)
    {

        $id_usuario = $this->admin_user->id_usuario;

        $nro_operacion = 0;

        if ( false && $no_es_traspaso = (strtolower($tipo) <> Venta::tpTraspaso) )

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

        $linea_log->atributo = $tipo;

        $linea_log->valor = $id_local;

        $linea_log->flag = $id_local_destino;

        //$linea_log->flag = json_encode($flag);

        if ( $no_es_traspaso )

        {

            $linea_log->subtotal = 0; //($linea_log->cantidad * $precio);

        }

        #--

        $linea_log->save();

        #--

        return $linea_log;

    }



    private function zeroFill($value, $length = 14)
    {

        return str_pad($value, $length, 0, STR_PAD_LEFT);

    }



    public function productoForm()
    {

        $form_title = "Registro de Producto";

        $permiso = Permiso::permisoCrear;

        $id_producto = floatval($_POST['id']);

        $fromBuy = isset($_GET['fb']);

        if ( $articulo = Articulo::find($id_producto) )

        {

            $permiso = Permiso::permisoEditar;

            $form_title = "Editar #{$id_producto} - \"{$articulo->producto}\"";

            $this->setParams('articulo', $articulo);

        }

        //$this->setPageTitle($form_title);

        $this->controlPermiso($permiso);

        $locales = Local::$_LOCALES;

        $alerta = $this->config['stock_alerta'];

        $sabores = Articulo::select('sabor')->whereRaw("!borrado AND sabor <> ''")->distinct('sabor')->orderBy('sabor')->get();

        $unidades = Articulo::$_UNIDADES;

        $selectMarca = $this->_selectMarca();

        $selectCategoria = $this->_selectMarca(Categoria::tipoCategoria);

        #--

        $array_pc = $articulo->arr_precio_compra;

        $utilidad = $articulo->arr_utilidad;

        $precios = new PreciosBlock();

        $precios->setMayorista($array_pc['valor'], $array_pc['unidad']);

        $precios->setUtilidad($utilidad['valor'], $utilidad['unidad']);

        $precios->isParent($articulo->id_parent);

        $preciosBlock = $precios->draw();

        // $this->setParams($params);

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <form action="!<?= self::class ?>/guardar" id="producto-frm" autocomplete="off">

                    <div class="col-md-4 form-group">

                        <label for="codigo">C&oacute;digo <i class="required"></i></label>

                        <?php if ( false ) : ?>

                            <div class="form-control"><?= $articulo->codigo ?></div>

                        <?php else : ?>

                            <label class="small" for="auto" style="margin-left:20px;display: inline-block">

                                <input type="checkbox" id="auto" name="auto" style="margin-top:0">&nbsp;GENERAR

                            </label>

                            <input type="tel" maxlength="20" name="codigo" class="form-control" id="codigo" value="<?= $articulo->codigo ?>" required autofocus>

                        <?php endif; ?>

                    </div>

                    <div class="form-group col-md-8">

                        <label for="nombre">Producto <i class="required"></i></label>

                        <input type="text" name="nombre" class="form-control" id="nombre" value="<?= $articulo->producto ?>" required>

                    </div>

                    <div class="col-md-6 form-group">

                        <label for="id_categoria">Categor&iacute;a <i class="required"></i></label>

                        <?= $selectCategoria ?>

                    </div>

                    <div class="form-group col-md-6">

                        <label for="marca">Marca</label>

                        <?php if ( false ): ?>

                            <input type="text" class="form-control" id="marca" name="marca" value="<?= $articulo->marca ?>">

                        <?php endif; ?>

                        <?= $selectMarca ?>

                    </div>

                    <div class="form-group col-md-4">

                        <label for="peso">Medida/Contenido <i class="required"></i></label>

                        <div class="input-group-addon">

                            <input type="tel" id="peso" name="peso" class="form-control" value="<?= $articulo->int_peso ?>"/>

                        </div>

                        <div class="input-group-addon">

                            <select name="unidad" class="form-control" id="">

                                <?php foreach ($unidades as $unidad) : ?>

                                    <option <?= ($unidad == $articulo->unidad) ? "selected" : null ?>><?= $unidad ?></option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>

                    <div class="form-group col-md-5">

                        <label for="sabor">Variante</label>

                        <select id="sabor" name="sabor" minlength="0" class="form-control">

                            <option value="">Seleccionar</option>

                            <?php foreach ($sabores as $res) : ?>

                                <option><?= $res->articulo_sabor ?></option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-3 form-group">

                        <label for="stock-alerta">Stock M&iacute;nimo <i class="required"></i></label>

                        <input type="tel" class="form-control" name="alerta" id="stock-alerta" value="<?= $articulo->stock_alerta ?: $alerta ?>" required>

                    </div>

                    <div class="col-md-9">

                        <?= $preciosBlock ?>

                    </div>

                    <?php if ( !$articulo->id_producto && false ) : ?>

                        <div class="col-md-12 form-group">

                            <label for="">Cantidades por local</label>

                            <div class="row">

                                <?php $cnt_deposito = (count($locales) + 1 - count(Local::$_puntosVenta)); ?>

                                <?php foreach ($locales as $key => $local) : ?>

                                    <?php

                                    if ( in_array($key, Local::$_puntosVenta) )

                                    {

                                        continue;

                                    }

                                    ?>

                                    <div class="col-md-<?= ($cnt_deposito < 5) ? round(12 / $cnt_deposito) : 3 ?>" style="text-align:left;background:none;border:none;">

                                        <input type="tel" name="stock[<?= $key ?>]" class="form-control" value="<?= $articulo->cantidad_array[$key] ?>" placeholder="<?= $local ?>">

                                        <p class="small"><?= $local ?></p>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                    <?php if ( false ): ?>

                        <div class="col-md-12 form-group">

                            <label for="texto">Descripci&oacute;n</label>

                            <textarea name="texto" id="texto" class="form-control"><?= $articulo->texto ?></textarea>

                        </div>

                    <?php endif; ?>

                    <div class="form-group col-md-9">

                        <label for="">Vencimiento:  </label>

                        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"> 

                    </div>


                    <div class="col-md-12 text-right">

                        <p class="small"><i class="required"></i> Datos requeridos</p>

                        <?php if ( ($id_producto = $articulo->id_producto) ) : ?>

                            <input type="hidden" name="id_producto" value="<?= $id_producto ?>"/>

                        <?php endif; ?>

                        <label for="paralelo" class="pull-left">

                            <input type="checkbox" id="paralelo" name="importado" <?= $articulo->id_sucursal ? "checked" : "" ?>> D&oacute;lar a $<?= $this->config['dolar_paralelo'] ?>

                        </label>

                        <button type="submit" class="btn btn-primary">Guardar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

                    </div>

                </form>

            </div>

        </div>

        <script type="text/javascript">

            (articuloForm = document.getElementById('producto-frm')).onsubmit = function (e) {

                e.preventDefault();

                theForm = this;

                submit_form(theForm, function (rsp) {

                    theForm.reset();

                    selectMarca.value = selectSabor.value = "";

                    $(selectSabor).selectar();

                    $(selectMarca).selectar();

                    if ( typeof get_rows === "function" )

                    {

                        get_rows();

                    }

                    delete rsp["notice"];

                    theForm.setAttribute("rel", JSON.stringify(rsp));

                    if ( document.querySelector('[name="id_producto"]') || parseInt(<?=$fromBuy?>) )

                    {

                        document.querySelector('[data-dismiss="modal"]').click();

                    }

                });

            };

            $('#precio').decimal('.');

            document.getElementById('id_categoria').value = "<?=$articulo->id_categoria?>";

            selectSabor = document.getElementById('sabor');

            selectSabor.value = "<?=$articulo->articulo_sabor?>";

            $(selectSabor).selectar();



            document.getElementById('auto').onclick = function () {

                codigo = document.getElementById('codigo');

                if ( this.checked )

                {

                    codigo.value = "0" + new Date().getTime();

                    return;

                }

                codigo.value = "";

            };



            selectMarca = document.getElementById('id_marca');

            selectMarca.setAttribute('minLength', "0");

            selectMarca.value = "<?=$articulo->id_marca?>";

            $(selectMarca).selectar();

        </script>

        <?php

        $this->setBlockModal(ob_get_clean());

    }



    public function exportar()
    {

        $this->setParams(array('articulos' => $this->reporte(), 'bc' => $_GET['bc']));

        $html = $this->loadView("admin/producto-exportar");

        ExportOpts::exportar($html, true);

        /*$pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', array(0, 5, 0, 5));

        $pdf->writeHTML($html);

        $pdf->Output();*/

        //die($html);

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

            $result[] = array('id' => $producto->id_producto, 'text' => "{$producto->id_producto} - " . $producto->nombre_producto);

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

        $id_producto = intval($_POST['id']);

        if ( ($articulo = Articulo::find($id_producto)) )

        {

            $articulo->hasAtributo()->delete();

            $articulo->borrado = 1;

            $articulo->save();

        }

        return;

    }

}