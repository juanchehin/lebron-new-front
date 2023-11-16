<?php


class AdminCustomers extends AdminMain

{

    const SES_LV = "linea_venta";

    private $file_name;


    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuClientes);

        if ( !in_array($this->admin_user->id_usuario, [1, 30, 28,43]) )

        {

            $url = '/ls-admin';

            header('Location: '.$url);

        }

    }



    public function index($online = false)

    {

        $this->setPageTitle("Clientes");

        // $this->setBotonNuevoRedirectNuevaVenta();

        $this->setBotonNuevoCliente("Alta cliente");

        $columns[] = "#";

        $columns[] = "Nro Doc";
        
        $columns[] = "Apellido";
        
        $columns[] = "Nombre";

        $columns[] = "Email";

        $columns[] = "Direccion";

        $columns[] = "Telefono";

        $columns[] = "Celular";

        $columns[] = "Observaciones";

        $columns[] = "";

        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        // $table->setSearchBoxCC();

        $table->setHideSearchBox();

        $table->setHideBuscador();

        $table->setFiltroBusquedaClienteDniApellidoNombre();

        $table->clean_filter();

        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("customers-index");

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

        $search_dni = trim($_GET['search_cliente_dni']);
        $search_apellido_nombre = trim($_GET['search_cliente_apellido_nombre']);

        $query = Persona::whereRaw(
            "(borrado = ?)", [ 0 ]
        );

        if($search_dni)
        {
            $query = $query->where('dni', 'LIKE', "%{$search_dni}%");
        }

        if($search_apellido_nombre)
        {
            $query = $query->where('apellido', 'LIKE', "%{$search_apellido_nombre}%")->orWhere('nombre', 'LIKE', "%{$search_apellido_nombre}%");
        }

        $query = $query->orderBy("id", "DESC");
    
        $count = $query->count();

        $result = $query->paginate($this->x_page);

        $data = null;

        foreach ($result as $cliente)
        {

                $data .= "<tr id='" . ($id = $cliente->id) . "'>";

                    $data .= "<td>{$cliente->id}</td>";
                
                    $data .= "<td>{$cliente->dni}</td>";

                    $data .= "<td>{$cliente->apellido}</td>";

                    $data .= "<td>{$cliente->nombre}</td>";

                    $data .= "<td>{$cliente->email}</td>";

                    $data .= "<td>{$cliente->direccion}</td>";

                    $data .= "<td>{$cliente->telefono}</td>";

                    $data .= "<td>{$cliente->celular}</td>";

                    $data .= "<td>{$cliente->comentario}</td>";

                    $data .= "<td>
                            
                                <a href='javascript:void(0)' onclick='get_modal_editar_cliente($cliente)'><i class='fa fa-pencil-alt'></i></a>
                                <a href='javascript:void(0)' onclick='dt_delete_cliente($cliente)'><i class='fa fa-trash text-danger'></i></a>

                            </td>";

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

        $form_title = "Alta cliente";

        $this->setPageTitle($form_title);

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">
                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarCliente" autocomplete="off" method="post">
                    <div class="form-group">
                        <input type="hidden" name="id_cliente" value="">
                         <!-- **** -->
                         <div class="col-md-12 ">
                            <label>DNI : </label>
                            <input id="dni_cliente" class="form-control" name="dni_cliente" type="text" value=""></input>
                        </div>
                        <!-- **** -->
                        <div class="col-md-6">
                            <label for="">Apellidos : </label>
                            <input id="apellido_cliente" class="form-control" name="apellido_cliente" value=""></input>
                        </div>

                        <!-- **** -->
                        <div class="col-md-6 ">
                            <label for="">Nombres : </label>
                            <input id="nombre_cliente" name="nombre_cliente" class="form-control" value=""></input>
                        </div>

                        <!-- **** -->
                        <div class="col-md-6 ">
                            <label for="">Email : </label>
                            <input id="email_cliente" name="email_cliente" class="form-control" value=""></input>
                        </div>
                        
                        <!-- **** -->
                        <div class="col-md-6 ">
                            <label for="">Telefono : </label>
                            <input id="telefono_cliente" name="telefono_cliente" class="form-control" value=""></input>
                        </div>

                        <!-- **** -->
                        <div class="col-md-12 ">
                            <label for="">Direccion : </label>
                            <input id="direccion_cliente" name="direccion_cliente" class="form-control" value=""></input>
                        </div>


                        <!-- **** -->
                        <div class="col-md-12 ">
                            <label for="">Observaciones : </label>
                            <textarea id="comentario_cliente" name="comentario_cliente" class="form-control" value=""></textarea>
                        </div>

                        <div class="col-md-12">    
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </form>
                <!-- ===== Fin form ===== -->
            </div>
        </div>

        <?php

        $this->setBlockModal(ob_get_clean());

    }

    // ----------------------------------------------------------------
    // Controla el modal 
    // ----------------------------------------------------------------
    public function modal_editar_cliente()
    {
        ob_start();

        $id_cliente = $_POST['id'];
        $apellido_cliente = $_POST['apellido'];
        $nombre_cliente = $_POST['nombre'];
        $dni_cliente = $_POST['dni'];

        $email_cliente = $_POST['email'];
        $direccion_cliente = $_POST['direccion'];
        $telefono_cliente = $_POST['telefono'];
        $observaciones_cliente = $_POST['comentario'];

        $form_title = "Editar cliente: " . $apellido_cliente . ", " . $nombre_cliente . " - " . $dni_cliente;

        $this->setPageTitle($form_title);

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">
                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarEditarCliente" autocomplete="off" method="post">
                    <div class="form-group">
                        <input type="hidden" name="id_cliente" value="<?= $id_cliente ?>">
                         <!-- **** -->
                         <div class="col-md-12 ">
                            <label>DNI : </label>
                            <input id="dni_cliente" class="form-control" name="dni_cliente" type="text" value="<?= $dni_cliente ?>"></input>
                        </div>
                        <!-- **** -->
                        <div class="col-md-6">
                            <label for="">Apellidos : </label>
                            <input id="apellido_cliente" class="form-control" name="apellido_cliente" value="<?= $apellido_cliente ?>"></input>
                        </div>

                        <!-- **** -->
                        <div class="col-md-6 ">
                            <label for="">Nombres : </label>
                            <input id="nombre_cliente" name="nombre_cliente" class="form-control" value="<?= $nombre_cliente ?>"></input>
                        </div>

                        <!-- **** -->
                        <div class="col-md-6 ">
                            <label for="">Email : </label>
                            <input id="email_cliente" name="email_cliente" class="form-control" value="<?= $email_cliente ?>"></input>
                        </div>

                        <!-- **** -->
                        <div class="col-md-6 ">
                            <label for="descripcion">Telefono : </label>
                            <input id="telefono_cliente" name="telefono_cliente" class="form-control" value="<?= $telefono_cliente ?>"></input>
                        </div>

                        <!-- **** -->
                        <div class="col-md-12 ">
                            <label for="descripcion">Direccion : </label>
                            <input id="direccion_cliente" name="direccion_cliente" class="form-control" value="<?= $direccion_cliente ?>"></input>
                        </div>                        

                        <!-- **** -->
                        <div class="col-md-12 ">
                            <label for="">Observaciones : </label>
                            <input id="observaciones_cliente" name="observaciones_cliente" class="form-control" value="<?= $observaciones_cliente ?>"></input>
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

        <?php

        $this->setBlockModal(ob_get_clean());

    }

    // ----------------------------------------------------------------
    // Controla el modal 
    // ----------------------------------------------------------------
    public function modal_delete_cliente()
    {
        ob_start();

        $form_title = "Eliminar cliente";

        $this->setPageTitle($form_title);

        $id_cliente = $_POST['id'];
        $apellido_cliente = $_POST['apellido'];
        $nombre_cliente = $_POST['nombre'];
        $dni_cliente = $_POST['dni'];

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">
                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/delete_cliente" id="nueva-cuenta-corriente" autocomplete="off" method="post">
                    <div class="form-group">
                        <input type="hidden" name="id_cliente" value="<?= $id_cliente ?>">

                         <!-- **** -->
                         <div class="col-md-12 ">
                            <h4>¿Desea eliminar el cliente  "<?= $apellido_cliente ?>, <?= $nombre_cliente ?>" (DNI: <?= $dni_cliente ?>) ? </h4>
                        </div>
                        <br>

                        <div class="col-md-12 text-right">    
                            <button type="submit" class="btn btn-primary">Aceptar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        </div>

                    </div>
                </form>
                <!-- ===== Fin form ===== -->
            </div>
        </div>

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
    //   Almacena 
    // ----------------------------------------------------------------
    public function guardarCliente()
    {

        $cliente = new Cliente();

        $cliente->dni = $_POST['dni_cliente'];                
        $cliente->apellido = $_POST['apellido_cliente'];
        $cliente->nombre = $_POST['nombre_cliente'];        
        $cliente->email = $_POST['email_cliente']; 

        $cliente->direccion = $_POST['direccion_cliente'];                
        $cliente->telefono = $_POST['telefono_cliente'];
        $cliente->comentario = $_POST['comentario_cliente'];

        $cliente->fecha_registro = date("Y-m-d H:i:s");
        $cliente->save();
      
        $this->_setLineas(null, true);

        $url = '/ls-admin/customers';

        header('Location: '.$url);


    }

    // ****************************************************************
    // Update cuenta
    // ****************************************************************
    public function guardarEditarCliente()
    {

        $id_cliente = $_POST['id_cliente'];
        $dni_cliente = $_POST['dni_cliente'];
        $apellido_cliente = $_POST['apellido_cliente'];
        $nombre_cliente = $_POST['nombre_cliente'];
        $email_cliente = $_POST['email_cliente'];
        $direccion_cliente = $_POST['direccion_cliente'];
        $telefono_cliente = $_POST['telefono_cliente'];
        $comentario = $_POST['observaciones_cliente'];

        //
        $cliente = Cliente::find($id_cliente);

        $cliente->dni = $dni_cliente;
        $cliente->nombre = $nombre_cliente;
        $cliente->apellido = $apellido_cliente;
        $cliente->email = $email_cliente;
        $cliente->direccion = $direccion_cliente;
        $cliente->telefono = $telefono_cliente;
        $cliente->comentario = $comentario;
        $cliente->updated_at = date("Y-m-d H:i:s");

        $cliente->save();
      
        $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $url = '/ls-admin/customers';

        header('Location: '.$url);

    }

    // ****************************************************************
    // eliminar 
    // ****************************************************************
    public function delete_cliente()
    {

        $id_persona = $_POST['id_cliente'];

        if(!$id_persona)
        {
            $url = '/ls-admin/customers';
            header('Location: '.$url);
            return;
        }

        $cliente = Cliente::find($id_persona);
        
        $cliente->borrado = 1;

        $cliente->updated_at = date("Y-m-d H:i:s");

        $cliente->save();
      
        $this->_setLineas(null, true);

        // HArray::jsonResponse($json,null,false);

        $url = '/ls-admin/customers';

        header('Location: '.$url);

        // $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        // $json['ok'] = 1;

        // HArray::jsonResponse($json);

    }

    

}