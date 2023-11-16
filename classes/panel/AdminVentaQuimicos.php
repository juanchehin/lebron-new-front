<?php


class AdminVentaQuimicos extends AdminMain

{

    const SES_LV = "linea_venta";

    private $file_name;


    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuQuimicos);

        if ( !in_array($this->admin_user->id_usuario, [1, 21, 28,38,30,47,32]) )

        {

            $url = '/ls-admin';

            header('Location: '.$url);

        }


        $this->file_name = "media/meta/" . str_pad($this->admin_user->id_usuario, 8, "0", 0) . ".lvq";

    }



    public function index($online = false)

    {

        $this->setPageTitle("Venta de quimicos");

        // $this->setBotonNuevoRedirectNuevaVenta();

        $this->setBotonNuevo("Nueva Venta", "javascript:void(0)");

        $columns[] = "#";

        $columns[] = "Fecha";
        
        $columns[] = "Cliente";

        $columns[] = "Zona";

        $columns[] = "Tipo";

        $columns[] = "Nro Remito";
        
        $columns[] = "Abonado";

        $columns[] = "Detalle";

        $columns[] = "Descripcion";

        $columns[] = "Confirmar";

        $columns[] = "";

        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        $table->setHideSearchBox();

        $table->setHideBuscador();

        $table->setFiltroFechaQuimico();

        $table->setFiltroVentasPendientesQuimico();

        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("quimicos-index");

    }



    public function ventaOnline()
    {

        $this->index(true);

    }


    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------
    public function getRows($exportar = false)
    {

        $fechaVentaQuimico = HDate::sqlDate($_GET['fechaVentaQuimico'] ?: date('d/m/Y'));

        $fechaVentaQuimico = date("Y-m-d", strtotime($fechaVentaQuimico));

        $filtro_ventas_quimico_pendientes = $_GET['venta_quimico_pendiente'];

        #--

        if($filtro_ventas_quimico_pendientes == 'P')
        {
            $query = VentaQuimico::whereRaw('abonado = "N"');
        }else{
            $query = VentaQuimico::whereRaw(
                "(fecha >= ? AND fecha <= ?)", 
                [
                   $fechaVentaQuimico ." 00:00:00", 
                   $fechaVentaQuimico ." 23:59:59"
                ]
            );         
        }
     

        $count = $query->count();

        $result = $query->paginate($this->x_page);

        $data = null;

        if($filtro_ventas_quimico_pendientes != 'P')
        {
            // ========== Calculo de montos Efectivo/Transferencia  ============
            $query_lq = VentaQuimico::whereRaw(
                "(fecha >= ? AND fecha <= ?)", 
                [
            $fechaVentaQuimico ." 00:00:00", 
            $fechaVentaQuimico ." 23:59:59"
                ]
            );

            $data_venta_quimicos = $query_lq->join("linea_venta_quimico", "linea_venta_quimico.id_venta_quimico", '=', 'venta_quimicos.id_venta_quimico');

            $count = $query_lq->count();
            $result_lq = $query_lq->paginate($this->x_page);

            $monto_efectivo_publico = 0;
            $monto_efectivo_mayorista = 0;
            $monto_transferencia_publico = 0;
            $monto_transferencia_mayorista = 0;
            $monto_tarjeta = 0;


            // suma de montos efectivo/transf
            foreach ($result_lq as $index => $data_venta_quimicos)
            {      
            if($data_venta_quimicos->id_concepto == '1' && $data_venta_quimicos->abonado == 'S')
            {
                $monto_efectivo_publico += $data_venta_quimicos->subtotal;
            }else{
                if($data_venta_quimicos->id_concepto == '13'  && $data_venta_quimicos->abonado == 'S'){
                    $monto_transferencia_publico += $data_venta_quimicos->subtotal;
                }
                if($data_venta_quimicos->id_concepto == '36'  && $data_venta_quimicos->abonado == 'S'){
                    $monto_tarjeta += $data_venta_quimicos->subtotal;
                }
                if($data_venta_quimicos->id_concepto == '44'  && $data_venta_quimicos->abonado == 'S'){
                    $monto_transferencia_mayorista += $data_venta_quimicos->subtotal;
                }
                if($data_venta_quimicos->id_concepto == '47'  && $data_venta_quimicos->abonado == 'S'){
                    $monto_efectivo_mayorista += $data_venta_quimicos->subtotal;
                }
            }
            }


            // ** Efectivo Publico **
            $data .= "<tr id=''>";

                $data .= "<td></td>";

                $data .= "<td>{$fechaVentaQuimico}</td>";

                $data .= "<td>-</td>";
                
                $data .= "<td>-</td>";
                
                $data .= "<td>-</td>";

                $data .= "<td>-</td>";
                
                $data .= "<td>$ {$monto_efectivo_publico}</td>";
                
                $data .= "<td> <p>Monto efectivo publico abonado</p></td>";
                $data .= "<td>-</td>";

                $data .= "<td>-</td>";

            $data .= "</tr>";

            // ** Efectivo Mayorista **
            $data .= "<tr id=''>";

                $data .= "<td></td>";

                $data .= "<td>{$fechaVentaQuimico}</td>";

                $data .= "<td>-</td>";
                
                $data .= "<td>-</td>";
                
                $data .= "<td>-</td>";

                $data .= "<td>-</td>";
                
                $data .= "<td>$ {$monto_efectivo_mayorista}</td>";
                
                $data .= "<td> <p>Monto efectivo mayorista abonado</p></td>";
                $data .= "<td>-</td>";

                $data .= "<td>-</td>";

            $data .= "</tr>";

            // ** Transferencia publica **
            $data .= "<tr id=''>";

                $data .= "<td></td>";

                $data .= "<td>{$fechaVentaQuimico}</td>";

                $data .= "<td>-</td>";
                
                $data .= "<td>-</td>";
                
                $data .= "<td>-</td>";

                $data .= "<td>-</td>";
                
                $data .= "<td>$ {$monto_transferencia_publico}</td>";

                $data .= "<td> <p>Monto transferencia publico abonado</p></td>";
                $data .= "<td>-</td>";

                $data .= "<td>-</td>";

            $data .= "</tr>";

            // ** Transferencia mayorista **
            $data .= "<tr id=''>";

                $data .= "<td></td>";

                $data .= "<td>{$fechaVentaQuimico}</td>";

                $data .= "<td>-</td>";
                
                $data .= "<td>-</td>";
                
                $data .= "<td>-</td>";

                $data .= "<td>-</td>";
                
                $data .= "<td>$ {$monto_transferencia_mayorista}</td>";

                $data .= "<td> <p>Monto transferencia mayorista abonado</p></td>";
                $data .= "<td>-</td>";

                $data .= "<td>-</td>";

            $data .= "</tr>";

            // ** Tarjeta **
            $data .= "<tr id=''>";

                $data .= "<td></td>";

                $data .= "<td>{$fechaVentaQuimico}</td>";

                $data .= "<td>-</td>";
                
                $data .= "<td>-</td>";
                
                $data .= "<td>-</td>";

                $data .= "<td>-</td>";
                
                $data .= "<td>$ {$monto_tarjeta}</td>";

                $data .= "<td> <p>Monto tarjeta abonado</p></td>";
                $data .= "<td>-</td>";

                $data .= "<td>-</td>";

            $data .= "</tr>";

            // ========== Fin Calculo de montos - quimicos ============
        }
        foreach ($result as $index => $ventas_quimicos)
        {           
            $tipo = 'No especificado';
            $zona = '-';
            
            switch ($ventas_quimicos->tipo) {
                case 'N':
                    $tipo = 'No especificado';
                    break;
                case 'P':
                    $tipo = 'Publico';
                    break;
                case 'M':
                    $tipo = 'Mayorista';
                    break;
                default:
                    $tipo = 'No especificado';
            }
            
            switch ($ventas_quimicos->zona) {
                case 'S':
                    $zona = 'Santiago';
                    break;
                case 'M':
                    $zona = 'San miguel';
                    break;
                case 'T':
                    $zona = 'Tuc. Sur';
                    break;
                default:
                    $zona = '-';
            } 
            
            // busqueda en linea_venta_quimicos
            $lvq = LineaVentaQuimico::where('id_venta_quimico', $ventas_quimicos->id_venta_quimico)->get();

            $clase = ($ventas_quimicos->abonado == 'N') ? 'background-color:#f46464' : '';

                $data .= "<tr style='" . $clase . "' id='" . ($id = $ventas_quimicos->id_venta_quimico) . "'>";

                    $data .= "<td>{$ventas_quimicos->id_venta_quimico}</td>";

                    $data .= "<td>{$ventas_quimicos->fecha}</td>";

                    $data .= "<td>{$ventas_quimicos->cliente}</td>";

                    $data .= "<td>{$zona}</td>";

                    $data .= "<td>{$tipo}</td>";

                    if($ventas_quimicos->nro_remito == 0)
                    {
                        $data .= "<td>-</td>";
                    }else{
                        $data .= "<td>{$ventas_quimicos->nro_remito}</td>";
                    }


                    $data .= "<td>{$ventas_quimicos->abonado}</td>";

                    $data .= "<td>";

                    foreach ($lvq as $index => $lvq_item)
                    { 
                        $data .= "<div style='font-size:12px;font-weight:600'>";

                        $data .= Concepto::get($lvq_item->id_concepto) . "<i class='pull-right'>$ " . Facturacion::numberFormat($lvq_item->subtotal) . "</i>";
        
                        $data .= "</div>";
                        
                    }

                    $data .= "</td>";

                    $data .= "<td>{$ventas_quimicos->observaciones}</td>";

                    if($ventas_quimicos->abonado == 'N')
                    {
                        $data .= "<td><a href='javascript:void(0)' onclick='confirmar_venta_quimico(this)'><i class='fa fa-chevron-down'></i></a></td>";
                    }
                    else{
                        $data .= "<td><i class='fa fa-chevron-down' disabled></i></a></td>";
                    }

                    #--
                    // if ( $this->es_admin )
                    // {
                        $data .= "<td><a href='javascript:void(0)' onclick='get_modal_form_editar_venta_quimico($ventas_quimicos)'><i class='fa fa-edit'></i></a>";
                        $data .= "<a href='javascript:void(0)' onclick='dt_delete_venta_quimico(this)'><i class='fa fa-trash-alt text-danger'></i></a>";

                    // }

                $data .= "</tr>";

        }

        $data .= "<tr class='not' data-count='{$count}'><td colspan='12'>{$this->replaceLinks($result->links())}</td></tr>";


         #--
         if ( self::isXhrRequest() )

         {
 
             die($data);
 
         }
 
         return $data;

    }

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------
    public function historialArticulos($print = false)
    {
        $prm['id_local'] = floatval($_POST['id_local']);

        $prm['tipo'] = trim($_POST['operacion']);

        $prm['search'] = trim($_POST['search']);

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

                $where[] = "`id_sucursal` = '{$id_local}'";

                $where[] = "`tipo` LIKE '{$tipo}'";

                $where[] = $whereUsuario;

                $where[] = "`id_venta_quimico` <> {$exc}";

                //$where[] = "`cliente` LIKE '%{$search}%'";

                $where[] = "DATE_FORMAT(`fecha_hora`,'%Y-%m-%d %H') BETWEEN '{$fecha} {$h_dsd}' AND '{$fecha} {$h_hst}'";

                #--

                $sql->whereRaw(implode(" AND ", $where))->where(function ($sql1) use ($where, $search) {

                    //unset($where[4]);

                    $sql1->where("cliente", "LIKE", "%{$search}%")->orWhereHas("hasCliente", function ($sql2) use ($search) {

                        $sql2->whereRaw("(`dni` LIKE '{$search}%' OR `nombre` LIKE '%{$search}%' OR `apellido` LIKE '%{$search}%')");

                    });

                });

            })->orderBy('id_venta_quimico', "DESC");

        }

        #--

        if ( ($num_rows_quimicos = $query->count()) && preg_match("#" . Venta::tpVenta . "#i", $tipo) )

        {

            $ids_venta = implode("','", $query->pluck('id_venta_quimico')->toArray());

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

        if ( !$control->id && $num_rows_quimicos )

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

        $rows_quimicos = "";

        //if ( $print )

        {

            $head = $lineas[0]->atributo . ": " . Local::nombreLocal($lineas[0]->valor) . " => " . Local::nombreLocal($lineas[0]->flag);

            //$rows_quimicos .= "<h3 style='position: absolute'>" . implode(" => ", array_filter($head)) . "</h3>";

        }

        foreach ($lineas as $i => $linea)

        {

            if ( ($lineas[$i - 1]->id_venta_quimico != $linea->id_venta_quimico) && ($venta = $linea->hasVenta) )

            {

                $css1 = "display:inline-block;vertical-align: middle;";

                $rows_quimicos .= "<tr style='background: #ffe709;font-weight: 600'>";

                $rows_quimicos .= "<td>";

                $rows_quimicos .= "<a href='!" . self::class . "/modalForm?n={$linea->id_venta_quimico}' target='_blank'><i class='fa fa-file-pdf'></i></a>";

                $rows_quimicos .= "#{$linea->id_venta_quimico}. {$venta->fecha}";

                if ( $venta->es_presupuesto )

                {

                    $rows_quimicos .= "&nbsp;&nbsp;<a href='" . self::PANEL_URI . "/ventas/form/{$venta->id_venta_quimico}'><i class='fa fa-edit'></i></a>";

                }

                $head = $linea->atributo . ": " . Local::nombreLocal($linea->valor) . " => " . Local::nombreLocal($linea->flag);

                $caption = $venta->nombre_cliente;

                #--

                $rows_quimicos .= "<p style='font-style:italic;font-size:11px;line-height:10px;margin:0'>" . str_ireplace($search, "<mark style='text-transform: uppercase'>{$search}</mark>", $caption) . "&nbsp;</p>";

                $rows_quimicos .= "</td>";

                $rows_quimicos .= "<td class='text-right' colspan='2'>";

                if ( !$venta->es_traspaso )

                {

                    /*$rows_quimicos .= "<div class='amount'>";

                    $rows_quimicos .= "<a href='!" . self::class . "/modalForm?n={$linea->id_venta_quimico}' target='_blank'><i class='fa fa-file-pdf'></i></a>";

                    $rows_quimicos .= "&nbsp;$ " . $venta->total;

                    $rows_quimicos .= "</div>";*/

                    #--

                    $rows_quimicos .= "<span style='font-size:10px'>" . str_ireplace("|", "<br />", $venta->str_pagos) . "</span>&nbsp;&nbsp;";

                    //$rows_quimicos .= "<a href='!" . self::class . "/modalForm?n={$linea->id_venta_quimico}' target='_blank'><i class='fa fa-file-pdf'></i></a>";

                }

                else

                {

                    //$rows_quimicos .= "<td></td>";

                }

                $rows_quimicos .= "</td></tr>";

            }

            $rows_quimicos .= "<tr>";

            //$rows_quimicos .= "<td>{$linea->producto}</td>";

            $rows_quimicos .= "<td>{$linea->hasArticulo->item}</td>";

            $rows_quimicos .= "<td class='text-center'>{$linea->cantidad}</td>";

            if ( $venta && !$venta->es_traspaso )

            {

                $rows_quimicos .= "<td class='amount'>" . HFunctions::formatPrice($linea->subtotal) . "</td>";

            }

            $rows_quimicos .= "</tr>";

        }

        #--

        //if ( $arr_total )

        {

            $rows_quimicos .= "<tr><td colspan='3' align='right' id='pp-totales'>" . implode('<br/>', $arr_total) . "</td></tr>";

        }

        #--

        if ( !$print )

        {

            $rows_quimicos .= "<tr class='not'>";

            $rows_quimicos .= "<td colspan='3' id='td-count-{$num_rows_quimicos}'>";

            $rows_quimicos .= $num_rows_quimicos ? $this->replaceLinks($lineas->links()) : "<div class='text-center'>Sin informaci&oacute;n</div>";

            $rows_quimicos .= "</td>";

            $rows_quimicos .= "</tr>";

            #-- 22/06/2022 -- para que no se bloquee

            if ( in_array($this->admin_user->id_usuario, [2, 13]) )

            {

                $blocked = 0;

            }

            HArray::jsonResponse(['rows_quimicos' => $rows_quimicos, 'blocked' => $blocked]);

        }

        return $rows_quimicos;

    }
    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------
    protected function _getLineas()
    {

        return (array)json_decode(file_get_contents($this->file_name), true);

    }


    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------
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

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------
    private function _lineaVentaQuimico($id_producto = null, $operacion = null, $id_linea = null)
    {

        $rows_quimicos = null;

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

                    $rows_quimicos['key'] = $index;

                    $rows_quimicos['add'] += $cantidad;

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

                $rows_quimicos .= "<tr class='trow' id='{$index}' rel='" . ($subtotal / $cantidad) . "&{$cantidad}'>";

                $rows_quimicos .= "<td align='center' class='col-md-2'><a href='javascript:void(0)' class='pull-left' onclick='seleccionar(\"{$index}&{$linea['cup']}\",true)'><i class='fa fa-edit'></i></a> {$cantidad}</td>";

                $rows_quimicos .= "<td><a href='javascript:void(0)' title='quitar' onclick='eliminar(\"{$index}\")'><i class='fa fa-trash text-danger'></i></a>&nbsp;{$articulo}</td>";

                $rows_quimicos .= "<td align='right' class='amount col-md-2'>" . number_format($subtotal, 2, '.', '') . "</td>";

                $rows_quimicos .= "</tr>";

                $total += $subtotal;

            }

        }

        return $rows_quimicos;

    }

    // ----------------------------------------------------------------
    // Controla el modal de nueva venta quimico
    // ----------------------------------------------------------------
    public function modalForm()
    {
        ob_start();

        $form_title = "Nueva Venta";

        $this->setPageTitle($form_title);


        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">
                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarVenta" id="nuevo-gasto-form" autocomplete="off" method="post">
                    <div class="form-group">

                         <!-- **** -->
                         <div class="col-md-12 ">
                            <label>Fecha : </label>
                            <input id="fecha" name="fecha" class="form-control" name="fecha" type="date"></input>
                        </div>
                        <!-- **** -->
                        <div class="col-md-12 ">
                            <label for="descripcion">Cliente : </label>
                            <input id="cliente" name="cliente" class="form-control" name="cliente"></input>
                        </div>
                         <!-- **** -->
                         <div class="col-md-12 ">
                            <label for="nro_remito">Nro remito : </label>
                            <input id="nro_remito" name="nro_remito" class="form-control" name="nro_remito"></input>
                        </div>
                         <!-- **** -->
                         <div class="col-md-12">
                                <label for="zona">Zona : </label>                                
                                    <select id="zona" name="zona" class="form-control">
                                        <option value="N">No especificado</option>
                                        <option value="M">San miguel</option>
                                        <option value="S">Santiago</option>
                                        <option value="T">Tucuman sur</option>
                                    </select>
                        </div>
                        <!-- **** -->

                        <!-- ================== Tipos de pago ===================== -->
                            <!-- ** Tipo pago 1 ** -->
                            <div>
                                <div class="col-md-6">
                                    <label for="tipo_pago_1">Tipo de pago/Concepto Nro. 1 : </label>                                
                                    <select id="tipo_pago_1" name="tipo_pago_1" class="form-control">
                                        <option value="0">No especificado</option>
                                        <option value="13">Transferencia publica</option>
                                        <option value="44">Transferencia mayorista</option>
                                        <option value="1">Efectivo - Publico</option>
                                        <option value="47">Efectivo - Mayorista</option>
                                        <option value="36">Tarjeta</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="monto_1">Monto Nro. 1 : </label>
                                    <input id="monto_1" name="monto_1" class="form-control" name="monto_1" value="0"></input>
                                </div>

                            </div>
                            <!-- **** -->
                            <!-- ** Tipo pago 2 ** -->
                            <div>
                                <div class="col-md-6">
                                    <label for="tipo_pago_2">Tipo de pago/Concepto Nro. 2 : </label>                                
                                    <select id="tipo_pago_2" name="tipo_pago_2" class="form-control" name="tipo_pago_2">
                                        <option value="0">No especificado</option>
                                        <option value="13">Transferencia publica</option>
                                        <option value="44">Transferencia mayorista</option>
                                        <option value="1">Efectivo - Publico</option>
                                        <option value="47">Efectivo - Mayorista</option>
                                        <option value="36">Tarjeta</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="monto_2">Monto Nro. 2 : </label>
                                    <input id="monto_2" name="monto_2" class="form-control" name="monto_2" value="0"></input>
                                </div>
                            </div>
                            <!-- **** -->
                            <!-- ** Tipo pago 3 ** -->
                            <div>
                                <div class="col-md-6">
                                    <label for="tipo_pago_3">Tipo de pago/Concepto Nro. 3 : </label>                                
                                    <select id="tipo_pago_3" name="tipo_pago_3" class="form-control" name="tipo_pago_3">
                                        <option value="0">No especificado</option>
                                        <option value="13">Transferencia publica</option>
                                        <option value="44">Transferencia mayorista</option>
                                        <option value="1">Efectivo - Publico</option>
                                        <option value="47">Efectivo - Mayorista</option>
                                        <option value="36">Tarjeta</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="monto_3">Monto Nro. 3 : </label>
                                    <input id="monto_3" name="monto_3" class="form-control" name="monto_3" value="0"></input>
                                </div>
                            </div>
                            <!-- **** -->
                            <!-- ================== Fin Tipos de pago ===================== -->
                            <div class="col-md-6">
                                <label for="abonado">Abonado : </label>
                                <select id="abonado" name="abonado" class="form-control" name="abonado">
                                    <option value="S">Si</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                          <!-- **** -->
                          <div>
                            <div class="col-md-6">
                                <label for="abonado">Tipo : </label>
                                <select id="tipo" name="tipo" class="form-control" name="tipo">
                                    <option value="S">No especificado</option>
                                    <option value="P">Publico</option>
                                    <option value="M">Mayorista</option>
                                </select>
                            </div>
                        <!-- **** -->
                        <div class="col-md-12 form-group">
                            <label for="descripcion">Descripcion : </label>
                            <textarea id="descripcion" name="descripcion" class="form-control" name="descripcion"></textarea>
                        </div>

                        <div class="col-md-12 text-right">    
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </form>
                <!-- ===== Fin form ===== -->
            </div>
        </div>

        <script type="text/javascript">
            var fechaActual = new Date().toISOString().split('T')[0];
            document.getElementById("fecha").value = fechaActual;
        </script>

        <?php

        $this->setBlockModal(ob_get_clean());

    }



    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------
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

        $list .= $this->_lineaVentaQuimico(null, "traspaso");

        $list .= "</tbody>";

        $list .= "</table>";

        ExportOpts::exportar($list, true);

    }


    // ----------------------------------------------------------------
    //   Almacena una nueva venta de quimico
    // ----------------------------------------------------------------
    public function guardarVenta()
    {

        $quimico = new VentaQuimico;
        
        if(!$_POST['fecha'])
        {
            $quimico->fecha = date("Y-m-d H:i:s");
        }else{
            $fecha = $_POST['fecha']; // Fecha inicial

            $hora_actual = date("H:i:s"); // Obtiene la hora actual en formato de 24 horas

            // Combina la fecha y la hora actual
            $fecha_con_hora = $fecha . " " . $hora_actual;

            // Crea un objeto DateTime utilizando la fecha con la hora actual
            $datetime = new DateTime($fecha_con_hora);

            // Puedes formatear la fecha y hora según tus necesidades
            $fecha_con_hora_formateada = $datetime->format("Y-m-d H:i:s");

            $quimico->fecha = $fecha_con_hora_formateada;
        }

        $quimico->cliente = $_POST['cliente'];                
        $quimico->abonado = $_POST['abonado'];
        $quimico->tipo = $_POST['tipo'];
        $quimico->zona = $_POST['zona'];

        if($_POST['nro_remito'])
        {
            $quimico->nro_remito = $_POST['nro_remito'];
        }else{
            $quimico->nro_remito = '0';
        }

        $quimico->monto = $_POST['monto_1'] + $_POST['monto_2'] + $_POST['monto_3'];
        $quimico->observaciones = $_POST['descripcion'];        
        $quimico->created_at = date("Y-m-d H:i:s");
        $quimico->save();

        if($_POST['monto_1'] != 0)
        {
            $linea_venta_quimico_1 = new LineaVentaQuimico;
            $linea_venta_quimico_1->id_venta_quimico = $quimico->id_venta_quimico;
            $linea_venta_quimico_1->subtotal = $_POST['monto_1'];
            $linea_venta_quimico_1->id_concepto = $_POST['tipo_pago_1'];
            $linea_venta_quimico_1->save();

        }

        if($_POST['monto_2'] != 0)
        {
            $linea_venta_quimico_2 = new LineaVentaQuimico;
            $linea_venta_quimico_2->id_venta_quimico = $quimico->id_venta_quimico;
            $linea_venta_quimico_2->subtotal = $_POST['monto_2'];
            $linea_venta_quimico_2->id_concepto = $_POST['tipo_pago_2'];
            $linea_venta_quimico_2->save();

        }

        if($_POST['monto_3'] != 0)
        {
            $linea_venta_quimico_3 = new LineaVentaQuimico;
            $linea_venta_quimico_3->id_venta_quimico = $quimico->id_venta_quimico;
            $linea_venta_quimico_3->subtotal = $_POST['monto_3'];
            $linea_venta_quimico_3->id_concepto = $_POST['tipo_pago_3'];
            $linea_venta_quimico_3->save();

        }
      
        $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'Venta guardado con exito';

        $url = '/ls-admin/venta-quimicos';

        header('Location: '.$url);


    }

    // ----------------------------------------------------------------
    // Abre un modal para editar una venta quimico
    // ----------------------------------------------------------------
    public function edicionVentaQuimico()
    {
        $id_venta_quimico = $_POST['id_venta_quimico'];

        // id linea venta quimico
        $id_linea_venta_quimico_1 = LineaVentaQuimico::where('id_venta_quimico', $id_venta_quimico)->first()->idlinea_venta_quimico;
        $id_linea_venta_quimico_2 = LineaVentaQuimico::where('id_venta_quimico', $id_venta_quimico)->skip(1)->take(1)->get()[0]->idlinea_venta_quimico;
        $id_linea_venta_quimico_3 = LineaVentaQuimico::where('id_venta_quimico', $id_venta_quimico)->skip(2)->take(1)->get()[0]->idlinea_venta_quimico;
        // Montos
        $monto_1 = LineaVentaQuimico::where('id_venta_quimico', $id_venta_quimico)->first()->subtotal;
        $monto_2 = LineaVentaQuimico::where('id_venta_quimico', $id_venta_quimico)->skip(1)->take(1)->get()[0]->subtotal;
        $monto_3 = LineaVentaQuimico::where('id_venta_quimico', $id_venta_quimico)->skip(2)->take(1)->get()[0]->subtotal;

        // Tipos de pago
        $tipo_pago_1 = LineaVentaQuimico::where('id_venta_quimico', $id_venta_quimico)->first()->id_concepto;
        $tipo_pago_2 = LineaVentaQuimico::where('id_venta_quimico', $id_venta_quimico)->skip(1)->take(1)->get()[0]->id_concepto;
        $tipo_pago_3 = LineaVentaQuimico::where('id_venta_quimico', $id_venta_quimico)->skip(2)->take(1)->get()[0]->id_concepto;


        if(!$monto_1)
        {
            $monto_1 = 0;
        }

        if(!$monto_2)
        {
            $monto_2 = 0;
        }

        if(!$monto_3)
        {
            $monto_3 = 0;
        }

        //
        if(!$tipo_pago_1)
        {
            $tipo_pago_1 = 0;
        }

        if(!$tipo_pago_2)
        {
            $tipo_pago_2 = 0;
        }

        if(!$tipo_pago_3)
        {
            $tipo_pago_3 = 0;
        }

        // 
        $fecha = $_POST['fecha'];
        $cliente = $_POST['cliente'];
        $abonado = $_POST['abonado'];
        $tipo = $_POST['tipo'];
        $zona = $_POST['zona'];
        $nro_remito = $_POST['nro_remito'];
        $observaciones = $_POST['observaciones'];

        // 
        $form_title = "Edicion venta quimico - " . $id_venta_quimico;

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarEditarVenta" id="editar-venta-quimico-form" autocomplete="off" method="post">
                    <div class="form-group">

                        <!-- **** -->
                            <input type="hidden" name="id_linea_venta_quimico_1" value="<?= $id_linea_venta_quimico_1 ?>">
                            <input type="hidden" name="id_linea_venta_quimico_2" value="<?= $id_linea_venta_quimico_2 ?>">
                            <input type="hidden" name="id_linea_venta_quimico_3" value="<?= $id_linea_venta_quimico_3 ?>">
                        <!-- *** -->

                        <input type="hidden" name="id_venta_quimico" value="<?= $id_venta_quimico ?>">

                         <!-- **** -->
                         <div class="col-md-12 ">
                            <label for="descripcion">Fecha : </label>
                            <input id="fecha" name="fecha" class="form-control hasDatepicker" name="fecha" type="date" value="<?= $fecha ?>"></input>
                        </div>
                        <!-- **** -->
                        <div class="col-md-12 ">
                            <label for="descripcion">Cliente : </label>
                            <input id="cliente" name="cliente" class="form-control" name="cliente" value="<?= $cliente ?>"></input>
                        </div>
                         <!-- **** -->
                         <div class="col-md-12 ">
                            <label for="nro_remito">Nro Remito : </label>
                            <input id="nro_remito" name="nro_remito" class="form-control" name="nro_remito" value="<?= $nro_remito ?>"></input>
                        </div>

                        <!-- ** Zona ** -->
                       <!-- **** -->
                       <div class="col-md-12">
                                <label for="zona">Zona : </label>                                
                                    <select id="zona" name="zona" class="form-control">
                                        <option value="N"<?= $zona == 'N' ? ' selected="selected"' : '';?>>No especificado</option>
                                        <option value="M"<?= $zona == 'M' ? ' selected="selected"' : '';?>>San miguel</option>
                                        <option value="S"<?= $zona == 'S' ? ' selected="selected"' : '';?>>Santiago</option>
                                        <option value="T"<?= $zona == 'T' ? ' selected="selected"' : '';?>>Tucuman sur</option>
                                    </select>
                        </div>
                        <!-- **** -->
                        <div class="input-group mb-3">

                            <!-- ================== Tipos de pago ===================== -->
                            <!-- ** Tipo pago 1 ** -->
                            <div>
                                <div class="col-md-6">
                                    <label for="tipo_pago_1">Tipo de pago/Concepto Nro. 1 : </label>                                
                                    <select id="tipo_pago_1" name="tipo_pago_1" class="form-control" name="tipo_pago_1">
                                        <option value="0"<?= $tipo_pago_1 == '0' ? ' selected="selected"' : '';?>>No especificado</option>
                                        <option value="13"<?= $tipo_pago_1 == '13' ? ' selected="selected"' : '';?>>Transferencia publica</option>
                                        <option value="44"<?= $tipo_pago_1 == '44' ? ' selected="selected"' : '';?>>Transferencia mayorista</option>
                                        <option value="1"<?= $tipo_pago_1 == '1' ? ' selected="selected"' : '';?>>Efectivo - Publico</option>
                                        <option value="47"<?= $tipo_pago_1 == '47' ? ' selected="selected"' : '';?>>Efectivo - Mayorista</option>
                                        <option value="36"<?= $tipo_pago_1 == '36' ? ' selected="selected"' : '';?>>Tarjeta</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="monto_1">Monto Nro. 1 : </label>
                                    <input id="monto_1" name="monto_1" class="form-control" name="monto_1" value="<?= $monto_1 ?>"></input>
                                </div>

                            </div>
                            <!-- **** -->
                            <!-- ** Tipo pago 2 ** -->
                            <div>
                                <div class="col-md-6">
                                    <label for="tipo_pago_2">Tipo de pago/Concepto Nro. 2 : </label>                                
                                    <select id="tipo_pago_1" name="tipo_pago_2" class="form-control" name="tipo_pago_2">
                                        <option value="0"<?= $tipo_pago_2 == '0' ? ' selected="selected"' : '';?>>No especificado</option>
                                        <option value="13"<?= $tipo_pago_2 == '13' ? ' selected="selected"' : '';?>>Transferencia publica</option>
                                        <option value="44"<?= $tipo_pago_2 == '44' ? ' selected="selected"' : '';?>>Transferencia mayorista</option>
                                        <option value="1"<?= $tipo_pago_2 == '1' ? ' selected="selected"' : '';?>>Efectivo - Publico</option>
                                        <option value="47"<?= $tipo_pago_2 == '47' ? ' selected="selected"' : '';?>>Efectivo - Mayorista</option>
                                        <option value="36"<?= $tipo_pago_2 == '36' ? ' selected="selected"' : '';?>>Tarjeta</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="monto_2">Monto Nro. 2 : </label>
                                    <input id="monto_2" name="monto_2" class="form-control" name="monto_2" value="<?= $monto_2 ?>"></input>
                                </div>
                            </div>
                            <!-- **** -->
                            <!-- ** Tipo pago 3 ** -->
                            <div>
                                <div class="col-md-6">
                                    <label for="tipo_pago_3">Tipo de pago/Concepto Nro. 3 : </label>                                
                                    <select id="tipo_pago_1" name="tipo_pago_3" class="form-control" name="tipo_pago_3">
                                        <option value="0"<?= $tipo_pago_3 == '0' ? ' selected="selected"' : '';?>>No especificado</option>
                                        <option value="13"<?= $tipo_pago_3 == '13' ? ' selected="selected"' : '';?>>Transferencia publica</option>
                                        <option value="44"<?= $tipo_pago_3 == '44' ? ' selected="selected"' : '';?>>Transferencia mayorista</option>
                                        <option value="1"<?= $tipo_pago_3 == '1' ? ' selected="selected"' : '';?>>Efectivo - Publico</option>
                                        <option value="47"<?= $tipo_pago_1 == '47' ? ' selected="selected"' : '';?>>Efectivo - Mayorista</option>
                                        <option value="36"<?= $tipo_pago_3 == '36' ? ' selected="selected"' : '';?>>Tarjeta</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="monto_3">Monto Nro. 3 : </label>
                                    <input id="monto_3" name="monto_3" class="form-control" name="monto_3" value="<?= $monto_3 ?>"></input>
                                </div>
                            </div>
                            <!-- **** -->
                            <!-- ================== Fin Tipos de pago ===================== -->
                        </div>
                        <!-- **** -->
                        <div>
                            <div class="col-md-6">
                                <label for="abonado">Abonado : </label>
                                <select id="abonado" name="abonado" class="form-control" name="abonado">
                                    <option value="S"<?= $abonado == 'S' ? ' selected="selected"' : '';?>>Si</option>
                                    <option value="N"<?= $abonado == 'N' ? ' selected="selected"' : '';?>>No</option>
                                </select>
                            </div>
                        </div>
                         <!-- **** -->
                         <div>
                            <div class="col-md-6">
                                <label for="abonado">Tipo : </label>
                                <select id="tipo" name="tipo" class="form-control" name="tipo">
                                    <option value="N"<?= $tipo == 'N' ? ' selected="selected"' : '';?>>No especificado</option>
                                    <option value="P"<?= $tipo == 'P' ? ' selected="selected"' : '';?>>Publico</option>
                                    <option value="M"<?= $tipo == 'M' ? ' selected="selected"' : '';?>>Mayorista</option>
                                </select>
                            </div>
                        </div>
                        <!-- **** -->
                        <div class="col-md-12 form-group">
                            <label for="descripcion">Descripcion : </label>
                            <textarea id="descripcion" name="descripcion" class="form-control" name="descripcion">
                                <?= $observaciones ?>
                            </textarea>
                        </div>

                        <div class="col-md-12 text-right">    
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </form>
                <!-- ===== Fin form ===== -->
                </div>
            </div>
        </div>

        <?php

        $this->setBlockModal(ob_get_clean());

    }
    // ****************************************************************
    // Update venta quimico
    // ****************************************************************
    public function guardarEditarVenta()
    {

        $id_venta_quimico = $_POST['id_venta_quimico'];
        $fecha = $_POST['fecha'];
        $cliente = $_POST['cliente'];
        // 1
        $monto_1 = $_POST['monto_1'];
        $id_concepto_1 = $_POST['tipo_pago_1'];
        // 2
        $monto_2 = $_POST['monto_2'];
        $id_concepto_2 = $_POST['tipo_pago_2'];
        // 3
        $monto_3 = $_POST['monto_3'];
        $id_concepto_3 = $_POST['tipo_pago_3'];
        //
        $abonado = $_POST['abonado'];
        $observaciones = $_POST['descripcion'];
        $tipo = $_POST['tipo'];
        $zona = $_POST['zona'];


        // 1
        if($_POST['id_linea_venta_quimico_1']){
            $lvq_quimico_1 = LineaVentaQuimico::find($_POST['id_linea_venta_quimico_1']);
            $lvq_quimico_1->subtotal = $monto_1;
            $lvq_quimico_1->id_concepto = $id_concepto_1;
            $lvq_quimico_1->save();
        }else{
            if($_POST['monto_1'] != 0)
            {
                $linea_venta_quimico_1 = new LineaVentaQuimico;
                $linea_venta_quimico_1->id_venta_quimico = $id_venta_quimico;
                $linea_venta_quimico_1->subtotal = $_POST['monto_1'];
                $linea_venta_quimico_1->id_concepto = $_POST['tipo_pago_1'];
                $linea_venta_quimico_1->save();

            }
        }
        // 2
        if($_POST['id_linea_venta_quimico_2']){

            $lvq_quimico_2 = LineaVentaQuimico::find($_POST['id_linea_venta_quimico_2']);
            $lvq_quimico_2->subtotal = $monto_2;
            $lvq_quimico_2->id_concepto = $id_concepto_2;
            $lvq_quimico_2->save();
        }else{
            if($_POST['monto_2'] != 0)
            {
                $linea_venta_quimico_2 = new LineaVentaQuimico;
                $linea_venta_quimico_2->id_venta_quimico = $id_venta_quimico;
                $linea_venta_quimico_2->subtotal = $_POST['monto_2'];
                $linea_venta_quimico_2->id_concepto = $_POST['tipo_pago_2'];
                $linea_venta_quimico_2->save();

            }
        }
        // 3
        if($_POST['id_linea_venta_quimico_3']){

            $lvq_quimico_3 = LineaVentaQuimico::find($_POST['id_linea_venta_quimico_3']);

            $lvq_quimico_3->subtotal = $monto_3;
            $lvq_quimico_3->id_concepto = $id_concepto_3;
            $lvq_quimico_3->save();
        }else{
            if($_POST['monto_3'] != 0)
            {
                $linea_venta_quimico_3 = new LineaVentaQuimico;
                $linea_venta_quimico_3->id_venta_quimico = $id_venta_quimico;
                $linea_venta_quimico_3->subtotal = $_POST['monto_3'];
                $linea_venta_quimico_3->id_concepto = $_POST['tipo_pago_3'];
                $linea_venta_quimico_3->save();

            }
        }
        //
        $quimico = VentaQuimico::find($id_venta_quimico);

        $quimico->cliente = $cliente;
	
        if($fecha){
            $quimico->fecha = $fecha;
        }

        
        if($_POST['nro_remito'])
        {
            $quimico->nro_remito = $_POST['nro_remito'];
        }else{
            $quimico->nro_remito = '0';
        }

        $quimico->abonado = $abonado;
        $quimico->tipo = $tipo;
        $quimico->zona = $zona;
        $quimico->observaciones = $observaciones;
        $quimico->updated_at = date("Y-m-d H:i:s");

        try {
            // Page code
            $quimico->save();
        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
                
      
        $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'Venta guardado con exito';

        $url = '/ls-admin/venta-quimicos';

        header('Location: '.$url);

    }

    // ****************************************************************
    //
    // ****************************************************************
    public function confirmar_venta_quimico()
    {

        $id_venta_quimico = $_POST['id'];

        $quimico = VentaQuimico::find($id_venta_quimico);
        
        $quimico->abonado = "S";

        $quimico->updated_at = date("Y-m-d H:i:s");

        $quimico->save();
      
        $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'Venta guardado con exito';

        // HArray::jsonResponse($json,null,false);

        $url = '/ls-admin/venta-quimicos';

        header('Location: '.$url);

        // $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        // $json['ok'] = 1;

        // HArray::jsonResponse($json);

    }

    // ****************************************************************
    // eliminar venta quimico
    // ****************************************************************
    public function eliminarVentaQuimico()
    {

        $id_venta_quimico = $_POST['id'];

        $quimico = VentaQuimico::find($id_venta_quimico);

        $quimico->delete();
      
        $this->_setLineas(null, true);

        // HArray::jsonResponse($json,null,false);

        $url = '/ls-admin/venta-quimicos';

        header('Location: '.$url);

        // $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        // $json['ok'] = 1;

        // HArray::jsonResponse($json);

    }

    

}