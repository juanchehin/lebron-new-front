<?php

//ini_set("display_errors", "On");


class AdminVentaMayoristas extends AdminMain

{

    const SES_LV = "lineas_venta_mayorista";

    private static $_thead = array("#Nro", "Fecha y Hora","Comprador", "Condición", "Total", "&nbsp;");



    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuMayoristas);

    }



    public function index()

    {

        $this->setPageTitle("Ventas mayoristas");

        $columns[] = "#";

        $columns[] = "Fecha";

        $columns[] = "Cliente";

        $columns[] = "Direccion";

        $columns[] = "Telefono";

        $columns[] = "Comprobante";

        $columns[] = "Remito";

        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        $table->setHideSearchBox();

        $table->setHideBuscador();

        $table->setFiltroFechaMayorista();
        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("mayoristas-index");

    }


    public function getRows()
    {

        $fechaVentaMayoristas = HDate::sqlDate($_GET['fechaVentaMayorista'] ?: date('d/m/Y'));

        $fechaVentaMayoristas = date("Y-m-d", strtotime($fechaVentaMayoristas));

        #--

        $query = VentasMayorista::whereRaw(
                "(created_at >= ? AND created_at <= ?)", 
                [
                   $fechaVentaMayoristas ." 00:00:00", 
                   $fechaVentaMayoristas ." 23:59:59"
                ]
        );

        $ventas_mayoristas = $query->leftjoin("persona", "ventas_mayoristas.id_cliente", '=', 'persona.id');

        $count = $query->count();

        $result = $query->paginate($this->x_page);

        $data = null;

        foreach ($result as $index => $ventas_mayoristas)
        {                                  

                $data .= "<tr id='" . ($id = $ventas_mayoristas->idventa_mayorista) . "'>";

                    $data .= "<td>{$ventas_mayoristas->idventa_mayorista}</td>";

                    $data .= "<td>{$ventas_mayoristas->created_at}</td>";

                    $data .= "<td>{$ventas_mayoristas->nombre} {$ventas_mayoristas->apellido}</td>";

                    $data .= "<td>{$ventas_mayoristas->direccion_mayorista}</td>";

                    $data .= "<td>{$ventas_mayoristas->telefono}</td>";

                    $data .=  "<td>
                    <a href='" . $_SERVER["HTTP_ORIGIN"] . "/media/uploads/mayoristas/" . "{$ventas_mayoristas->comprobante}' target='_blank'><i class='fa fa-window-maximize'></i></a>
                    </td>"; 

                    $data .= "<td><a href='!AdminVenta/imprimirRemitoMayorista?n={$ventas_mayoristas->idventa_mayorista}' target='_blank'><i class='fa fa-file-pdf'></i></a></td>";

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


    public function modalForm()
    {

        $id_venta_quimico = floatval($_POST['id'] ?: ($id = $_GET['n']));

        $body = "";

        if ( ($venta = Venta::find($id_venta_quimico)) )

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

                $factura->setFacturaEmision(substr($venta->fecha, 0, 10));

                $factura->setFacturaCodigo($venta->es_presupuesto ? "00" : 11);

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

                $factura->setClienteNombre($venta_quimico->cliente);

                $total = 0;

                foreach (($lineas = $venta_quimico->hasLineaVenta) as $linea)

                {

                    $item['cant.'] = $linea->cantidad;

                    $item['detalle'] = $linea->producto;

                    $item['p._unit'] = round($linea->subtotal / $linea->cantidad);

                    $total += ($item['subtotal'] = $linea->subtotal);

                    $factura->setFacturaItems($item);

                }

                //
                $factura->setFacturaItems($item);

                $factura->setFacturaTotal($total);

                $factura->drawFactura();

                die;

            }

            $this->setParams('data', $venta_quimico);

            $body = $this->loadView("admin/venta-detalle");

        }

        HArray::jsonResponse('body', $body);

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



    public function agregarLinea(LineaVenta $linea = null, $op = null)

    {

        //$vta_por_mayor = ($_POST['tipo_venta'] == Aporte::POR_MAYOR);

        $id_producto = floatval($linea->id_producto ?: $_POST['id_producto']);

        $cantidad = intval($linea->cantidad ?: $_POST['cantidad']);

        $operacion = trim($op ?: $_POST['operacion']);

        $codigo = trim($linea->hasArticulo->codigo ?: $_POST['cup']);

        $op_nro = trim($linea->id_venta_quimico ?: $_POST['op_nro']);

        if ( intval($_POST['nro']) && false )

        {

            $res['id_venta_quimico'] = Venta::nextIdVenta();

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

        $res['rows_quimicos'] = ($rows_quimicos = $this->_lineaVentaQuimico(null, $operacion));

        //HArray::jsonResponse($res);

        die($rows_quimicos);

    }



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

        HArray::jsonResponse('ok', true);

    }

    public function updateConceptoMovimiento()
    {

        $id_concepto_nuevo = floatval($_POST['id_concepto']);
        $id_venta_quimico = floatval($_GET['id_venta_quimico']);

        $servername = 'localhost';

        $username = 'root';
        
        $password = '';

        $dbname = 'lebronsu_admin';
    
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sql = "UPDATE movimiento SET id_cuenta =  " . $id_concepto_nuevo . " WHERE id_operacion = " . $id_venta_quimico . ";";

        if ($conn->query($sql) === TRUE) {
            // echo "Actualizacion exitosa";
        } else {
            // echo "Error " . $conn->error;
        }

        $conn->close();

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        HArray::jsonResponse($json);

    }

    public function updateTipoVenta()
    {

        $id_tipo_venta = floatval($_POST['id_tipo_venta']);
        $id_venta_quimico = floatval($_GET['id_venta_quimico']);

        $servername = 'localhost';

        $username = 'root';
        
        $password = '';

        $dbname = 'lebronsu_admin';
    
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        switch ($id_tipo_venta) {

            case 0:    
                $sql = "UPDATE venta SET tipo =  'venta_publico' WHERE id_venta_quimico = " . $id_venta_quimico . ";";    
            break;

            case 1:    
                $sql = "UPDATE venta SET tipo =  'venta_mayorista' WHERE id_venta_quimico = " . $id_venta_quimico . ";";    
            break;

            case 2:    
                $sql = "UPDATE venta SET tipo =  'venta_presupuesto' WHERE id_venta_quimico = " . $id_venta_quimico . ";";    
            break;
            
            case 3:    
                $sql = "UPDATE venta SET tipo =  'compra' WHERE id_venta_quimico = " . $id_venta_quimico . ";";    
            break;    
    
            default:    
                $json['notice'] = "Ocurrio un problema";    
            break;    
    }
    
        

        if ($conn->query($sql) === TRUE) {
            // echo "Actualizacion exitosa";
        } else {
            // echo "Error " . $conn->error;
        }

        $conn->close();       

        HArray::jsonResponse($json);
    }

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

    public function ventaForm($id = null)
    {

        $accion = $this->modulo;

        // if ( $this->current_item == MenuPanel::menuVentas )

        // {

        //     $this->controlPermiso(Permiso::permisoCrear);

        // }

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

        $table->setrows($this->_lineaVentaQuimico(null, $operacion));

        $params['selectLocal'] = $this->_selectLocal(($accion == MenuPanel::menuQuimicos) ? 1 : null);

        // 'selectLocal' => $this->_selectLocal(($accion != MenuPanel::menuVentas) ? 2 : 1),

        // $params['operacion'] = $accion;

        $params['linea_venta'] = $table->drawTable();

        $params['articulos'] = $articulos;

        $params['minDate'] = HDate::modifyDate(date('Y-m-d'), '-3 day', 'd/m/Y');

        // $params['tipos_venta'] = Venta::$_tipoVenta;

        // $params['cuentas'] = Concepto::cuentasPago();

        $params['total_op'] = $venta->total;

        $params['pagoControl'] = PagoControl::pagoForm($pagos);

        #--

        $this->setParams($params);

        $this->setBody("venta-form-quimico");

    }



    public function setEstado()
    {

        $id_venta_quimico = floatval($_POST['id']);

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

        if ( $venta = Venta::find($id_venta_quimico) )

        {

            $venta->{$attr} = $estado;

            $venta->save();

        }

    }



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



    public function eliminar()

    {

        $id_venta_quimico = floatval($_POST['id']);

        if ( $venta = Venta::find($id_venta_quimico) )

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

            //Movimiento::whereRaw("`id_operacion`='{$id_venta_quimico}' AND `modulo` IN('" . Movimiento::moduloCuenta . "','" . Movimiento::moduloStock . "'")->delete();

            $venta->hasPago()->delete();

            $venta->delete();

        }

    }

    public function guardarVenta()
    {

        $quimico = new VentaQuimico;

        $dataVenta = json_decode($_POST['venta']);

        $cadete = $dataVenta->cadete;

        $direccion_envio = $dataVenta->direccion_envio;

        $telefono = $dataVenta->telefono;

        $id_concepto = $dataVenta->id_concepto;

        $quimico->cliente = $_POST['cliente'];

        $quimico->monto = $_POST['total'];

        $quimico->direccion_envio = $direccion_envio;

        $quimico->telefono_envio = $telefono;

        $quimico->id_concepto = $id_concepto;

        $quimico->cadete = $cadete;

        $quimico->created_at = date("Y-m-d H:i:s");

        if ( !($lineas_venta = $this->_getLineas()) )
        {

            HArray::jsonError("A&uacute;n no se agregaron artículos");
        }

        $quimico->save();

        $total = 0;

        #--

        foreach ($lineas_venta as $time => $lv)
        {

            #-- Guardar la línea de venta

            $id_venta_quimico = $quimico->id_venta_quimico;

            $subtotal = floatval($lv['subtotal']);

            $total += $subtotal;

            $cantidad = floatval($lv['cantidad']);


            $linea = new LineaVentaQuimico;

            $linea->id_venta_quimico = $id_venta_quimico;

            $linea->id_producto = $lv['id_producto'];

            $linea->cantidad = $lv['cantidad'];

            $linea->subtotal = $subtotal;

            $linea->save();

            $producto = Articulo::find($lv['id_producto']);

            #--

            if ( $producto )
            {

              $producto->stockUpdate(6, $cantidad, '0',false);


            }

        }

        $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 1;

        HArray::jsonResponse($json);

    }

}