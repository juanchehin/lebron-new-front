<?php

//ini_set("display_errors", "On");


class AdminVenta extends AdminMain

{

    const SES_LV = "linea_venta";

    private $lineas;

    private $file_name;

    private $descuento;

    private static $_thead = array("#Nro", "Fecha y Hora", "Tipo Vta", "Comprador", "Usuario", "Local", "Condición", "Total", "&nbsp;");



    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuVentas);

        $this->file_name = "media/meta/" . str_pad($this->admin_user->id_usuario, 8, "0", 0) . ".lv";

        $this->descuento = ($this->config['rebaja_por_mayor'] / 100);

    }



    public function index($online = false)

    {

        if ( !$this->controlPermiso(Permiso::permisoVer, false) )

        {

            Router::redirect(self::PANEL_URI . "/ventas/nuevo");

        }

        $this->setPageTitle("Ventas " . ($online ? "Online" : "Realizadas"));

        $this->setBotonNuevo("Registrar venta");

        #--

        switch ($this->admin_user->id_local) {
            case 7:
                $filter = $this->_filtroBsAs();
                break;
            case 12:
                $filter = $this->_filtro25();
                break;
            default:
                $filter = $this->_selectLocal();
                break;
        }


        $filter .= "<select id='hora' class='form-control'>";

        $filter .= "<option value=''>Horario</option>";

        foreach (['00&13' => "Mañana", '14&23' => "Tarde"] as $key => $label)

        {

            $filter .= "<option value='{$key}'>{$label}</option>";

        }

        $filter .= "</select>";

        $filter .= "<select id='tipo_vta' class='form-control'>";

        $filter .= "<option value=''>Tipo de Vta.</option>";

        foreach (["Mayorista", "Publico"] as $item)

        {

            $filter .= "<option>{$item}</option>";

        }

        $filter .= "</select>";

        $filter .= "<select class='form-control' id='id_cuenta'>";

        $filter .= "<option value=''>Forma de Pago</option>";

        foreach (Concepto::cuentasPago() as $cuenta)

        {

            $filter .= "<option value='{$cuenta->id_concepto}'>{$cuenta->nombre}</option>";

        }

        $filter .= "</select>";

        // ventas pendientes

        $filter .= "<select class='form-control' id='ventas_pendientes'>";

            $filter .= "<option value='realizada' selected>Realizadas</option>";

            $filter .= "<option value='pendiente'>Pendientes</option>";

        $filter .= "</select>";

        // ventas pendientes

        $filter .= "<div style='float:right;margin:-5px 0'>";

        $filter .= "<a href='" . self::sysUrl . "/estadistica/venta'>Estad&iacute;sticas</a>";

        $filter .= "<br/><a href='javascript:void(0)' id='aa-exportar-xls'>Exportar (.xls)</a>";

        $filter .= "</div>";

        $filter .= "</br>";

        $filter .= "<input type='text' id='search_box' name='dt_search' class='form-control' placeholder='Buscar nro remito' autocomplete='off'>";

        $filter .= "<input type='text' id='dni' name='dni' class='form-control' placeholder='Busqueda por dni' autocomplete='off'>";

        $filter .= "<input type='text' id='search-cliente' name='search-cliente' class='form-control' placeholder='Busqueda por cliente' autocomplete='off'>";

        #--

        $tabla = new HDataTable();

        $tabla->setHideSearchBox();

        if ( $online )

        {

            $filter = null;

            //$tabla->setHideDateRange();

            //static::$_thead[2] = "Cliente";

            unset(static::$_thead[3]);

            $tabla->setKeys('online', 1);

            $this->setParams('online', 1);

        }

        $tabla->setColumns(self::$_thead);

        $tabla->setHtmlControl($filter);

        $tabla->setRows($this->getRows());

        $this->setParams('data_table', $tabla->drawTable());

        $this->setBody("ventas-index");

    }



    public function ventaOnline()

    {

        /* $lineas = LineaVenta::where('id',">",19052)->orderBy('id')->get();

         foreach ($lineas as $linea)

         {

             $nuevo = ($linea->id - 169670);

             echo $linea->id . " => {$nuevo}<br/>";

             $linea->id = $nuevo;

             $linea->save();

         }*/

        $this->index(true);

    }

    private function _selectConcepto()
    {
        $control = "<select id='id_concepto' name='id_concepto' class='form-control' required>";

        // $control .= "<option value=''>" . ucfirst($tipo) . "</option>";

        foreach (Concepto::selectRaw("id_concepto AS id, `concepto` AS label")->whereRaw("`categoria`='disponibilidad' AND `visible`='1'")->get() as $Concepto)

        {

            $control .= "<option value='{$Concepto->id}'>{$Concepto->label}</option>";

        }

        $control .= "</select>";

        return $control;

    }

    private function _selectTipoVenta()
    {
        $control = "<select id='id_tipo_venta' name='id_tipo_venta' class='form-control' required>";

        $control .= "<option value='0'>venta_publico</option>";

        $control .= "<option value='1'>venta_mayorista</option>";

        $control .= "<option value='2'>venta_presupuesto</option>";

        $control .= "<option value='3'>compra</option>";

        $control .= "</select>";

        return $control;

    }

    private function _filtroBsAs()
    {
        $selectLocal = "<select id='local' name='7' class='form-control'>";
        $selectLocal .= "<option value=''>Local bs as</option>";
        $selectLocal .= "</select>";

        return $selectLocal;
    }

    private function _filtro25()
    {
        $selectLocal = "<select id='local' name='12' class='form-control'>";
        $selectLocal .= "<option value=''>Local 25 de mayo</option>";
        $selectLocal .= "</select>";

        return $selectLocal;

    }



    // Egresos - Registros
    public function getRows($exportar = false)

    {

        if($this->admin_user->id_local == 7 || $this->admin_user->id_local == 12)
        {
            $params['local'] = $this->admin_user->id_local;
        }else{
            $params['local'] = intval($_REQUEST['local']);
        }


        $params['id_cuenta'] = intval($_REQUEST['id_cuenta']);

        $params['desde'] = HDate::sqlDate($_REQUEST['desde']) ?: date('Y-m-01');//"1900-01-01";

        $params['hasta'] = HDate::sqlDate($_REQUEST['hasta']) ?: date('Y-m-d');//date('Y-12-31');

        list($params['h_desde'], $params['h_hasta']) = explode("&", trim($_REQUEST['hora'] ?: "00&23"));

        $params['tipo_vta'] = trim($_REQUEST['tipo_vta']);

        $filter_estado = trim($_REQUEST['ventas_pendientes']);

        $params['id_venta'] = $_POST['search_box'];    // Nro remito

        $id_venta = $_POST['search_box'];    // Nro remito

        $dni = $_POST['dni'];    // 

        $cliente = $_POST['search-cliente'];

        #--

        extract($params);

        $where['visible'] = 1;

        $where['external_id'] = 0;

        // $where['estado'] = $filter_estado;



        $tipo_op = Venta::tpVenta;

        if ( $local ) $where['id_sucursal'] = $local;

        if($exportar)
        {
            $desde = date('Y-m-d', strtotime($desde));
            $hasta = date('Y-m-d', strtotime($hasta));
        }

       

        #--

        if($id_venta)
        {
            $wRaw = "id_venta = '{$id_venta}' ";
        }else
        {
            
            if($filter_estado != 'realizada')
            {
                
                $wRaw = "`tipo` LIKE '{$tipo_op}_{$tipo_vta}%' AND ";

                $wRaw .= "((DATE(fecha_hora) BETWEEN '{$desde}' AND '{$hasta}') AND ";

                $wRaw .= "HOUR(fecha_hora) BETWEEN '{$h_desde}' AND '{$h_hasta}') AND ";

                $wRaw .= "`estado` LIKE '{$filter_estado}' ";
            }else
            {
                $wRaw = "`tipo` LIKE '{$tipo_op}_{$tipo_vta}%' AND ";

                $wRaw .= "((DATE(fecha_hora) BETWEEN '{$desde}' AND '{$hasta}') AND ";

                $wRaw .= "HOUR(fecha_hora) BETWEEN '{$h_desde}' AND '{$h_hasta}')";
            }

        }

         #--

        //  echo $wRaw;


         if($dni)
         {
            $query1 = Cliente::whereRaw("!`borrado` AND (`dni` = '{$dni}')");
            $registros = $query1->paginate($this->x_page);

            foreach ($registros as $reg)
            {
                $id_persona = $reg->id;
            }
         }

         



        // echo $wRaw;

        if ( isset($_REQUEST['online']) || ($pendientes = isset($_GET['count'])) )

        {

            unset($where['external_id'], $where['id_sucursal']);

            $wRaw .= " AND `external_id`";

            if ( $pendientes )

            {

                $wRaw .= " AND `estado`='" . Venta::estadoEspera . "'";

            }

        }

        #--

        $query = Venta::where($where)->whereRaw($wRaw);

        if ( $id_cuenta )

        {

            $query->whereHas('hasPago', function ($qq) use ($id_cuenta) {

                $qq->where('id_cuenta', $id_cuenta);

            });

        }

        $query->orderBy('estado')->orderBy("id_venta", "DESC");

        #--

        $count = $query->count();

        if ( $pendientes )

        {

            echo $count;

            die;

        }

        $arr_total = array();

        $allRows = $query->get();

        $total = 0;

        $excluidas = [Concepto::cuentaRegalo, Concepto::cuentaCorriente];

        if ( $local == Local::mitreNegocio && $tipo_vta == "mayorista" )

        {

            $excluidas[] = Concepto::cuentaCaja;

        }

        foreach ($allRows as $row)

        {

            foreach ($row->hasPago as $item)

            {

                $pagos[$row->id_venta][$item->id_cuenta] = $item->importe;

                $arr_total[Concepto::get($item->id_cuenta)] += floatval($item->importe);

                if ( !in_array($item->id_cuenta, $excluidas) )

                {

                    $total += floatval($item->importe);

                }

                if ( $row->estado == 'pendiente' )

                {
    
                    $total_pendientes += floatval($item->importe);
    
                }

            }

        }

        //$arr_total['x'] = $total;

        //HArray::varDump($arr_total);

        $arr_total['PENDIENTES'] += floatval($total_pendientes);

        if ( $id_cuenta )

        {

            $total = $arr_total[Concepto::get($id_cuenta)];

        }

        $ventas = $exportar ? $allRows : $query->paginate($this->x_page);

        $colspan = sizeof(static::$_thead) - 2;

        $rows = null;

        // ***** Armado de las filas *****

        foreach ($ventas as $index => $venta)

        {

            if($cliente)
            {

                if (stripos($venta->nombre_cliente, $cliente) !== false) {

                    // $where['cliente'] = $cliente;

                    $clase = null;

                    $periodo = substr($venta->fecha_hora, 0, 7);

                    // Primera fila
                    if ( !$index )

                    {

                        $rows .= "<tr>";

                        $rows .= "<td colspan='{$colspan}' align='right'>";

                        $rows .= implode("<br/>", array_keys($arr_total));

                        $rows .= "</td>";
                        
                        $rows .= "<td align='right'>";

                        foreach (array_values($arr_total) as $monto)

                        {

                            $rows .= HFunctions::formatPrice($monto) . "<br/>";

                        }

                        $rows .= "</td>";

                        if ( !$exportar )

                        {

                            $rows .= "<td>&nbsp;</td>";

                        }

                        $rows .= "</tr>";

                    }

                    if ( $venta->external_id )

                    {

                        $clase = "tr-{$venta->estado}";

                    }

                    // 'Rojo' si esta pendiente
                    $clase = ($venta->estado == 'pendiente') ? 'background-color:#f46464' : '';

                    $rows .= "<tr style='" . $clase . "' id='" . ($id_venta = $venta->id_venta) . "' class='{$clase}'>";

                    $rows .= "<td>{$id_venta}</td>";

                    $rows .= "<td>{$venta->fecha}</td>";
                    $rows .= "<td>" . strtoupper(str_ireplace("_", " ", $venta->tipo));

                    if ( $this->controlPermiso(Permiso::permisoBorrar, false) )

                    {

                        $rows .= "<a href='javascript:void(0)' rel='1' onclick='get_form_tipo_venta(this)'><i class='fa fa-pencil-alt'></i></a>&nbsp;";

                    }
                    $rows .= "</td>";

                    $persona = $venta->nombre_cliente;  // Nombre comprador

                    if ( $venta->external_id && ($venta->id_usuario < 0) )

                    {

                        $persona .= " (Con envío)";

                    }

                    $rows .= "<td>" . ($persona ?: '-') . "</td>";

                    if ( !$venta->external_id )

                    {

                        $rows .= "<td>{$venta->usuario}</td>";

                    }

                    $rows .= "<td>" . Local::nombreLocal($venta->id_sucursal) . "</td>";

                    $rows .= "<td>";

                    foreach ($pagos[$id_venta] as $id_cuenta => $importe)

                    {

                        //$arr_total[$pago->id_cuenta] += floatval($pago->importe);

                        $rows .= "<div style='font-size:12px;font-weight:600'>";

                        $rows .= Concepto::get($id_cuenta) . "<i class='pull-right'>$ " . Facturacion::numberFormat($importe) . "</i>";

                        $rows .= "</div>";

                    }

                    $rows .= "</td>";

                    $rows .= "<td class='amount'>$ " . HFunctions::formatPrice($venta->total) . "</td>";

                    if ( !$exportar ) :

                        $rows .= "<td class='text-center'>";

                        $rows .= "<a href='!" . self::class . "/modalForm?n={$id_venta}' target='_blank'><i class='fa fa-file-pdf'></i></a>";

                        if($venta->comprobante)
                        {
                            $rows .=  "<a href='" . $_SERVER["HTTP_ORIGIN"] . "/media/uploads/comprobantes/" . "{$venta->comprobante}' target='_blank'><i class='fa fa-file'></i></a>";
                        }


                        if ( $pendiente = ($venta->estado != "realizada") )

                        {

                            $estado = "realizada";

                            if ( $venta->estado == Venta::estadoEspera )

                            {

                                $estado = Venta::estadoEnviado;

                            }

                            $json = array('id' => $id_venta, 'attr' => "estado", 'estado' => $estado);

                            $rows .= "<a href='javascript:void(0)' onclick='set_estado(" . json_encode($json) . ")'><i class='fa fa-check-circle'></i></a>";

                        }

                        #--

                        //if ( $this->controlPermiso(Permiso::permisoBorrar, false) && ($pendiente || $periodo >= date('Y-m')) )

                        if ( $this->controlPermiso(Permiso::permisoBorrar, false) )

                        {

                            $rows .= "<a href='javascript:void(0)' title='Eliminar' onclick='dt_delete(this)'><i class='fa fa-trash-alt text-danger'></i></a>";
                            $rows .= "<a href='javascript:void(0)' rel='1' onclick='get_form_venta(this)'><i class='fa fa-pencil-alt'></i></a>&nbsp;";

                        }

                        $rows .= "</td>";

                    endif;

                    $rows .= "</tr>";
                }

            }
            else{

                    $clase = null;

                    $periodo = substr($venta->fecha_hora, 0, 7);

                    // Primera fila
                    if ( !$index )

                    {

                        $rows .= "<tr>";

                        $rows .= "<td colspan='{$colspan}' align='right'>";

                        $rows .= implode("<br/>", array_keys($arr_total));

                        $rows .= "</td>";
                        
                        $rows .= "<td align='right'>";

                        foreach (array_values($arr_total) as $monto)

                        {

                            $rows .= HFunctions::formatPrice($monto) . "<br/>";

                        }

                        $rows .= "</td>";

                        if ( !$exportar )

                        {

                            $rows .= "<td>&nbsp;</td>";

                        }

                        $rows .= "</tr>";

                    }

                    if ( $venta->external_id )

                    {

                        $clase = "tr-{$venta->estado}";

                    }

                    // 'Rojo' si esta pendiente
                    $clase = ($venta->estado == 'pendiente') ? 'background-color:#f46464' : '';

                    $rows .= "<tr style='" . $clase . "' id='" . ($id_venta = $venta->id_venta) . "' class='{$clase}'>";

                    $rows .= "<td>{$id_venta}</td>";

                    $rows .= "<td>{$venta->fecha}</td>";
                    $rows .= "<td>" . strtoupper(str_ireplace("_", " ", $venta->tipo));

                    if ( $this->controlPermiso(Permiso::permisoBorrar, false) )

                    {

                        $rows .= "<a href='javascript:void(0)' rel='1' onclick='get_form_tipo_venta(this)'><i class='fa fa-pencil-alt'></i></a>&nbsp;";

                    }
                    $rows .= "</td>";

                    $persona = $venta->nombre_cliente;  // Nombre comprador

                    if ( $venta->external_id && ($venta->id_usuario < 0) )

                    {

                        $persona .= " (Con envío)";

                    }

                    $rows .= "<td>" . ($persona ?: '-') . "</td>";

                    if ( !$venta->external_id )

                    {

                        $rows .= "<td>{$venta->usuario}</td>";

                    }

                    $rows .= "<td>" . Local::nombreLocal($venta->id_sucursal) . "</td>";

                    $rows .= "<td>";

                    foreach ($pagos[$id_venta] as $id_cuenta => $importe)

                    {

                        //$arr_total[$pago->id_cuenta] += floatval($pago->importe);

                        $rows .= "<div style='font-size:12px;font-weight:600'>";

                        $rows .= Concepto::get($id_cuenta) . "<i class='pull-right'>$ " . Facturacion::numberFormat($importe) . "</i>";

                        $rows .= "</div>";

                    }

                    $rows .= "</td>";

                    $rows .= "<td class='amount'>$ " . HFunctions::formatPrice($venta->total) . "</td>";

                    if ( !$exportar ) :

                        $rows .= "<td class='text-center'>";

                        $rows .= "<a href='!" . self::class . "/modalForm?n={$id_venta}' target='_blank'><i class='fa fa-file-pdf'></i></a>";

                        if($venta->comprobante)
                        {
                            $rows .=  "<a href='" . $_SERVER["HTTP_ORIGIN"] . "/media/uploads/comprobantes/" . "{$venta->comprobante}' target='_blank'><i class='fa fa-file'></i></a>";
                        }


                        if ( $pendiente = ($venta->estado != "realizada") )

                        {

                            $estado = "realizada";

                            if ( $venta->estado == Venta::estadoEspera )

                            {

                                $estado = Venta::estadoEnviado;

                            }

                            $json = array('id' => $id_venta, 'attr' => "estado", 'estado' => $estado);

                            $rows .= "<a href='javascript:void(0)' onclick='set_estado(" . json_encode($json) . ")'><i class='fa fa-check-circle'></i></a>";

                        }

                        #--

                        //if ( $this->controlPermiso(Permiso::permisoBorrar, false) && ($pendiente || $periodo >= date('Y-m')) )

                        if ( $this->controlPermiso(Permiso::permisoBorrar, false) )

                        {

                            $rows .= "<a href='javascript:void(0)' title='Eliminar' onclick='dt_delete(this)'><i class='fa fa-trash-alt text-danger'></i></a>";
                            $rows .= "<a href='javascript:void(0)' rel='1' onclick='get_form_venta(this)'><i class='fa fa-pencil-alt'></i></a>&nbsp;";

                        }

                        $rows .= "</td>";

                    endif;

                    $rows .= "</tr>";
            }

            #--

        }

        #--

        if ( !$exportar && self::isXhrRequest() )

        {

            $rows .= "<tr class='not' data-count='{$count}'><td colspan='15' class='not'>{$this->replaceLinks($ventas->links())}</td></tr>";

            $rows .= "<script>";

            $rows .= "document.getElementById('aa-exportar-xls').href='!" . self::class . "/exportarVentas?" . http_build_query(array_filter($params)) . "';";

            $rows .= "document.getElementById('total-ventas').innerHTML = \"$ " . HFunctions::formatPrice($total) . "\";";

            $rows .= "</script>";

            die($rows);

        }

         // ***** Fin Armado de las filas *****


        return $rows;

    }



    public function ventaForm($id = null)

    {

        $accion = $this->modulo;

        if ( $this->current_item == MenuPanel::menuVentas )

        {

            $this->controlPermiso(Permiso::permisoCrear);

        }

        $this->setPageTitle("Registrar " . ucfirst($accion ?: "venta"));

        #--

        $articulos = Articulo::whereRaw("!`borrado` OR `audit`")->orderBy('producto')->get();

        if ( $accion != Venta::tpTraspaso )

        {

            $model = ($accion == Venta::tpIngreso) ? "Proveedor" : "Cliente";

            $clientes = $model::whereRaw("(!`borrado` OR `id`=4) AND `nombre`!=''")->orderBy('apellido')->get();

            $params['clientes'] = $clientes;

            $params['persona'] = $model;

        }

        #--

        $operacion = $this->modulo;

        $frs_line = array_shift($this->_getLineas());

        $pagos = array();

        if ( $id && ($venta = Venta::find($id)) )

        {

            $params['venta'] = $venta;

            //$params['pagos'] = $venta->hasPago()->pluck('importe', 'id_cuenta')->toArray();

            foreach ($venta->hasPago()->select('id_cuenta', 'importe')->get() as $item)

            {

                $importe = floatval($item->importe);

                $pagos[$item->id_cuenta] += $importe;

            }

            if ( $venta->es_presupuesto )

            {

                $venta->hasPago()->delete();

            }

            $operacion = preg_replace("#_\w+$#", "", $venta->tipo);

            if ( $frs_line['op_nro'] != $id )

            {

                $this->_setLineas(null, true);

                foreach ($venta->hasLineaVenta as $i => $linea)

                {

                    $lineas[time() + $i] = $this->agregarLinea($linea, $operacion);

                    $this->_setLineas($lineas, true);

                }

            }

        }

        else

        {

            if ( preg_match("#{$frs_line['op']}#", $operacion) && $frs_line['id_linea'] )

            {

                $this->_setLineas(null, true);

            }

        }

        $table = new HDataTable();

        $table->setColumns(["Cantidad", "Articulo", "Subtotal"]);

        $table->setFixedHead();

        $table->setHideSearchBox();

        $table->setHideDateRange();

        $table->setDisableFunciones();

        $table->setRows($this->_lineaVenta(null, $operacion));

        $params['selectLocal'] = $this->_selectLocal(($accion == MenuPanel::menuVentas) ? 1 : null);

        //'selectLocal' => $this->_selectLocal(($accion != MenuPanel::menuVentas) ? 2 : 1),

        $params['operacion'] = $accion;

        $params['linea_venta'] = $table->drawTable();

        $params['articulos'] = $articulos;

        $params['minDate'] = HDate::modifyDate(date('Y-m-d'), '-3 day', 'd/m/Y');

        $params['tipos_venta'] = Venta::$_tipoVenta;

        $params['cuentas'] = Concepto::cuentasPago();

        $params['total_op'] = $venta->total;

        $params['pagoControl'] = PagoControl::pagoForm($pagos);

        #--

        $this->setParams($params);

        $this->setBody("venta-form");

    }



    public function historialArticulos($print = false)

    {

        /*$prm['id_local'] = floatval($_POST['id_local']);get

        $prm['tipo'] = trim($_POST['operacion']);

        if ( $tpo_vta = trim($_POST['tv']) )

        {

            $prm['tipo'] .= "_{$tpo_vta}";

        }

        $prm['fecha'] = HDate::sqlDate($_POST['fecha'] ?: date('d/m/Y'));

        list($prm['h_dsd'], $prm['h_hst']) = explode("&", $_POST['horario'] ?: "00&23");

        //HArray::varDump($prm);

        $print ? ($prm = $this->reporte()) : $this->reporte($prm);

        $prm['exc'] = floatval($_POST['exc']);

        extract($prm);



        $arr_total = array();

        #--

        if ( $tipo === Venta::tpTraspaso )

        {

            $where = "`valor` = '{$id_local}' AND `atributo` LIKE '{$tipo}' ";

            $where .= "AND DATE_FORMAT(`fecha_hora`,'%Y-%m-%d %H') BETWEEN '{$fecha} {$h_dsd}' AND '{$fecha} {$h_hst}'";

            $query = LineaVenta::whereRaw($where)->orderBy("id", "DESC");

        }

        else

        {

            $query = LineaVenta::whereHas("hasVenta", function ($sql) use ($prm) {

                extract($prm);

                $where = "`id_sucursal` = '{$id_local}' AND (`tipo` LIKE '{$tipo}') ";

                $where .= "AND `id_venta` <> '{$exc}' ";

                $where .= "AND DATE_FORMAT(`fecha_hora`,'%Y-%m-%d %H') BETWEEN '{$fecha} {$h_dsd}' AND '{$fecha} {$h_hst}'";

                $sql->whereRaw($where);

            })->orderBy('id_venta', "DESC");

        }*/

        $prm['id_local'] = floatval($_POST['id_local']);

        $prm['tipo'] = trim($_POST['operacion']);

        $prm['search'] = trim($_POST['search']);

        $prm['whereUsuario'] = "`id_usuario` " . (intval($_POST['allUsers']) ? "> 0" : "= '{$this->admin_user->id_usuario}'");

        if ( $tpo_vta = trim($_POST['tv']) )

        {

            $prm['tipo'] .= "_{$tpo_vta}";

        }

        $prm['fecha'] = HDate::sqlDate($_POST['fecha'] ?: date('d/m/Y'));

        list($prm['h_dsd'], $prm['h_hst']) = explode("&", $_POST['horario'] ?: "00&23");

        //HArray::varDump($prm);

        $print ? ($prm = $this->reporte()) : $this->reporte($prm);

        $prm['exc'] = floatval($_POST['exc']);

        extract($prm);



        $arr_total = array();

        #--

        if ( $tipo === Venta::tpTraspaso )

        {

            $where = "`valor` = '{$id_local}' AND `atributo` LIKE '{$tipo}' ";

            $where .= "AND DATE_FORMAT(`fecha_hora`,'%Y-%m-%d %H') BETWEEN '{$fecha} {$h_dsd}' AND '{$fecha} {$h_hst}'";

            $query = LineaVenta::whereRaw($where)->orderBy("id", "DESC");

        }

        else

        {
          
            $query = LineaVenta::whereHas("hasVenta", function ($sql) use ($prm) {

                extract($prm);

                if($tipo == 'venta_presupuesto publico' || $tipo == 'venta_presupuesto')
                {
                    $tipo = 'venta_presupuesto';
                }
                
                $where[] = "`tipo` LIKE '{$tipo}'";
    
                $where[] .= "`id_sucursal` = '{$id_local}'";             

                $where[] = $whereUsuario;

                $where[] = "`id_venta` <> {$exc}";

                //$where[] = "`cliente` LIKE '%{$search}%'";

                $where[] = "DATE_FORMAT(`fecha_hora`,'%Y-%m-%d %H') BETWEEN '{$fecha} {$h_dsd}' AND '{$fecha} {$h_hst}'";

                #--

                $sql->whereRaw(implode(" AND ", $where))->where(function ($sql1) use ($where, $search) {

                    //unset($where[4]);

                    $sql1->where("cliente", "LIKE", "%{$search}%")->orWhereHas("hasCliente", function ($sql2) use ($search) {

                        $sql2->whereRaw("(`dni` LIKE '{$search}%' OR `nombre` LIKE '%{$search}%' OR `apellido` LIKE '%{$search}%')");

                    });

                });

            })->orderBy('id_venta', "DESC");

        }

        #--

        if ( ($num_rows = $query->count()) && preg_match("#" . Venta::tpVenta . "#i", $tipo) )

        {

            $ids_venta = implode("','", $query->pluck('id_venta')->toArray());

            $totales = Movimiento::selectRaw("id_cuenta, SUM(importe) AS 'total'")

                //->whereRaw($whr = "`id_cuenta` AND (`id_operacion` IN ('{$ids_venta}') OR `accion` IN ('{$ids_venta}'))")

                ->whereRaw($whr = "`id_cuenta` AND `id_operacion` IN ('{$ids_venta}')")

                ->groupBy("id_cuenta")->get();

            foreach ($totales as $total)

            {

                $arr_total[] = "<b>" . Concepto::get($total->id_cuenta) . "</b>: $ " . HFunctions::formatPrice($total->total);

            }

        }

        #-- 04/10/2020

        $control = Incidencia::whereRaw("`operacion`='" . Incidencia::controlVenta . "' AND `valor`='{$id_local}' AND `id_usuario` = '{$this->admin_user->id_usuario}' AND DATE(`fecha_hora`)='{$fecha}'")->first();

        $blocked = intval($control->estado);

        if ( !$control->id && $num_rows )

        {

            $dias = HDate::dateDiff($fecha, date('Y-m-d'));

            if ( $dias > 6 )

            {

                $blocked = 1;

            }

            else

            {

                $control = Incidencia::crear(Incidencia::controlVenta, $this->admin_user->id_usuario, $fecha, $id_local);

                $control->id_operacion = strtotime($fecha);

                $control->save();

            }

        }

        #--

        $lineas = $print ? $query->get() : $query->paginate(12);

        $rows = "";

        //if ( $print )

        {

            $head = $lineas[0]->atributo . ": " . Local::nombreLocal($lineas[0]->valor) . " => " . Local::nombreLocal($lineas[0]->flag);

            //$rows .= "<h3 style='position: absolute'>" . implode(" => ", array_filter($head)) . "</h3>";

        }

        foreach ($lineas as $i => $linea)

        {

            if ( ($lineas[$i - 1]->id_venta != $linea->id_venta) && ($venta = $linea->hasVenta) )

            {

                $css1 = "display:inline-block;vertical-align: middle;";

                $rows .= "<tr style='background: #ffe709;font-weight: 600'>";

                $rows .= "<td>";

                $rows .= "<a href='!" . self::class . "/modalForm?n={$linea->id_venta}' target='_blank'><i class='fa fa-file-pdf'></i></a>";

                $rows .= "#{$linea->id_venta}. {$venta->fecha}";

                if ( $venta->es_presupuesto || $venta->tipo == 'venta_presupuesto publico' || $venta->tipo == 'venta_presupuesto mayorista' || $venta->tipo == 'venta_presupuesto_publico' || $venta->tipo == 'venta_presupuesto')

                {

                    $rows .= "&nbsp;&nbsp;<a href='" . self::PANEL_URI . "/ventas/form/{$venta->id_venta}'><i class='fa fa-edit'></i></a>";

                }

                if ( in_array($this->admin_user->id_usuario, [1, 28]) )

                {
    
                    $rows .= "<a href='javascript:void(0)' onclick='dt_delete($venta->id_venta)'><i class='fa fa-trash-alt text-danger'></i></a>";
    
                }
                
                $head = $linea->atributo . ": " . Local::nombreLocal($linea->valor) . " => " . Local::nombreLocal($linea->flag);

                $caption = $venta->nombre_cliente;

                #--

                $rows .= "<p style='font-style:italic;font-size:11px;line-height:10px;margin:0'>" . str_ireplace($search, "<mark style='text-transform: uppercase'>{$search}</mark>", $caption) . "&nbsp;</p>";

                $rows .= "</td>";

                $rows .= "<td class='text-right' colspan='2'>";

                if ( !$venta->es_traspaso )

                {

                    /*$rows .= "<div class='amount'>";

                    $rows .= "<a href='!" . self::class . "/modalForm?n={$linea->id_venta}' target='_blank'><i class='fa fa-file-pdf'></i></a>";

                    $rows .= "&nbsp;$ " . $venta->total;

                    $rows .= "</div>";*/

                    #--

                    $rows .= "<span style='font-size:10px'>" . str_ireplace("|", "<br />", $venta->str_pagos) . "</span>&nbsp;&nbsp;";

                    //$rows .= "<a href='!" . self::class . "/modalForm?n={$linea->id_venta}' target='_blank'><i class='fa fa-file-pdf'></i></a>";

                }

                else

                {

                    //$rows .= "<td></td>";

                }

                $rows .= "</td></tr>";

            }

            $rows .= "<tr>";

            //$rows .= "<td>{$linea->producto}</td>";

            $rows .= "<td>{$linea->hasArticulo->item}</td>";

            $rows .= "<td class='text-center'>{$linea->cantidad}</td>";

            if ( $venta && !$venta->es_traspaso )

            {

                $rows .= "<td class='amount'>" . HFunctions::formatPrice($linea->subtotal) . "</td>";

            }

            $rows .= "</tr>";

        }

        #--

        //if ( $arr_total )

        {

            $rows .= "<tr><td colspan='3' align='right' id='pp-totales'>" . implode('<br/>', $arr_total) . "</td></tr>";

        }

        #--

        if ( !$print )

        {

            $rows .= "<tr class='not'>";

            $rows .= "<td colspan='3' id='td-count-{$num_rows}'>";

            $rows .= $num_rows ? $this->replaceLinks($lineas->links()) : "<div class='text-center'>Sin informaci&oacute;n</div>";

            $rows .= "</td>";

            $rows .= "</tr>";

            #-- 22/06/2022 -- para que no se bloquee

            if ( in_array($this->admin_user->id_usuario, [2, 13]) )

            {

                $blocked = 0;

            }

            HArray::jsonResponse(['rows' => $rows, 'blocked' => $blocked]);

        }

        return $rows;

    }


    public function modalForm()
    {

        $id_venta = floatval($_POST['id'] ?: ($id = $_GET['n']));

        $body = "";

        if ( ($venta = Venta::find($id_venta)) )

        {

            if ( $id )

            {

                $persona = $venta->hasCliente;

                $cuit = $this->config['cuit'];

                $entidad = $this->config['site_name'];

                $inicio_actividades = "01-04-2016";

                $condicion_iva = "RESPONSABLE MONOTRIBUTO";

                $direccion = "Belgrano 354. Lules (4128) - Tucuman";

                #--

                if ( $venta->operacion_ingreso )

                {

                    $cuit = $persona->dni;

                    $entidad = $persona->nombre_apellido;

                    $inicio_actividades = null;

                    $condicion_iva = "-";

                    $direccion = $persona->direccion;

                }

                $factura = new Facturacion();

                $factura->setFacturaNumero($venta->factura ?: $id);

                $factura->setFacturaEmision($venta->fecha, 0, 10);

                $factura->setFacturaCodigo(($venta->es_presupuesto || $venta->tipo == 'venta_presupuesto' || $venta->tipo == 'venta_presupuesto publico' || $venta->tipo == 'venta_presupuesto mayorista') ? "00" : 11);

                $factura->setEntidadCuit($cuit);

                $factura->setEntidadCondicionIva($condicion_iva);

                $factura->setEntidadInicio($inicio_actividades);

                $factura->setPuntoVenta(self::appUrl);

                $factura->setEntidadSucursal(Local::nombreLocal($venta->id_sucursal));

                $factura->setEntidadDireccion($direccion);

                $factura->setEntidadNombre($entidad);

                $factura->setFacturaTipoVta($venta->str_pagos);

                $factura->setClienteDni($persona->dni);

                $factura->setClienteNombre($venta->nombre_cliente);

                if ( !$venta->es_traspaso )

                {

                    $factura->setClienteDireccion($persona->direccion ?: "-");

                }

                $total = 0;

                foreach (($lineas = $venta->hasLineaVenta) as $linea)

                {

                    $item['cant.'] = $linea->cantidad;

                    $item['detalle'] = $linea->producto;

                    $item['p._unit'] = round($linea->subtotal / $linea->cantidad);

                    $total += ($item['subtotal'] = $linea->subtotal);

                    $factura->setFacturaItems($item);

                }

                $factura->setFacturaTotal($total);

                $factura->drawFactura();

                //$html .= "<div style='page-break-after:always;'></div>{$html}";

                //die($html);

                /*$options = new \Dompdf\Options();

                $options->setIsRemoteEnabled(true);

                $dompdf = new \Dompdf\Dompdf();

                $dompdf->setOptions($options);

                //$dompdf->setHttpContext($contxt);

                $dompdf->loadHtml($html.$html);

                $dompdf->setPaper('A4', "landscape");

                $dompdf->render();

                $dompdf->stream(str_pad($id, 11, "0", 0), ['Attachment' => 0]);*/

                die;

            }

            $this->setParams('data', $venta);

            $body = $this->loadView("admin/venta-detalle");

        }

        HArray::jsonResponse('body', $body);

    }

    // ----------------------------------------------------------------
    // imprimir remito para venta de quimicos
    // ----------------------------------------------------------------
    public function imprimirRemito()
    {

        $id_venta_quimico = floatval($_POST['id_venta_quimico'] ?: ($id_venta_quimico = $_GET['n']));

        $body = "";

        if ( ($venta_quimico = VentaQuimico::find($id_venta_quimico)) )

        {

            if ( $venta_quimico )

            {

                // $venta_quimico = $venta_quimico->leftjoin("persona", "venta_quimicos.cliente", '=', 'persona.id');


                // $persona = $venta_quimico->cliente;

                // Venta::find($id_venta_quimico)


                $cuit = $this->config['cuit'];

                $entidad = $this->config['site_name'];

                $inicio_actividades = "01-04-2016";

                $condicion_iva = "RESPONSABLE MONOTRIBUTO";

                $direccion = "Belgrano 354. Lules (4128) - Tucuman";

                #--

                $factura = new Facturacion();

                $factura->setFacturaNumero($id_venta_quimico);  // chequear si esta bien el nro de factura

                $factura->setFacturaEmision(substr($venta_quimico->fecha, 0, 10));

                $factura->setFacturaCodigo("00");

                $factura->setEntidadCuit($cuit);

                $factura->setEntidadCondicionIva($condicion_iva);

                $factura->setEntidadInicio($inicio_actividades);

                $factura->setPuntoVenta(self::appUrl);

                $factura->setEntidadSucursal(Local::nombreLocal(6));

                $factura->setEntidadDireccion($direccion);

                $factura->setEntidadNombre($entidad);

                $factura->setFacturaTipoVta("");

                $factura->setClienteDni("-");

                if(is_numeric($venta_quimico->cliente))
                {
                    $persona = Persona::find($venta_quimico->cliente);
                    $nombre_client = $persona->apellido . ' ' . $persona->nombre;
                    $factura->setClienteNombre($nombre_client);
                }else{
                    $factura->setClienteNombre($venta_quimico->cliente);
                }


                $total = 0;

                foreach (($lineas = $venta_quimico->hasLineaVenta) as $linea)

                {

                    $item['cant.'] = $linea->cantidad;

                    $item['detalle'] = $linea->producto;

                    $item['p._unit'] = round($linea->subtotal / $linea->cantidad);

                    $total += ($item['subtotal'] = $linea->subtotal);

                    $factura->setFacturaItems($item);

                }

                $factura->setFacturaTotal($total);

                $factura->drawFactura();

                die;

            }

            $this->setParams('data', $venta_quimico);

            $body = $this->loadView("admin/venta-detalle");

        }

        HArray::jsonResponse('body', $body);

    }

     // ----------------------------------------------------------------
    // imprimir remito para venta de mayoristas
    // ----------------------------------------------------------------
    public function imprimirRemitoMayorista()
    {

        $idventa_mayorista = floatval($_POST['idventa_mayorista'] ?: ($idventa_mayorista = $_GET['n']));

        $body = "";

        if ( ($venta_mayorista = VentasMayorista::find($idventa_mayorista)) )

        {

            if ( $venta_mayorista )

            {

                $cuit = $this->config['cuit'];

                $entidad = $this->config['site_name'];

                $inicio_actividades = "01-04-2016";

                $condicion_iva = "RESPONSABLE MONOTRIBUTO";

                $direccion = "Belgrano 354. Lules (4128) - Tucuman";

                #--

                $factura = new Facturacion();

                $factura->setFacturaNumero($idventa_mayorista);  // chequear si esta bien el nro de factura

                $factura->setFacturaEmision(substr($venta_mayorista->created_at, 0, 10));

                $factura->setFacturaCodigo("00");

                $factura->setEntidadCuit($cuit);

                $factura->setEntidadCondicionIva($condicion_iva);

                $factura->setEntidadInicio($inicio_actividades);

                $factura->setPuntoVenta(self::appUrl);

                $factura->setEntidadSucursal(Local::nombreLocal(6));

                $factura->setEntidadDireccion($direccion);

                $factura->setEntidadNombre($entidad);

                $factura->setFacturaTipoVta("");

                $factura->setClienteDni("-");

                $persona = Persona::find($venta_mayorista->id_cliente);
                $nombre_client = $persona->apellido . ' ' . $persona->nombre;
                $factura->setClienteNombre($nombre_client);


                $total = 0;

                foreach (($lineas = $venta_mayorista->hasLineaVenta) as $linea)

                {

                    $item['cant.'] = $linea->cantidad;

                    $item['detalle'] = $linea->producto;

                    $item['p._unit'] = round($linea->subtotal / $linea->cantidad);

                    $total += ($item['subtotal'] = $linea->subtotal);

                    $factura->setFacturaItems($item);

                }

                $factura->setFacturaTotal($total);

                $factura->drawFactura();

                die;

            }

            $this->setParams('data', $venta_mayorista);

            $body = $this->loadView("admin/venta-detalle");

        }

        HArray::jsonResponse('body', $body);

    }

    // ----------------------------------------------------------------
    //      |||| barcode ||||
    // ----------------------------------------------------------------
    public function imprimirBarcode()
    {
        
        $barcode = $_GET['n'];

        $factura = new Facturacion();

        $factura->drawBarcode(true,$barcode);

    }


    protected function _getLineas()

    {

        return (array)json_decode(file_get_contents($this->file_name), true);

    }



    protected function _setLineas($item = null, $overwrite = false)

    {

        $lineas = $this->_getLineas();

        $lineas[time()] = $item ?: array();

        if ( $overwrite )

        {

            $lineas = array();

        }



        file_put_contents($this->file_name, json_encode($lineas ?: $item));

    }



    public function getProducto()

    {

        $id_producto = floatval($_POST['id_producto']);

        $id_local = floatval($_POST['id_local']) ?: $this->id_local_ses;

        $codigo = trim($_POST['codigo']);

        $tipo = trim($_REQUEST['tp'] ?: "publico");

        $term = trim($_POST['term']);

        if ( $tipo == strtolower(Venta::presupuesto) || $tipo == 'presupuesto mayorista')

        {

            $tipo = "mayorista";

        }

        //floatval($linea->subtotal) / $linea->cantidad;

        $json = array();

        #-- Change: 27/11/2020

        if ( $term )

        {

            $where = null;

            if ( $_GET['hdn'] )

            {

                $where .= "`oculto` AND ";

            }

            $where .= "(`codigo`= '{$term}' OR `producto` LIKE '%{$term}%')";

            $productos = Articulo::selectRaw("`id_producto` AS 'id', `precio`, `codigo`, CONCAT(`id_producto`, ' - ', UCASE(`producto`)) AS 'value'")

                ->whereRaw($where)->get();

            HArray::jsonResponse($productos->toArray());

        }

        #--

        $producto = Articulo::getProducto($id_producto, $codigo);

        #-- Buscar si ya existe en la línea y restar esa cantidad.

        if ( $id_local == Local::ventaPagina )

        {

            $id_local = Local::depositoMitre;

        }

        $stock = $producto->cantidad_array[$id_local];

        #-- 19/04/2021 : comentar si no funciona

        //$stock -= $this->_lineaVenta($id_producto)['add'];

        if ( $id_producto == Articulo::varios )

        {

            $stock = 50;

        }

        if ( $producto && self::isXhrRequest() )

        {

            $linea = $producto->hasLineaVenta()->selectRaw("ROUND(subtotal/cantidad) As 'p_unit'")->where('atributo', "LIKE", "%_{$tipo}")->orderBy('id', "DESC")->first();



            $json['id_producto'] = $producto->id_producto;

            $json['stock'] = $stock;

            $json['codigo'] = $producto->codigo;

            $articulo = $producto->item;

            //if ( $stock <= $producto->stock_alerta )

            {

                $articulo .= " (En stock: <b>{$stock}</b> un.)";

            }

            $json['producto'] = $articulo;

            $json['precio'] = $producto->array_precios[$tipo];

            //$json['precio'] = $linea->p_unit ?: $producto->array_precios[$tipo];

            $json['alerta'] = "";

            //$json['alerta'] = ($stock <= $producto->stock_alerta) ? "Cantidad restante <b>{$stock}</b> un." : "";

            #--

        }

        HArray::jsonResponse($json);

    }

    // ----------------------------------------------------------------
    // Controla el modal de edicion de tipo de venta en /ventas
    // ----------------------------------------------------------------
    public function updateTipoVentaForm()
    {

        $form_title = "Edicion de tipo venta";

        $permiso = Permiso::permisoCrear;

        $id_venta = floatval($_POST['id_venta']);

        if ( $venta = Venta::find($id_venta) )

        {

            $permiso = Permiso::permisoEditar;

            $form_title = "Editar tipo de Venta #{$id_venta} ";

            // $id_movimiento = $id_movimiento_pend[0]['attributes']['id'];

            // $id_concepto = $id_movimiento_pend[0]['attributes']['id_concepto'];

            // $tipo = $venta->tipo;

            // $estado = $venta->estado;

            // $fecha_hora = $venta->fecha_hora;

        }

        $this->setPageTitle($form_title);

        $selectTipoVenta = $this->_selectTipoVenta();

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/updateTipoVenta?id_venta=<?= $id_venta ?>" id="editar-tipo-venta-form" autocomplete="off" method="post">
               
                    <div class="input-group-addon">

                        <div class="col-md-6 form-group">

                            <label for="id_tipo_venta">Seleccione el nuevo Tipo Venta <i class="required"></i></label>

                            <?= $selectTipoVenta ?>

                        </div>

                        <div class="col-md-12 text-right">
    
                            <button type="submit" class="btn btn-primary">Guardar</button>

                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <script type="text/javascript">

            // se dispara al hacer clic en 'Guardar'
            (editarVentaForm = document.getElementById('editar-tipo-venta-form')).onsubmit = function (e) {

                e.preventDefault();

                theForm = this;

                submit_form(theForm, function (rsp) {

                    theForm.reset();
                    
                    if ( typeof get_rows === "function" )
                    {
                        get_rows();
                    }
                    delete rsp["notice"];

                    theForm.setAttribute("rel", JSON.stringify(rsp));

                });

                document.querySelector('[data-dismiss="modal"]').click();

            };

        </script>

        <?php

        $this->setBlockModal(ob_get_clean());

    }
    // ----------------------------------------------------------------
    // Fin * Controla el modal de edicion de tipo de venta en /ventas
    // ----------------------------------------------------------------
    // ----------------------------------------------------------------
    // Controla el modal de edicion de venta en /ventas
    // ----------------------------------------------------------------
    public function updateVentaForm()
    {

        $form_title = "Edicion de venta";

        $permiso = Permiso::permisoCrear;

        $id_venta = floatval($_POST['id_venta']);

        if ( $venta = Venta::find($id_venta) )

        {

            $permiso = Permiso::permisoEditar;

            $form_title = "Editar Venta #{$id_venta} ";

            // $id_movimiento = $id_movimiento_pend[0]['attributes']['id'];

            // $id_concepto = $id_movimiento_pend[0]['attributes']['id_concepto'];

            // $tipo = $venta->tipo;

            // $estado = $venta->estado;

            // $fecha_hora = $venta->fecha_hora;

        }

        $this->setPageTitle($form_title);

        $selectConcepto = $this->_selectConcepto();

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/updateConceptoMovimiento?id_venta=<?= $id_venta ?>" id="editar-venta-form" autocomplete="off" method="post">

               
                    <div class="input-group-addon">

                        <div class="col-md-6 form-group">

                            <label for="id_concepto">Concepto <i class="required"></i></label>

                            <?= $selectConcepto ?>

                        </div>

                        <div class="col-md-12 text-right">
    
                            <button type="submit" class="btn btn-primary">Guardar</button>

                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <script type="text/javascript">

            // se dispara al hacer clic en 'Guardar'
            (editarVentaForm = document.getElementById('editar-venta-form')).onsubmit = function (e) {

                e.preventDefault();

                theForm = this;

                submit_form(theForm, function (rsp) {

                    theForm.reset();
                    
                    if ( typeof get_rows === "function" )
                    {
                        get_rows();
                    }
                    delete rsp["notice"];

                    theForm.setAttribute("rel", JSON.stringify(rsp));

                });

                document.querySelector('[data-dismiss="modal"]').click();

            };

        </script>

        <?php

        $this->setBlockModal(ob_get_clean());

    }
    // ----------------------------------------------------------------
    // Fin * Controla el modal de edicion de venta en /ventas
    // ----------------------------------------------------------------


    /*private function _lineaVenta($id_producto = null, $operacion = null, $id_linea = null)
    {

        $rows = null;

        $total = 0;

        //HArray::arraySortByColumn($this->lineas, 'id', SORT_DESC);

        foreach ($this->_getLineas() as $index => $linea)

        {

            if ( $id_producto || $id_linea )

            {

                if ( ($id_producto == $linea['id_producto']) || in_array($id_linea, [$linea['id_linea'], $index]) )

                {

                    $linea['key'] = $index;

                    return $linea;

                }

                return false;

            }

            #--

            if ( $linea['op'] != ($operacion ?: $this->modulo) )

            {

                continue;

            }

            $subtotal = floatval($linea['subtotal']);

            $articulo = $linea['producto'];

            $cantidad = intval($linea['cantidad']);

            $rows .= "<tr class='trow' id='{$index}' rel='" . ($subtotal / $cantidad) . "&{$cantidad}'>";

            $rows .= "<td class='text-center col-md-2'><a href='javascript:void(0)' class='pull-left' onclick='seleccionar(\"{$index}&{$linea['cup']}\",true)'><i class='fa fa-edit'></i></a> {$cantidad}</td>";

            $rows .= "<td><a href='javascript:void(0)' title='quitar' onclick='eliminar(\"{$index}\")'><i class='fa fa-trash text-danger'></i></a>&nbsp;{$articulo}</td>";

            $rows .= "<td class='amount col-md-2'>" . number_format($subtotal, 2, '.', '') . "</td>";

            $rows .= "</tr>";

            $total += $subtotal;

        }

        return $rows;

    }*/



    private function _lineaVenta($id_producto = null, $operacion = null, $id_linea = null)
    {

        $rows = null;

        $total = 0;

        //HArray::arraySortByColumn($this->lineas, 'id', SORT_DESC);

        foreach ($this->_getLineas() as $index => $linea)

        {

            $cantidad = intval($linea['cantidad']);

            if ( $id_producto || $id_linea )

            {

                //if ( ($id_producto == $linea['id_producto']) || in_array($id_linea, [$linea['id_linea'], $index]) )

                if ( ($id_producto == $linea['id_producto']) || ($id_linea && in_array($id_linea, [$linea['id_linea'], $index])) )

                {

                    $rows['key'] = $index;

                    $rows['add'] += $cantidad;

                }

            }

            else

            {

                #--

                if ( $linea['op'] != ($operacion ?: $this->modulo) )

                {

                    continue;

                }

                $subtotal = floatval($linea['subtotal']);

                $articulo = $linea['producto'];

                $rows .= "<tr class='trow' id='{$index}' rel='" . ($subtotal / $cantidad) . "&{$cantidad}'>";

                $rows .= "<td align='center' class='col-md-2'><a href='javascript:void(0)' class='pull-left' onclick='seleccionar(\"{$index}&{$linea['cup']}\",true)'><i class='fa fa-edit'></i></a> {$cantidad}</td>";

                $rows .= "<td><a href='javascript:void(0)' title='quitar' onclick='eliminar(\"{$index}\")'><i class='fa fa-trash text-danger'></i></a>&nbsp;{$articulo}</td>";

                $rows .= "<td align='right' class='amount col-md-2'>" . number_format($subtotal, 2, '.', '') . "</td>";

                $rows .= "</tr>";

                $total += $subtotal;

            }

        }

        return $rows;

    }



    public function agregarLinea(LineaVenta $linea = null, $op = null)

    {

        //$vta_por_mayor = ($_POST['tipo_venta'] == Aporte::POR_MAYOR);

        $id_producto = floatval($linea->id_producto ?: $_POST['id_producto']);

        $cantidad = intval($linea->cantidad ?: $_POST['cantidad']);

        $operacion = trim($op ?: $_POST['operacion']);

        $codigo = trim($linea->hasArticulo->codigo ?: $_POST['cup']);

        $op_nro = trim($linea->id_venta ?: $_POST['op_nro']);

        if ( intval($_POST['nro']) && false )

        {

            $res['id_venta'] = Venta::nextIdVenta();

        }

        if ( $id_producto && $cantidad )

        {

            $_linea['cantidad'] = $cantidad;

            $_linea['id_producto'] = $id_producto;

            //$_linea['cup'] = $codigo;

            $_linea['producto'] = trim($linea->producto ?: $_POST['producto']);

            $_linea['subtotal'] = floatval($linea->subtotal ?: $_POST['subtotal']);

            $_linea['op'] = $operacion;

            $_linea['op_nro'] = $op_nro;

            if ( ($id_linea = $linea->id) )

            {

                $_linea['id_linea'] = $id_linea;

                return $_linea;

            }

            #-- Change: 27/11/2020

            if ( !$codigo && intval($_POST['hdn']) )

            {

                $articulo = Articulo::findOrNew($id_producto);

                $articulo->codigo = time();

                //$articulo->id_producto = $id_producto;

                $articulo->producto = mb_strtolower($_linea['producto']);

                $articulo->precio = round(($_linea['subtotal'] / $cantidad), 2);

                $articulo->oculto = 1;

                $articulo->borrado = 1;

                $articulo->save();

                $_linea['id_producto'] = $articulo->id_producto;

                $_linea['producto'] = $articulo->id_producto . " - " . $articulo->nombre;

            }

            #--

            $this->_setLineas($_linea);

            /*if ( $id_linea )

            {

                //return;

            }*/

        }

        #--

        $res['rows'] = ($rows = $this->_lineaVenta(null, $operacion));

        //HArray::jsonResponse($res);

        die($rows);

    }


    // =================================
    // Eliminia una linea venta desde el formulario de venta
    // =================================
    public function eliminarLinea()

    {

        $id = trim($_GET['id'] ?: ($editar = $_POST['id_linea']));

        $precio = floatval($_POST['prc']);

        $cantidad = floatval($_POST['cnt']);

        $id_local = intval($_POST['id_local']);

        if ( $id_local == Local::ventaPagina )

        {

            $id_local = Local::mitreNegocio;

        }

        $lineas = $this->_getLineas();

        foreach ($lineas as $index => $linea)

        {

            if ( $id == $index )

            {

                $lineas[$index]['cantidad'] = $cantidad;

                $lineas[$index]['subtotal'] = ($subtotal = ($cantidad * $precio));

                if ( !$editar )

                {

                    unset($lineas[$index]);

                }

                #--

                $articulo = Articulo::find($linea['id_producto']);

                $stock = $articulo->cantidad_array;

                $edicion = ($editar && !$articulo->oculto);

                if ( $edicion && in_array(strtolower($linea['op']), [Venta::tpVenta, Venta::tpTraspaso]) )

                {

                    //HArray::varDump($id_local);

                    if ( ($stock[$id_local] -= $cantidad) < 0 )

                    {

                        HArray::jsonError("No hay stock suficiente!", "cnt");

                    }

                }

                if ( $linea['op_nro'] && ($item = LineaVenta::find($linea['id_linea'])) )

                {

                    #--

                    $item->cantidad = $cantidad;

                    $item->subtotal = $subtotal;

                    $item->save();

                    if ( !$editar )

                    {

                        $math = "-";

                        if ( $linea['op'] == Venta::tpVenta )

                        {

                            $math = "+";

                        }

                        //if($linea['op'])

                        eval("\$stock[{$id_local}] {$math}= \$cantidad;");

                        $articulo->cantidad_json = $stock;

                        $articulo->save();

                        $item->delete();

                    }

                }

                break;

            }

        }

        $this->_setLineas($lineas, true);

        $p_id_venta = $linea['op_nro'];

        $p_id_producto = $linea['id_producto'];

        if(!$p_id_venta)
        {
            $p_id_venta = '0';
        }

        if(!$p_id_producto)
        {
            $p_detalle = 'eliminacion de linea venta - id_producto : 0';
        }else{
            $p_detalle = 'eliminacion de linea venta - id_producto : ' . $p_id_producto;
        }

        $this->alta_incidencia($this->admin_user->id_usuario,'eliminacion',$p_id_venta,$id,$p_detalle,'0','0','0');

        HArray::jsonResponse('ok', true);

    }
    // =================================
    //
    // =================================
    public function updateConceptoMovimiento()
    {

        $id_concepto_nuevo = floatval($_POST['id_concepto']);
        $id_venta = floatval($_GET['id_venta']);

         // 
         if($id_venta){
            Movimiento::where('id_operacion', $id_venta)->update(['id_cuenta' => $id_concepto_nuevo]);
        }

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        HArray::jsonResponse($json);

    }
    // =================================
    //
    // =================================
    public function updateTipoVenta()
    {

        $id_tipo_venta = floatval($_POST['id_tipo_venta']);
        $id_venta = floatval($_GET['id_venta']);

        
        $venta = Venta::findOrNew($id_venta);

        switch ($id_tipo_venta) {
            case 0:
                $venta->tipo = 'venta_publico';
                break;
            case 1:
                $venta->tipo = 'venta_mayorista';
                break;
            case 2:
                $venta->tipo = 'venta_presupuesto';
                break;
            case 3:
                $venta->tipo = 'compra';
                break;
            default:
                break;
        }

        $venta->save();

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        HArray::jsonResponse($json);
    }

    // =================================
    //
    // =================================
    public function guardarVenta()
    {

        $id_venta = floatval($_POST['id_venta']);

        $id_cliente = trim($_POST['cliente']);

        $id_cuenta = floatval($_POST['cuenta']);

        $tipo_venta = strtolower($_POST['tipo_venta']);

        $operacion = $_operacion = trim($_POST['operacion']);

        $monto_recibido = floatval($_POST['recibido']);

        $js_venta = json_decode($_POST['venta'], true);

        $_cuentas = (array)$js_venta['pagos'];

        $id_local = intval($js_venta['id_local'] ?: $this->id_local_ses);

        $id_local_destino = intval($js_venta['destino']);

        $factura = trim($js_venta['factura']);

        $ventaPresupuesto = ($tipo_venta == strtolower(Venta::presupuesto));

        $operacionCompra = ($_operacion == Venta::tpIngreso);

        $operacionTraspaso = ($_operacion == Venta::tpTraspaso);

        $cuentas = json_decode($_POST['pc_pago'], true);

        $estado_venta = $_POST['estado-venta'];

        $nro_tarjeta_input = $_POST['nro_tarjeta_input'];

        $deuda_cobrar = in_array(Concepto::itemDeudaCliente, array_keys($cuentas));

        //HArray::varDump($ventaPresupuesto);

        //HArray::varDump($_POST);
        $total_venta_cuentas = array_sum($_cuentas);

         #--

         if ( $nro_tarjeta_input )
         {
            if (strlen($nro_tarjeta_input) < 16) {
                HArray::jsonError("Nro tarjeta invalido");
            } elseif (strlen($nro_tarjeta_input) > 20) {
                HArray::jsonError("Nro tarjeta invalido");
            }           
        }

        if ( $tipo_venta )
        {
            if($tipo_venta == 'presupuesto publico')
            {
                $tipo_venta = 'presupuesto';
            }

            $operacion .= "_{$tipo_venta}";

        }

        #--

        if ( !($lineas_venta = $this->_getLineas()) )
        {

            HArray::jsonError("A&uacute;n no se agregaron artículos");
        }

        #--

        //HArray::varDump($lineas_venta);

        if ( $operacionTraspaso && !$id_local_destino )
        {
            HArray::jsonError("Seleccionar un Local", "destino");
        }

        /*if ( !key_exists($id_local, Local::$_LOCALES) )

        {

            HArray::jsonError("Seleccionar un Local", "id_local");

        }*/

        if ( $deuda_cobrar && !$id_cliente )
        {
            HArray::jsonError("Debe seleccionar un " . (($_operacion == MenuPanel::menuCompra) ? "Proveedor" : "Cliente"));
        }

        #--

        $id_usuario = $this->admin_user->id_usuario;

        $fecha = HDate::sqlDate($js_venta['fecha'] ?: date('d/m/Y'));

        if ( ($_operacion != Venta::tpTraspaso) || true )
        {

            #-- guardar la venta

            $venta = Venta::findOrNew($id_venta);

            $venta->id_usuario = $id_usuario;

            $venta->fecha_hora = $fecha . date(" H:i:s");

            $venta->factura = $factura;

            $venta->tipo = $operacion;

            $venta->id_sucursal = $id_local;

            if($venta->id_sucursal == 0 || $id_local == 0)
            {
                $venta->id_sucursal = 12;
                $id_local = 12;
            }

            $venta->id_cliente = floatval($id_cliente ?: $id_local_destino);

            if ( !is_numeric($id_cliente) )
            {
                $venta->cliente = mb_strtolower($id_cliente);
            }

            // ---------------------------
            if ( $estado_venta == 'r' )
            {
                $venta->estado = 'realizada';
            }
            else{
                $venta->estado = 'pendiente';
            }


            if ( $id_local == Local::ventaPagina )
            {
                $venta->external_id = $id_local;
                $venta->estado = Venta::estadoEspera;
                //$id_local = Local::depositoMitre;
            }

            // ** Comprobante **

            if($_FILES["comprobante"]["type"])
            {

                $file_type = $_FILES["comprobante"]["type"];


                if (($file_type != "image/png") && ($file_type != "image/jpeg") && ($file_type != "image/jpg") && ($file_type != "application/pdf") && ($file_type != "image/webp")) {
                    HArray::jsonError("Comprobante invalido");
                }else{
                    $extension = substr($file_type, strpos($file_type, "/") + 1);
    
                    $targetfolder = $_SERVER["DOCUMENT_ROOT"] . "/media/uploads/comprobantes/";
        
                    $namedFile = rand() . "." . $extension;
        
                    $targetfolder = $targetfolder . $namedFile;
        
                    $moved = move_uploaded_file($_FILES['comprobante']['tmp_name'], $targetfolder);
        
                    $sizeFile = $_FILES['comprobante']['size'];
    
                    if ((!$moved) || ($sizeFile > 500000)) {
                        HArray::jsonError("Comprobante invalido");
                    }else{
                        $venta->comprobante = $namedFile;
                    }
            }
       

            }

            // ** Fin comprobante


            $venta->save();

            // Si existe nro tarjeta, almaceno
            if ( $nro_tarjeta_input )
            {
                $nro_tarjeta_venta = new TarjetaVenta();

                $key = $this->config['encrypt_key'];

                $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));

                $encrypted_nro_tarjeta = openssl_encrypt($nro_tarjeta_input, 'AES-256-CBC', $key,0,$iv);

                $nro_tarjeta_venta->id_venta_tarjeta = $venta->id_venta;
                $nro_tarjeta_venta->nro_tarjeta = $encrypted_nro_tarjeta;
                $nro_tarjeta_venta->iv = $iv;

                $nro_tarjeta_venta->save();

            }

            #--

        }

        $total = 0;

        #--

        foreach ($lineas_venta as $time => $lv)
        {

            #-- Guardar la línea de venta

            if ( ($lv['op'] != $_operacion) && ($operacion != 'venta_mayorista') )

            {

                continue;

            }

            $subtotal = floatval($lv['subtotal']);

            $total += $subtotal;

            $cantidad = floatval($lv['cantidad']);

            /*if ( $tipo_venta == Aporte::POR_MAYOR )

            {

                $subtotal -= ($subtotal * $this->descuento);

            }*/

            $linea = LineaVenta::findOrNew($lv['id_linea']);

            $linea->id_venta = $venta->id_venta ?: $id_usuario;

            $linea->id_producto = $lv['id_producto'];

            $linea->cantidad = $lv['cantidad'];

            //$linea->flag = $id_local_destino;

            $linea->atributo = ucfirst($operacion);

            $linea->valor = $id_local;

            $linea->flag = $id_local_destino;

            $linea->fecha_hora = $fecha . date(' H:i:s', $time);

            $linea->subtotal = $subtotal;

            // $linea->subtotal = $total_venta;

            $linea->save();

            #-- Changes: 23/05/2020 # 28/11/2020

            $precioUn = round(($subtotal / $cantidad), 2);

            $producto = Articulo::find($lv['id_producto']);

            if ( $producto->oculto && ($producto->precio != $precioUn) )
            {
                $producto->precio = $precioUn;

                $producto->save();
            }

            #--

            if ( !$ventaPresupuesto && ($lv['id_producto'] != 1) && $producto && ($operacion != "venta_presupuesto mayorista"))

            {

                /*$stock = $producto->cantidad_array;

                $arr_stck = array();

                $cantidad_actual = $quantity = floatval($stock[$id_local]);

                if ( $operacionCompra )

                {

                    $cantidad_actual += $cantidad;

                }

                else

                {

                    $cantidad_actual -= $cantidad;

                    $quantity = 1; // comentar esto si se quiere indicar stock+

                    $quantity = $cantidad_actual;

                    if ( $id_local_destino )

                    {

                        $arr_stck[2] = floatval($stock[$id_local_destino]);

                        $stock[$id_local_destino] += $cantidad;

                    }

                }

                #--

                if ( $cantidad_actual <= 0 )

                {

                    $cantidad_actual = $quantity = 0;

                }

                $flagStock = ($operacionCompra && $operacionTraspaso || !$cantidad_actual);

                $stock[$id_local] = $cantidad_actual;

                $arr_stck[1] = $quantity;

                $producto->cantidad_json = $stock;

                $producto->save();*/

                #--
                //Resta de stock

                $arr_stck = $producto->stockUpdate($id_local, $cantidad, $id_local_destino, $operacionCompra);

                //if ( $id_local != Local::mercadoLibre && ($operacionCompra || !$cantidad_actual) )

                if ( $id_local != Local::ventaPagina )

                {

                    $linea->stock = $arr_stck;

                    $linea->save();

                }

            }

        }

        // Caso porcentaje aumento con tarjetas
        if(($total != $total_venta_cuentas) && !$venta->es_traspaso && !$ventaPresupuesto && ($operacion != "venta_presupuesto mayorista"))
        {
            // $linea = LineaVenta::findOrNew($ultima_linea_venta);

            $linea = new LineaVenta;

            $linea->id_venta = $venta->id_venta ?: $id_usuario;

            $linea->id_producto = 1;

            $linea->cantidad = 1;

            $linea->atributo = ucfirst($operacion);

            $linea->valor = $id_local;

            $linea->flag = $id_local_destino;

            $linea->fecha_hora = $fecha . date(' H:i:s', $time);

            $linea->subtotal = ($total_venta_cuentas - $total);

            // $linea->subtotal = $total_venta;

            $linea->save();
        }

        #--

        if ( !$ventaPresupuesto && ($operacion != "venta_presupuesto mayorista"))
        {
            $saldo = $i = $aid = 0;

            $pagos = collect([]);

            if ( $id_venta )
            {
                $pagos = Movimiento::whereRaw("id_operacion='{$id_venta}'")->get();
            }

            $tipos_pago_concepto = array();
            
            foreach ($_cuentas as $id_concepto => $monto) {

                $tipos_pago_concepto[explode('&', $id_concepto)[1]] = $monto;

            }

            foreach ($tipos_pago_concepto as $id_cuenta => $monto)
            {

                $reg = $pagos[$i] ?: new Movimiento();

                /*if ( !($reg = Movimiento::whereRaw("id_operacion='{$venta->id_venta}' AND id_cuenta='{$id_cuenta}'")->first()) )

                {

                    $reg = Movimiento::crear($debe, $haber, $monto, $this->id_local_ses, $venta->id_venta);

                }*/

                // $reg->id_sucursal = $this->id_local_ses;

                $reg->id_sucursal = $id_local;

                $reg->id_cuenta = $id_cuenta;

                $reg->id_operacion = $venta->id_venta;

                $reg->id_concepto = $operacionCompra ? Concepto::itemCompra : Concepto::itemVenta;

                $reg->importe = $monto;

                $reg->modulo = Movimiento::moduloStock;

                $reg->fecha_registro = substr($fecha, 0, 10);

                $reg->id_relacion = $aid;

                $reg->saldo = intval($operacionCompra);

                if ( $id_cuenta == Concepto::cuentaCorriente )

                {

                    $reg->modulo = Movimiento::moduloCuenta;

                    $reg->accion = $id_cliente;

                    if ( $operacionCompra )

                    {

                        $reg->accion = 0;

                        $reg->valor = $id_cliente;

                    }

                    $saldo += $monto;

                    //$reg->comentario =

                }

                #--

                $reg->save();

                if ( !$i )

                {

                    $aid = $reg->id;

                }

                $pagos->forget($i);

                $i++;

            }

            #--

            foreach ($pagos as $pago)

            {

                $pago->delete();

            }

            //Movimiento::whereRaw("`id_operacion`='{$venta->id_venta}' AND id_cuenta NOT IN ('" . implode("','", array_keys($cuentas)) . "')")->delete();

            #--

            CuentaCliente::actualizarSaldo($id_cliente, $saldo);

        }


        $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        //$json['location'] = self::PANEL_URI . "/ventas";

        //$json['ticket'] = self::renderView(self::ADMIN_VIEWS . "venta-detalle", ['data' => $venta, 'hidden' => true]);

        $json['ok'] = $venta->id_venta ?: 1;//Venta::nextIdVenta();

        HArray::jsonResponse($json);

    }


    // =================================
    //
    // =================================
    public function exportar()

    {

        $data = $this->historialArticulos(true);

        if ( !is_numeric($prm = trim($_GET['pdf'])) )

        {

            static::$_thead = ["Artículo", "Cantidad", "Subtotal"];

            if ( $prm != Venta::tpTraspaso )

            {

                static::$_thead[] = "";

            }

        }

        $this->exportando(static::$_thead, $data);

    }


    // =================================
    //
    // =================================
    public function printList()
    {

        //$datos = $this->_getLineas();

        $list = "<table width='100%' border='1' cellspacing='0' cellpadding='0'>";

        $list .= "<tr><td colspan='3' align='center'><h4 style='margin:4px 0'>" . date('d/m/Y H:i:s') . "</h4>Traspaso desde " . Local::nombreLocal($this->id_local_ses) . "<br/></td></tr>";

        $list .= "<tbody>";

        $list .= "<tr>";

        $list .= "<th>Cantidad</th>";

        $list .= "<th>Descripci&oacute;n</th>";

        $list .= "<th>Subtotal</th>";

        $list .= "</tr>";

        $list .= $this->_lineaVenta(null, "traspaso");

        $list .= "</tbody>";

        $list .= "</table>";

        ExportOpts::exportar($list, true);

    }


    // =================================
    //
    // =================================
    public function exportarVentas()

    {

        $this->exportando(static::$_thead, $this->getRows(true));

    }


    // =================================
    //
    // =================================
    public function setEstado()

    {

        $id_venta = floatval($_POST['id']);

        $attr = trim($_POST['attr']);

        $estado = trim($_POST['estado']);

        #--

        if ( $fecha = HDate::sqlDate($_GET['fch']) )

        {

            if ( $control = Incidencia::whereRaw("`operacion`='" . Incidencia::controlVenta . "' AND `id_usuario`='{$this->admin_user->id_usuario}' AND `id_operacion`='" . strtotime($fecha) . "'")->first() )

            {

                $control->estado = 1;

                $control->save();

            }

            return;

        }

        #--

        if ( $venta = Venta::find($id_venta) )

        {

            $venta->{$attr} = $estado;

            $venta->save();

        }

    }


    // =================================
    //
    // =================================
    private function _deshacer($cup, $operacion, $id_local, $cantidad, $destino = null)

    {

        if ( $articulo = Articulo::find($cup) )

        {

            $stock = $articulo->cantidad_array;

            $signo = "+";

            if ( $operacion == Venta::tpIngreso )

            {

                $signo = "-";

            }

            eval("\$stock[{$id_local}] {$signo}= \$cantidad;");

            if ( $destino )

            {

                $stock[$destino] -= $cantidad;

            }

            $articulo->cantidad_json = $stock;

            $articulo->save();

        }

    }


    // =================================
    //
    // =================================
    public function eliminar()

    {

        $id_venta = floatval($_POST['id']);

        if ( $venta = Venta::find($id_venta) )

        {

            //HArray::varDump(!$venta->es_presupuesto && ($venta->id_sucursal != Local::mercadoLibre));

            foreach ($venta->hasLineaVenta as $linea)

            {

                $id_local = ($venta->id_sucursal ?: $linea->valor);

                $destino = ($venta->id_cliente ?: intval($linea->flag));

                $operacion = strtolower($venta->tipo ?: $linea->atributo);

                if ( !$venta->es_presupuesto && ($venta->id_sucursal != Local::ventaPagina) )

                {

                    $this->_deshacer($linea->id_producto, $venta->tipo, $id_local, $linea->cantidad, $destino);

                }

            }

            //Movimiento::whereRaw("`id_operacion`='{$id_venta}' AND `modulo` IN('" . Movimiento::moduloCuenta . "','" . Movimiento::moduloStock . "'")->delete();

            $venta->hasPago()->delete();

            $this->alta_incidencia($this->admin_user->id_usuario,'delete venta','0',$id_venta,'operacion : ' . $operacion . " - fecha venta : " . $venta->fecha_hora . " - tipo : " . $venta->tipo,'0','0','0');

            $venta->delete();

        }

    }

    // ----------------------------------------------------------------
    //   
    // ----------------------------------------------------------------
    public function alta_incidencia($p_id_usuario,$p_operacion,$valor,$p_id_operacion,$p_detalle,$p_importe,$p_cobrado,$p_estado)
    {

        $incidencia = new Incidencia;        
        
        $incidencia->id_usuario = $p_id_usuario;                
        $incidencia->operacion = $p_operacion;
        $incidencia->valor = $valor;
        $incidencia->id_operacion = $p_id_operacion;

        $incidencia->detalle = $p_detalle;                
        $incidencia->importe = $p_importe;
        $incidencia->cobrado = $p_cobrado;
        $incidencia->estado = $p_estado;
        
        $incidencia->fecha_hora = date("Y-m-d H:i:s");

        try {
            // Page code
            $incidencia->save();
        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }


    }

}