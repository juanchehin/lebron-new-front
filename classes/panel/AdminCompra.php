<?php



class AdminCompra extends AdminVenta

{

    private static $_thead = array(

        "#ID",

        "Fecha y Hora",

        "Local",

        "Usuario",

        "Artículo",

        "Cantidad.text-center",

        "Detalle.text-center"

    );



    private $por_cantidad = false;



    public function __construct()

    {

        parent::__construct();

        if( ($this->por_cantidad = isset($_GET['cnt'])) )

        {

            static::$_thead = array("Artículo", "Cantidad", "Marca", "Local");

        }

//        $this->setItemSeleccionado(MenuPanel::menuCompra);

    }



    /*public function index()

    {

        $this->setItemSeleccionado(MenuPanel::menuCompra);

    }*/



    public function historial($cup = null, $reporte = array())

    {

        //ini_set("display_errors","on");

        if ( $_GET['dt'] || $_POST || $reporte )

        {

            $params = $reporte ?: $_POST;

            $id_local = floatval($params['local']);

            $id_producto = floatval($params['cup']);

            $fecha = HDate::sqlDate($params['desde'] ?: date('01/m/Y'));

            $hasta = HDate::sqlDate($params['hasta'] ?: date('d/m/Y'));

            $queryRaw = "DATE(fecha_hora) >= '{$fecha}' AND DATE(`fecha_hora`) <= '{$hasta}'";

            $queryRaw .= " AND `atributo` NOT LIKE '%presupuesto'";

            #--

            if ( $id_producto )

            {

                $queryRaw .= " AND `id_producto` = '{$id_producto}'";

            }

            #--

            if ( $id_local )

            {

                $queryRaw .= " AND (`valor`='{$id_local}' OR `flag`='{$id_local}')";

            }

            #--

            if ( $operacion = trim($params['operacion']) )

            {

                $queryRaw .= " AND (`atributo` LIKE '{$operacion}%'";

                if ( $operacion == "Egreso" )

                {

                    // $queryRaw .= " OR `atributo` LIKE 'venta%'";

                }

                $queryRaw .= ")";

            }

            #--

            $sql = LineaVenta::whereRaw($queryRaw)->orderBy("id", "DESC")->orderBy("id_venta", "DESC");

            $dt = "";

            // Suma de las cantidades

            $ruta_origen = $_SERVER['HTTP_REFERER'];

            $posicion = strpos($ruta_origen, 'productos');

            if($posicion == false){
                $result1 = $sql->get();
                $suma_cant = 0;

                foreach($result1 as $x => $linea){                
                    $suma_cant = $suma_cant + $linea->cantidad;
                }

                $dt .= "<tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>{$suma_cant}</td>
                        <td>Suma</td>
                    </tr>";
                    
            }else{
                $result1 = $sql->get();
                $suma_cant = 0;

                foreach($result1 as $x => $linea){                
                    $suma_cant = $suma_cant + $linea->cantidad;
                }

                $dt .= "<tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>{$suma_cant}</td>
                        <td>Suma</td>
                    </tr>";
            }
            // Fin Suma de las cantidades

            

            $cantidad = $sql->count();            

            #--

            $result = $reporte ? $sql->get() : $sql->paginate($this->x_page);            


            foreach ($result as $x => $linea)

            {

                $item = $linea->hasVenta;

                //$local = Local::nombreLocal($item->id_sucursal);

                if ( !$linea->atributo )

                {

                    $flg = $linea->array_flag;

                    if ( !$tipo = $flg['tipo'] )

                    {

                        $tipo = ($linea->flag == "e") ? "Egreso" : "Ingreso";

                    }

                    if ( Local::esDeposito($item->id_sucursal ?: $flg['origen']) && ($item->id_sucursal != $linea->flag) )

                    {

                        $tipo = "Traspaso";

                        $linea->flag = ($flg['destino'] ?: Local::mitreNegocio);

                    }

                    //$linea->flag = json_encode(['origen' => $item->id_sucursal, 'destino' => $destino, 'tipo' => $tipo]);

                    $linea->atributo = $tipo;

                    $linea->valor = $item->id_sucursal;

                    $linea->save();

                }

                #--

                $arr = $linea->array_flg;

                $local = array();

                //$local[] = Local::nombreLocal($item->id_sucursal ?: $arr['origen']);

                $local[] = Local::nombreLocal($arr['origen']);

                $local[] = Local::nombreLocal($arr['destino']);

                $local = implode(" => ", array_filter($local));

                if ( !($usuario = $item->usuario) )

                {

                    $usuario = Usuario::find($linea->id_venta)->hasPersona->nombre_apellido;

                }

                #-- 17/01/2021

                $operacion = $linea->hasVenta;

                $detalle = array();

                $detalle[0] = $arr['tipo'];

                $data = array("&nbsp;");

                $detalle[1] = $operacion ? "#{$operacion->id_venta}" . (!$linea->operacion_traspaso ? "| {$operacion->nombre_cliente}" : "") : "";

                if ( $nota = $linea->nota )

                {

                    $detalle[2] = "<i class='small text-info'>Nota: {$nota}</i>";

                }

                #--

                
                if ( !$id_producto && ($result[$x - 1]->id_venta != $linea->id_venta) && $operacion )
                
                {
                    
                    
                    $data[0] = "#{$operacion->id_venta} | ";

                    $data[1] = $operacion->nombre_cliente;

                    $data[2] = "<span class='pull-right'>$ " . Facturacion::numberFormat($operacion->total) . "</span>";

                    $data[3] = " | <a href='!" . self::class . "/modalForm?n={$operacion->id_venta}' target='_blank'><i class='fa fa-file-pdf'></i></a>";

                    if ( $operacion->es_traspaso )

                    {

                        $data[1] = null;

                        $data[2] = null;

                    }

                    $dt .= "<tr>";

                    $dt .= "<td colspan='7' style='font-weight:600;background:#fcfce3;font-style: italic'>";

                    $dt .= implode("  ", array_filter($data));

                    $dt .= "</td>";

                    $dt .= "</tr>";

                }

                ///$dt .= "<tr>";

                

                if($item->original->tipo != 'venta_presupuesto mayorista')
                {

                    $dt .= "<tr id='" . ($id_linea = $linea->id) . "'>";

                    $dt .= "<td>{$id_linea}</td>";

                    $dt .= "<td class='text-center'>";

                    if ( static::$_adminCp && !$reporte && $this->controlPermiso(Permiso::permisoBorrar, false))
                    {

                        $dt .= "<a href='javascript:void(0)' onclick='dt_delete(this)'><i class='fa fa-trash-alt'></i></a>&nbsp;&nbsp;";

                    }


                    $dt .= "<a href='!AdminVenta/imprimirBarcode?n={$operacion->id_venta}' target='_blank'><i class='fa fa-barcode'></i></a>";                    

                    $dt .= $linea->fecha_registro;

                    $dt .= "</td>";

                    $dt .= "<td>{$local}</td>";

                    $dt .= "<td>{$usuario}</td>";

                    if ( !$id_producto )

                    {

                        //$detalle[1] = $detalle[2] = null;

                        $detalle[1] = null;

                        $dt .= "<td>{$linea->producto}</td>";

                    }

                    $dt .= "<td class='text-center'>{$linea->cantidad} ";

                    $arr_stock = explode("&", $linea->stock);

                    foreach ($arr_stock as $key => $value)

                    {

                        if ( (!$key && !$linea->operacion_ingreso) && $value )

                        {

                            $value = "S";

                            continue;

                        }

                        $dt .= "[{$value}]";

                    }

                    //if ( is_numeric($linea->stock) && (!$linea->stock || $linea->operacion_ingreso) )

                    {



                    }

                    $dt .= "</td>";

                    $dt .= "<td class='text-center'>" . implode("<br/>", array_filter($detalle)) . "</td>";

                    $dt .= "</tr>";

                }

            }

            #--

            if ( !$reporte && self::isXhrRequest() )

            {

                $dt .= "<tr class='not'><td colspan='6' data-count='{$cantidad}'>{$this->replaceLinks($result)}</td></tr>";

                if ( $cantidad < 401 )

                {

                    $params['pdf'] = 1;

                }

                $dt .= "<script>document.getElementById('aa-export').href = '!" . self::class . "/listado?" . http_build_query(array_filter($params)) . "';</script>";

                die($dt);

            }

            return $dt;

        }

        #--

        $this->setItemSeleccionado(MenuPanel::menuCompra);

        $titulo = "Historial de Movimientos";

        if ( $cup && ($item = Articulo::find($cup)) )

        {

            $titulo = "Movimientos de {$cup} - {$item->nombre}";

        }

        $this->setPageTitle($titulo);

        $bbtn = "<a href='" . self::sysUrl . "/articulos' class='btn btn-default'>Atras</a>";

        if ( !$this->por_cantidad )

        {

            $bbtn .= "<a href='" . self::sysUrl . "/log?cnt' style='float:right' class='btn btn-warning'>Cantidades</a>";

        }

        $this->setBotonNuevo(null, null, $bbtn);

        $options = $this->_selectLocal();

        $options .= "<select id='operacion' class='form-control'>";

        if ( !$this->por_cantidad )

        {

            $options .= "<option value=''>Operaci&oacute;n</option>";

            foreach (["egreso", Venta::tpVenta, Venta::tpIngreso, Venta::tpTraspaso] as $operacion)

            {

                $options .= "<option>" . ucfirst($operacion) . "</option>";

            }

        }

        else

        {

            $options .= "<option value=''>Marca</option>";

            foreach (Categoria::select('id_item', 'nombre')->whereRaw("!`borrado` AND `tipo`='". Categoria::tipoMarca . "'")->orderBy('nombre')->get() as $item )

            {

                $options .= "<option value='{$item->id_item}'>{$item->titulo}</option>";

            }

        }

        $options .= "</select>";

        $options .= "<span class='pull-right'><a target='_blank' id='aa-export' href='javascript:void()'>Reporte</a></span>";

        #--

        $table = new HDataTable();

        $table->setDataSource(self::class . "/" . ($this->por_cantidad ? "dsCantidades" : "historial"));

        if ( $cup )

        {

            unset(static::$_thead[3]);

            static::$_thead[4] = "Usuario";

            $table->setKeys('cup', $cup);

        }

        $table->setColumns(static::$_thead);

        $table->setHideSearchBox();

        $table->setDateRangeConf(true, $this->por_cantidad);

        $table->setHtmlControl($options);

        $this->setParams("_table", $table->drawTable());

        $this->setBody("articulo-index");

    }



    public function dsCantidades($reporte = array())

    {

        $params = $_POST ?: $reporte;

        $local = floatval($params['local']);

        $marca = floatval($params['operacion']);

        //$periodo = trim($params['anio'] ?: date('Y')) . "-" . trim($params['mes'] ?: date('m'));

        $desde = HDate::sqlDate($params['desde'] ?: date('01/m/Y'));

        $hasta = HDate::sqlDate($params['hasta'] ?: date('d/m/Y'));

        $tipo = trim($params['tipo']) ?: "publico";

        $group = trim($params['group']) ?: "id_producto";

        #--

        $queryRaw = "DATE(fecha_hora) >= '{$desde}' AND DATE(`fecha_hora`) <= '{$hasta}'";

        $queryRaw .= " AND `atributo` LIKE 'venta_{$tipo}%' AND (subtotal / cantidad)/" . $this->config['dolar_paralelo'] . " > 2";

        #--

        //die($queryRaw);

        if ( $local )

        {

            $queryRaw .= " AND `valor`='{$local}'";

        }

        #--

        $sql = LineaVenta::selectRaw("SUM(cantidad) AS 'qty', id_producto, valor")->whereRaw($queryRaw)->whereHas("hasArticulo", function($sql) use ($marca){

            $query = "!borrado";

            if ( $marca )

            {

                $query .= " AND id_marca='{$marca}'";

            }

            $sql->whereRaw($query)->orderBy("id_categoria");

        })->groupBy($group)->orderByRaw("SUM(cantidad) DESC");

        $cantidad = $sql->count();

        #--

        $result = $reporte ? $sql->get() : $sql->paginate(100);

        $rows = null;

        foreach ($result as $res)

        {

            $articulo = $res->hasArticulo;

            $nombre = "{$articulo->id_producto} - " . $articulo->nombre;

            if ( !$articulo->no_sabor ) 

            {

                $nombre .= " (<b>{$articulo->articulo_sabor}</b>)";

            }

            #--

            if ( $articulo->peso )

            {

                $nombre .= ". " . mb_strtoupper($articulo->peso);

            }

            #--

            $rows .= "<tr id=''>";

            $rows .= "<td>{$nombre}</td>";

            $rows .= "<td style='text-align:center'>{$res->qty}</td>";

            $rows .= "<td>{$articulo->marca}</td>";

            $rows .= "<td>" . Local::$_LOCALES[$res->valor] . "</td>";

            $rows .= "</tr>";

        }

        #--

        if ( !$reporte )

        {

            $rows .= "<tr class='not' data-count='{$cantidad}'><td colspan='" . sizeof(static::$_thead) . "'>{$this->replaceLinks($result->links())}</td></tr>";

            if ( $cantidad < 401 )

            {

                $params['pdf'] = 1;

            }

            $rows .= "<script>document.getElementById('aa-export').href='!" . self::class . "/listado?cnt&" . http_build_query(array_filter($params)) . "';</script>";

            die($rows);

        }

        return $rows;

    }



    public function form($param)

    {

        $this->setItemSeleccionado(MenuPanel::menuStock, $param);

        $this->controlPermiso($param);

        $this->ventaForm();

    }



    public function listado()

    {

        // die($this->historial(null, $_GET));

        $this->exportando(self::$_thead, ($this->por_cantidad ? $this->dsCantidades($_GET) : $this->historial(null, $_GET)), false, true, false);

    }



    public function eliminar()

    {

        $id_linea = floatval($_POST['id']);

        if ( $linea = LineaVenta::find($id_linea) )

        {

            if ( $articulo = $linea->hasArticulo )

            {

                $stock = $articulo->cantidad_array;

                $update = false;

                $math = $linea->operacion_ingreso ? "-" : "+";

                if ( ($id_local = ($linea->valor ?: $linea->hasVenta->id_sucursal)) )

                {

                    eval("\$stock[{$id_local}] {$math}= \$linea->cantidad;");

                    $update = true;

                }

                #--

                if ( $destino = intval($linea->flag) )

                {

                    $stock[$destino] -= $linea->cantidad;

                }

                #--

                if ( $update )

                {

                    $articulo->cantidad_json = $stock;

                    $articulo->save();

                }

            }

            $linea->delete();

            HArray::jsonSuccess();

        }

        exit;

    }

}