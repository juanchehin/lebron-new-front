<?php


class AdminCuentasCorrientes extends AdminMain

{

    const SES_LV = "linea_venta";

    private $file_name;


    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuCuentasCorrientes);

        if ( !in_array($this->admin_user->id_usuario, [1, 21, 28,30,44]) )

        {

            $url = '/ls-admin';

            header('Location: '.$url);

        }

    }



    public function index($online = false)

    {

        $this->setPageTitle("Cuentas corrientes");

        // $this->setBotonNuevoRedirectNuevaVenta();

        $this->setBotonNuevo("Nuevo", "javascript:void(0)");

        $columns[] = "#";

        $columns[] = "Fecha";
        
        $columns[] = "Cliente";
        
        $columns[] = "Abonado";

        $columns[] = "Monto";

        $columns[] = "Descripcion";

        $columns[] = "Confirmar";

        $columns[] = "";

        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        $table->setSearchBoxCC();

        $table->setHideSearchBox();

        $table->setHideBuscador();

        $table->setFiltroFechaCuentas();

        $table->setFiltroCuentasPendientes();

        $table->clean_filter();

        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("cuentas-corrientes-index");

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

        $fechaOperacion = HDate::sqlDate($_GET['fechaCuentaCorriente'] ?: date('d/m/Y'));

        $fechaOperacion = date("Y-m-d", strtotime($fechaOperacion));

        $filtro_cuentas_corrientes_pendientes = $_GET['cuentas_pendientes'];
        
        $busqueda_cliente = trim($_GET['search_cliente_cc']);

        if($busqueda_cliente)
        {
            $query = CuentasCorrientes::where('cliente', 'LIKE', "%{$busqueda_cliente}%")::whereRaw('estado_cc = "A"');
         
        }
        else
        {
            if($filtro_cuentas_corrientes_pendientes == 'P')
            {
                $query = CuentasCorrientes::whereRaw('abonado = "N" AND estado_cc = "A"');
            }else{           
                $query = CuentasCorrientes::whereRaw(
                    "(fecha >= ? AND fecha <= ? AND estado_cc = ?)", 
                    [
                        $fechaOperacion ." 00:00:00", 
                        $fechaOperacion ." 23:59:59",
                        "A"
                    ]
                ); 
            }
        }

     

        $count = $query->count();

        $result = $query->paginate($this->x_page);

        $data = null;

        foreach ($result as $index => $cuentas)
        {         
            
            $clase = ($cuentas->abonado == 'N') ? 'background-color:#f46464' : '';

                $data .= "<tr style='" . $clase . "' id='" . ($id = $cuentas->idcuentas_corrientes) . "'>";

                    $data .= "<td>{$cuentas->idcuentas_corrientes}</td>";

                    $data .= "<td>{$cuentas->fecha}</td>";

                    $data .= "<td>{$cuentas->cliente}</td>";

                    $data .= "<td>{$cuentas->abonado}</td>";

                    $data .= "<td>{$cuentas->monto}</td>";

                    $data .= "<td>{$cuentas->observaciones}</td>";

                    if($cuentas->abonado == 'N')
                    {
                        $data .= "<td><a href='javascript:void(0)' onclick='confirmar_cuenta_corriente(this)'><i class='fa fa-chevron-down'></i></a></td>";
                    }
                    else{
                        $data .= "<td><i class='fa fa-chevron-down' disabled></i></a></td>";
                    }

                    #--
                    // if ( $this->es_admin )
                    // {
                        $data .= "<td><a href='javascript:void(0)' onclick='get_modal_form_editar_cuenta($cuentas)'><i class='fa fa-edit'></i></a>";
                        $data .= "<a href='javascript:void(0)' onclick='dt_delete_cuenta_corriente(this)'><i class='fa fa-trash-alt text-danger'></i></a>";

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
    // Controla el modal 
    // ----------------------------------------------------------------
    public function modalForm()
    {
        ob_start();

        $form_title = "Nuevo item";

        $this->setPageTitle($form_title);


        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">
                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarCuentaCorriente" id="nueva-cuenta-corriente" autocomplete="off" method="post">
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

                        <!-- ================== Tipos de pago ===================== -->
                            <!-- ** Tipo pago 1 ** -->
                            <div>
                                <div class="col-md-6">
                                    <label for="tipo_pago">Tipo de pago : </label>                                
                                    <select id="tipo_pago" name="tipo_pago" class="form-control">
                                        <option value="0">No especificado</option>
                                        <option value="13">Transferencia</option>
                                        <option value="1">Efectivo</option>
                                        <option value="36">Tarjeta</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="monto">Monto : </label>
                                    <input id="monto" name="monto" class="form-control" name="monto" value="0"></input>
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
    //   Almacena una nueva 
    // ----------------------------------------------------------------
    public function guardarCuentaCorriente()
    {

        $cuenta_corriente = new CuentasCorrientes();
        
        if(!$_POST['fecha'])
        {
            $cuenta_corriente->fecha = date("Y-m-d H:i:s");
        }else{
            $fecha = $_POST['fecha']; // Fecha inicial

            $hora_actual = date("H:i:s"); // Obtiene la hora actual en formato de 24 horas

            // Combina la fecha y la hora actual
            $fecha_con_hora = $fecha . " " . $hora_actual;

            // Crea un objeto DateTime utilizando la fecha con la hora actual
            $datetime = new DateTime($fecha_con_hora);

            // Puedes formatear la fecha y hora según tus necesidades
            $fecha_con_hora_formateada = $datetime->format("Y-m-d H:i:s");

            $cuenta_corriente->fecha = $fecha_con_hora_formateada;
        }

        $cuenta_corriente->cliente = $_POST['cliente'];                
        $cuenta_corriente->abonado = $_POST['abonado'];
        $cuenta_corriente->monto = $_POST['monto'];        
        $cuenta_corriente->observaciones = $_POST['descripcion'];        
        $cuenta_corriente->created_at = date("Y-m-d H:i:s");
        $cuenta_corriente->save();
      
        $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'Venta guardado con exito';

        $url = '/ls-admin/cuentas-corrientes';

        header('Location: '.$url);


    }

    // ----------------------------------------------------------------
    // Abre un modal para editar
    // ----------------------------------------------------------------
    public function edicionCuentaCorriente()
    {
        $id_cuenta_corriente = $_POST['idcuentas_corrientes'];
        
        // Monto
        $monto = CuentasCorrientes::where('idcuentas_corrientes', $id_cuenta_corriente)->first()->monto;

        // Tipo de pago
        $tipo_pago = CuentasCorrientes::where('idcuentas_corrientes', $id_cuenta_corriente)->first()->id_concepto;

        if(!$monto)
        {
            $monto = 0;
        }
        //
        if(!$tipo_pago)
        {
            $tipo_pago = 0;
        }

        // 
        $fecha = $_POST['fecha'];
        $cliente = $_POST['cliente'];
        $abonado = $_POST['abonado'];
        $observaciones = $_POST['observaciones'];

        // 
        $form_title = "Edicion - " . $id_cuenta_corriente;

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarEditarCuenta" id="editar-venta-quimico-form" autocomplete="off" method="post">
                    <div class="form-group">

                        <input type="hidden" name="id_cuenta_corriente" value="<?= $id_cuenta_corriente ?>">

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

                        <div class="input-group mb-3">

                            <!-- ================== Tipos de pago ===================== -->
                            <!-- ** Tipo pago 1 ** -->
                            <div>
                                <div class="col-md-6">
                                    <label for="tipo_pago_1">Tipo de pago : </label>                                
                                    <select id="tipo_pago_1" name="tipo_pago" class="form-control" name="tipo_pago_1">
                                        <option value="0"<?= $tipo_pago == '0' ? ' selected="selected"' : '';?>>No especificado</option>
                                        <option value="13"<?= $tipo_pago == '13' ? ' selected="selected"' : '';?>>Transferencia</option>
                                        <option value="1"<?= $tipo_pago == '1' ? ' selected="selected"' : '';?>>Efectivo</option>
                                        <option value="36"<?= $tipo_pago == '36' ? ' selected="selected"' : '';?>>Tarjeta</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="monto">Monto : </label>
                                    <input id="monto" name="monto" class="form-control" name="monto" value="<?= $monto ?>"></input>
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
    // Update cuenta
    // ****************************************************************
    public function guardarEditarCuenta()
    {

        $id_cuenta_corriente = $_POST['id_cuenta_corriente'];
        $fecha = $_POST['fecha'];
        $cliente = $_POST['cliente'];
        // 1
        $monto = $_POST['monto'];
        $tipo_pago = $_POST['tipo_pago'];

        //
        $abonado = $_POST['abonado'];
        $observaciones = $_POST['descripcion'];

        //
        $cuenta = CuentasCorrientes::find($id_cuenta_corriente);

        $cuenta->cliente = $cliente;

        if($fecha){
            $cuenta->fecha = $fecha;
        }

        if($monto){
            $cuenta->monto = $monto;
        }

        $cuenta->id_concepto = $tipo_pago;
        $cuenta->abonado = $abonado;
        $cuenta->observaciones = $observaciones;
        $cuenta->updated_at = date("Y-m-d H:i:s");

        $cuenta->save();
      
        $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $url = '/ls-admin/cuentas-corrientes';

        header('Location: '.$url);

    }

    // ****************************************************************
    //
    // ****************************************************************
    public function confirmar_cuenta_corriente()
    {

        $id_cuenta_corriente = $_POST['id'];

        $cuenta_corriente = CuentasCorrientes::find($id_cuenta_corriente);
        
        $cuenta_corriente->abonado = "S";

        $cuenta_corriente->updated_at = date("Y-m-d H:i:s");

        $cuenta_corriente->save();
      
        $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'Guardado con exito';

        // HArray::jsonResponse($json,null,false);

        $url = '/ls-admin/cuentas-corrientes';

        header('Location: '.$url);

        // $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        // $json['ok'] = 1;

        // HArray::jsonResponse($json);

    }

    // ****************************************************************
    // eliminar 
    // ****************************************************************
    public function eliminarFilaCuenta()
    {

        $id_cuenta_corriente = $_POST['id'];

        $cuenta_corriente = CuentasCorrientes::find($id_cuenta_corriente);

        // $cuenta->delete();

        $cuenta_corriente->estado_cc = "B";

        $cuenta_corriente->updated_at = date("Y-m-d H:i:s");

        $cuenta_corriente->save();
      
        $this->_setLineas(null, true);

        // HArray::jsonResponse($json,null,false);

        $url = '/ls-admin/cuentas-corrientes';

        header('Location: '.$url);

        // $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        // $json['ok'] = 1;

        // HArray::jsonResponse($json);

    }

    

}