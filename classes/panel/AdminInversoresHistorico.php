<?php

class AdminInversoresHistorico extends AdminMain

{

    

    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuInversores);
    }



    public function index()
    {
         // $id_inversor = ;
         $url = $_SERVER['PATH_INFO'];

         // Encontrar la última barra en la URL
         $lastSlashPosition = strrpos($url, "/");
 
         // Extraer la parte de la URL después de la última barra
         $numberPart = substr($url, $lastSlashPosition + 1);
 
         // Convertir el número de cadena a entero
         $id_inversor = intval($numberPart);

         $inversor = Persona::find($id_inversor);

        $apellido = $inversor->apellido;

        $nombre = $inversor->nombre;
         

        $this->setPageTitle("Historico de " . $apellido . ", " . $nombre);

        // $this->setBotonesInversores("Agregar inversor", "javascript:void(0)");

        $columns[] = "#";

        $columns[] = "Monto inversion";

        $columns[] = "Tipo";

        $columns[] = "Tipo pago";

        $columns[] = "Fecha Alta";
        
        $columns[] = "Fecha venciminto";

        $columns[] = "Observaciones";

        $columns[] = "";


        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        $table->setHideSearchBox();

        $table->setHideBuscador();

        // $table->setFiltroFecha();
        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("inversores-index");

    }


    // ----------------------------------------------------------------
    // Controla el modal de nuevo inversor
    // ----------------------------------------------------------------
    public function getRows()
    {
        #--

        $query = MovimientosInversores::orderBy("idmovimientos_inversores", "DESC");

        // $id_inversor = ;
        $url = $_SERVER['HTTP_REFERER'];

        // Encontrar la última barra en la URL
        $lastSlashPosition = strrpos($url, "/");

        // Extraer la parte de la URL después de la última barra
        $numberPart = substr($url, $lastSlashPosition + 1);

        // Convertir el número de cadena a entero
        $id_inversor = intval($numberPart);


        $movimientos_inv = $query->where("estado_mi",'A')->where("id_inversor",$id_inversor);

        $count = $movimientos_inv->count();

        $result = $movimientos_inv->paginate($this->x_page);

        $data = null;

        foreach ($result as $index => $movimiento)
        {

                switch ($movimiento->tipo) {
                    case 'I':
                        $tipo = "Inversion";
                        break;
                    case 'P':
                        $tipo = "Pago";
                        break;
                    default:
                        $tipo = "No especificado";
                }

                switch ($movimiento->tipo_pago) {
                    case 0:
                        $tipo_pago = "No especificado";
                        break;
                    case 1:
                        $tipo_pago = "Efectivo";
                        break;
                    case 13:
                        $tipo_pago = "Transferencia";
                        break;
                    case 36:
                        $tipo_pago = "Tarjeta";
                        break;
                    case 46:
                        $tipo_pago = "Comisionista";
                        break;
                    default:
                        $tipo_pago = "No especificado";
                }

                $data .= "<tr id='" . ($id = $movimiento->id) . "'>";

                    $data .= "<td>{$movimiento->idmovimientos_inversores}</td>";

                    $data .= "<td>{$movimiento->monto_inversion}</td>";

                    $data .= "<td>{$tipo}</td>";

                    $data .= "<td>{$tipo_pago}</td>";

                    $data .= "<td>{$movimiento->created_at}</td>";

                    if($movimiento->fecha_vencimiento)
                    {
                        $data .= "<td>{$movimiento->fecha_vencimiento}</td>";
                    }else{
                        $data .= "<td>-</td>";
                    }


                    $data .= "<td>{$movimiento->observaciones}</td>";

                    $data .= "<td>";

                        $data .= "<a href='javascript:void(0)' onclick='dt_delete_inversion($movimiento->idmovimientos_inversores)'><i class='fa fa-trash-alt text-danger'></i></a>";

                    $data .= "</td>";

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
    // Controla el modal de nuevo inversor
    // ----------------------------------------------------------------
    public function modalForm()
    {

        $form_title = "Nuevo Inversor";

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarInversor" id="nuevo-inversor-form" autocomplete="off" method="post">
               
                    <div class="col-md-6 form-group">

                        <label >Apellidos <i class="required"></i></label>
                        <input type="text" name="apellidos" class="form-control" id="apellidos" value="" required>
                        
                    </div>

                    <div class="col-md-6 form-group">

                        <label >Nombres <i class="required"></i></label>
                        <input type="text" name="nombres" class="form-control" id="nombres" value="" required>
                        
                    </div>

                    <div class="col-md-6 form-group">

                        <label >DNI </label>
                        <input type="tel" name="dni" class="form-control" id="dni" value="">

                    </div>

                    <div class="col-md-6 form-group">

                        <label >Direccion </label>
                        <input type="text" name="direccion" class="form-control" id="direccion" value="">

                    </div>

                    <div class="col-md-6 form-group">

                        <label >Telefono </label>
                        <input type="telefono" name="telefono" class="form-control" id="telefono" value="">

                    </div>

                    <div class="col-md-6 form-group">

                        <label >Email </label>
                        <input type="email" name="email" class="form-control" id="email" value="">

                    </div>

                    <div class="col-md-4 form-group">                      

                        </div>
                            <div class="col-md-12 form-group">
                            <label>Observaciones</label>
                            <textarea id="observaciones" name="observaciones" class="form-control" name="observaciones"></textarea>
                        </div>

                        <div class="col-md-12 text-right">    
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <?php

        $this->setBlockModal(ob_get_clean());

    }
    // ----------------------------------------------------------------
    // Fin * Controla el modal
    // ----------------------------------------------------------------

    public function guardarInversor()
    {

        $inversor = new Persona;


        $inversor->apellido =  $_POST['apellidos'];

        $inversor->nombre = $_POST['nombres'];

        $inversor->dni =  $_POST['dni'];

        $inversor->telefono = $_POST['telefono'];

        $inversor->direccion = $_POST['direccion'];

        $inversor->email = $_POST['email'];

        $inversor->comentario = $_POST['descripcion'];

        $inversor->rol = "inversor";

        $inversor->fecha_registro = date("Y-m-d H:i:s");

        $inversor->save();

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'Inversor guardado con exito';

        // HArray::jsonResponse($json,null,false);

        $url = '/ls-admin/inversores';

        header('Location: '.$url);



    }

    // ----------------------------------------------------------------
    // Abre un modal para editar
    // ----------------------------------------------------------------
    public function edicionInversor()
    {
        $id_inversor = $_POST['id'];
        // 
        $apellidos = $_POST['apellido'];
        $nombres = $_POST['nombre'];
        $dni = $_POST['dni'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $observaciones = $_POST['observaciones'];

        // 
        $form_title = "Edicion inversor - " . $id_inversor;

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

        <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

        <div class="panel-body">

            <!-- ===== form ===== -->
            <form action="!<?= self::class ?>/guardarEditarInversor" id="editar-inversor-form" autocomplete="off" method="post">

                <input type="hidden" name="id_inversor" value="<?= $id_inversor ?>">
        
                <div class="col-md-6 form-group">

                    <label >Apellidos <i class="required"></i></label>
                    <input type="text" name="apellidos" class="form-control" id="apellidos" value="<?= $apellidos ?>" required>
                    
                </div>

                <div class="col-md-6 form-group">

                    <label >Nombres <i class="required"></i></label>
                    <input type="text" name="nombres" class="form-control" id="nombres" value="<?= $nombres ?>" required>
                    
                </div>

                <div class="col-md-6 form-group">

                    <label >DNI </label>
                    <input type="tel" name="dni" class="form-control" id="dni" value="<?= $dni ?>">

                </div>

                <div class="col-md-6 form-group">

                    <label >Direccion </label>
                    <input type="text" name="direccion" class="form-control" id="direccion" value="<?= $direccion ?>">

                </div>

                <div class="col-md-6 form-group">

                    <label >Telefono </label>
                    <input type="telefono" name="telefono" class="form-control" id="telefono" value="<?= $telefono ?>">

                </div>

                <div class="col-md-6 form-group">

                    <label >Email </label>
                    <input type="email" name="email" class="form-control" id="email" value="<?= $email ?>">

                </div>

                <div class="col-md-4 form-group">                      

                    </div>
                        <div class="col-md-12 form-group">
                        <label>Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control" name="observaciones" value="<?= $observaciones ?>"></textarea>
                    </div>

                    <div class="col-md-12 text-right">    
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>

                </div>
            </form>
        </div>
        </div>

        <?php

        $this->setBlockModal(ob_get_clean());

    }
    // ****************************************************************
    // Update inversor
    // ****************************************************************
    public function guardarEditarInversor()
    {

        $id_inversor = $_POST['id_inversor'];
        // 
        $apellidos = $_POST['apellidos'];
        $nombres = $_POST['nombres'];
        $dni = $_POST['dni'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $observaciones = $_POST['observaciones'];

        //
        $persona = Persona::find($id_inversor);

        $persona->apellido = $apellidos;
        $persona->nombre = $nombres;
        $persona->dni = $dni;
        $persona->direccion = $direccion;
        $persona->email = $email;
        $persona->telefono = $telefono;
        $persona->comentario = $observaciones;

        $persona->save();
      
        // $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'Venta guardado con exito';

        $url = '/ls-admin/inversores';

        header('Location: '.$url);

    }

    // ----------------------------------------------------------------
    // Abre un modal para nueva inversion
    // ----------------------------------------------------------------
    public function nuevaInversion()
    {
        $id_inversor = $_POST['id'];
        // 
        $apellidos = $_POST['apellido'];
        $nombres = $_POST['nombre'];

        // 
        $form_title = "Nueva inversion - " . $apellidos . ", " . $nombres;

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

        <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarInversion" id="nueva-inversion-form" autocomplete="off" method="post">

                    <input type="hidden" name="id_inversor" value="<?= $id_inversor ?>">
            
                    <div class="col-md-6 form-group">
                        <label >Capital a invertir: <i class="required"></i></label>
                        <input type="tel" name="monto_inversion" class="form-control" id="monto_inversion" value="1000" min="1000" required>                        
                    </div>

                    <div class="col-md-6 form-group">
                        <label >Rendimiento Porcentual (%):<i class="required"></i></label>
                        <input type="tel" name="rendimiento_porcentual" class="form-control" id="rendimiento_porcentual" value="8" required>
                    </div>

                    <div class="col-md-6 form-group">
                        <label >Plazo (en dias): </label>
                        <input type="tel" name="plazo" class="form-control" id="plazo" value="30">
                    </div>

                    <div class="col-md-6">
                        <label for="tipo_pago">Tipo de pago: </label>                                
                        <select id="tipo_pago" name="tipo_pago_1" class="form-control">
                            <option value="0">No especificado</option>
                            <option value="13">Transferencia</option>
                            <option value="1">Efectivo</option>
                            <option value="36">Tarjeta</option>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        </div>
                            <div class="col-md-12 form-group">
                            <label>Observaciones:</label>
                            <textarea id="observaciones" name="observaciones" class="form-control" name="observaciones" value=""></textarea>
                        </div>

                        <div class="col-md-12 text-right">    
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <?php

        $this->setBlockModal(ob_get_clean());

    }

     // ----------------------------------------------------------------
    //  Almacena una nueva inversion para un inversor
    // ----------------------------------------------------------------

    public function guardarInversion()
    {
        $movimientos_inversores = new MovimientosInversores();

        //
        $movimientos_inversores->id_inversor =  $_POST['id_inversor'];
        $movimientos_inversores->monto_inversion = $_POST['monto_inversion'];
        $movimientos_inversores->tipo_pago =  $_POST['tipo_pago'];

        //
        $fechaActual = new DateTime();

        $cantidadDias = $_POST['plazo'];

        $fechaActual->modify("+" . $cantidadDias . " days");
        //

        $movimientos_inversores->fecha_vencimiento = $fechaActual->format("Y-m-d");;

        $movimientos_inversores->observaciones = $_POST['observaciones'];

        $movimientos_inversores->save();

        $url = '/ls-admin/inversores';

        header('Location: '.$url);

    }

    // ****************************************************************
    // Baja inversion
    // ****************************************************************
    public function eliminarInversion()
    {

        $id_inversion = $_POST['idmovimientos_inversores'];

        //
        $mov_inv = MovimientosInversores::find($id_inversion);

        $mov_inv->estado_mi = "B";

        $mov_inv->save();


        $url = '/ls-admin/inversores';

        header('Location: '.$url);

    }



}