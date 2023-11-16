<?php


class AdminPedidos extends AdminMain

{

    const SES_LV = "linea_venta";

    private $file_name;


    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuRegistro);

        // if ( !in_array($this->admin_user->id_usuario, [1, 21, 28,38,30,47,32]) )

        // {

        //     $url = '/ls-admin';

        //     header('Location: '.$url);

        // }

    }



    public function index($online = false)
    {

        $this->setPageTitle("Pedidos - Compras online");

        // $this->setBotonNuevoRedirectNuevaVenta();

        // $this->setBotonNuevo("Nueva Venta", "javascript:void(0)");

        $columns[] = "#";

        $columns[] = "Fecha";

        $columns[] = "Nro Remito";

        $columns[] = "Payment ID";
        
        $columns[] = "Cliente";

        $columns[] = "Direccion";

        $columns[] = "Celular";

        $columns[] = "Comentario";

        $columns[] = "Monto";


        $columns[] = "Monto mp";

        $columns[] = "Monto trans.";

        $columns[] = "Estado MP";

        $columns[] = "Estado";

        $columns[] = "";


        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        $table->setHideSearchBox();

        $table->setHideBuscador();

        $table->setFiltroFechaPedido();

        $table->setFiltroVentasPendientesPedidos();
        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("pedidos-index");

    }



    public function ventaOnline()
    {

        $this->index(true);

    }


    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------
    public function getRows()
    {

        $fechaPedido = HDate::sqlDate($_GET['fechaVentaPedido'] ?: date('d/m/Y'));

        $fechaPedido = date("Y-m-d", strtotime($fechaPedido));

        $filtro_pedidos_pendientes = $_GET['venta_pedido_pendiente'];

        #--

        if($filtro_pedidos_pendientes == 'P')
        {
            $query = Pedidos::whereRaw('estado_pedido = "P"');
        }else{
            if($filtro_pedidos_pendientes == 'C')
            {
                $query = Pedidos::whereRaw('estado_pedido = "C"');
            }else{
                $query = Pedidos::whereRaw(
                    "(created_at >= ? AND created_at <= ?)", 
                    [
                    $fechaPedido ." 00:00:00", 
                    $fechaPedido ." 23:59:59"
                    ]
                );         
            }      
        }

        $query = $query->leftjoin("componente", "pedidos.id_componente", '=', 'componente.id');
        $query = $query->leftjoin("persona", "pedidos.id_cliente", '=', 'persona.id');    
        
        $count = $query->count();

        $result = $query->paginate($this->x_page);

        $data = null;

        foreach ($result as $index => $pedidos)
        {                           
            $clase = ($pedidos->estado_pedido == 'P') ? 'background-color:#f46464' : '';

                $data .= "<tr style='" . $clase . "' id='" . ($id = $pedidos->id_venta) . "'>";

                    $data .= "<td>{$pedidos->id_pedido}</td>";

                    $data .= "<td>{$pedidos->created_at}</td>";

                    $data .= "<td>{$pedidos->id_venta}</td>";

                    $data .= "<td>{$pedidos->payment_id}</td>";

                    $data .= "<td>{$pedidos->apellido}, {$pedidos->nombre}</td>";

                    $data .= "<td>{$pedidos->direccion}</td>";

                    $data .= "<td>{$pedidos->celular}</td>";

                    $data .= "<td>{$pedidos->comentario}</td>";

                    $data .= "<td>$ " . Facturacion::numberFormat($pedidos->monto_total) . "</td>";

                    $data .= "<td>$ " . Facturacion::numberFormat($pedidos->total_paid_amount) . "</td>";

                    $data .= "<td>$ " . Facturacion::numberFormat($pedidos->transaction_amount) . "</td>";

                    $data .= "<td>{$pedidos->status_mp}</td>";

                    if($pedidos->estado_pedido == 'P')
                    {
                        $data .= "<td>Pendiente</td>";
                    }
                    else{
                        $data .= "<td>Entregado</td>";
                    }

                    if($pedidos->estado_pedido == 'P')
                    {
                        $data .= 
                        "<td>
                                <a href='javascript:void(0)' onclick='get_modal_form_confirmar_pedido($pedidos)'><i class='fa fa-truck'></i></a>
                        </td>";
                    }
                    else{
                        $data .= 
                        "<td >
                                <i class='fa fa-truck' disabled></i>
                        </td>";
                    }


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
    // Abre un modal 
    // ----------------------------------------------------------------
    public function confirmar_pedido_modal()
    {
        $id_pedido = $_POST['id_pedido'];
        
        // 
        $form_title = "Confirmar entrega - " . $id_pedido;

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/confirmar_pedido" id="editar-venta-quimico-form" autocomplete="off" method="post">
                    <div class="form-group">
                        <input type="hidden" name="id_pedido" value="<?= $id_pedido ?>">

                        <p>¿Desea Confirmar pedido?</p>

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
    // confirmar_pedido_
    // ****************************************************************
    public function confirmar_pedido()
    {

        $id_pedido = $_POST['id_pedido'];
      
        //
        $pedido = Pedidos::find($id_pedido);

        $pedido->estado_pedido = 'C';
        $pedido->updated_at = date("Y-m-d H:i:s");

        try {
            // Page code
            $pedido->save();
        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
                
      
        $this->_setLineas(null, true);


        $url = '/ls-admin/ventas/pedidos';

        header('Location: '.$url);

    }


    

}