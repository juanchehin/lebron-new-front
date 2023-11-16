<?php

class AdminInversores extends AdminMain

{

    

    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuInversores);

        if ( !in_array($this->admin_user->id_usuario, [1, 30, 28]) )

        {

            $url = '/ls-admin';

            header('Location: '.$url);

        }
    }



    public function index()
    {

        $this->setPageTitle("Inversores");

        $this->setBotonesInversores("Agregar inversor", "javascript:void(0)");


        $columns[] = "#";

        $columns[] = "Apellidos";

        $columns[] = "Nombres";

        $columns[] = "DNI";

        $columns[] = "Observaciones";

        $columns[] = "Operacion";

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

        $query = Persona::orderBy("id", "DESC");

        $inversores = $query->where("rol",'inversor')->where("borrado",'0');

        $count = $inversores->count();

        $result = $inversores->paginate($this->x_page);

        $data = null;

        foreach ($result as $index => $inversor)
        {                                  

                $data .= "<tr id='" . ($id = $inversor->id) . "'>";

                    $data .= "<td>{$inversor->id}</td>";

                    $data .= "<td>{$inversor->apellido}</td>";

                    $data .= "<td>{$inversor->nombre}</td>";

                    $data .= "<td>{$inversor->dni}</td>";

                    $data .= "<td>{$inversor->comentario}</td>";

                    $data .= "<td>";

                        $data .= "<a href='" . self::PANEL_URI . "/inversores/historico/{$id}' title='Movimientos'><i class='fa fa-list-alt text-warning'></i></a>";

                        $data .= "<a href='javascript:void(0)' onclick='get_modal_form_editar_inversor($inversor)'><i class='fa fa-edit'></i></a>";
                        
                        $data .= "<a href='javascript:void(0)' onclick='get_modal_form_nueva_inversion($inversor)'><i class='fa fa-plus'></i></a>";

                        $data .= "<a href='javascript:void(0)' onclick='get_modal_form_pago_inversion($inversor)'><i class='fa fa-minus'></i></a>";

                        $data .= "<a href='javascript:void(0)' onclick='dt_delete_inversor(this)'><i class='fa fa-trash-alt text-danger'></i></a>";

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

        if($_POST['dni'])
        {
            $inversor->dni =  $_POST['dni'];
        }else{
            $inversor->dni =  null;
        }

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
        $observaciones = $_POST['comentario'];

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
                        <textarea id="observaciones" name="observaciones" class="form-control" name="observaciones">
                                <?= $observaciones ?>
                            </textarea>
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
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $observaciones = $_POST['observaciones'];

        //
        $persona = Persona::find($id_inversor);

        $persona->apellido = $apellidos;
        $persona->nombre = $nombres;
        
        if($_POST['dni'])
        {
            $persona->dni =  $_POST['dni'];
        }else{
            $persona->dni =  null;
        }
        
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
    // Abre un modal para pagar una inversion
    // ----------------------------------------------------------------
    public function pagoInversionForm()
    {
        $id_inversor = $_POST['id'];
        // 
        $apellidos = $_POST['apellido'];
        $nombres = $_POST['nombre'];

        // 
        $form_title = "Pago inversion - " . $apellidos . ", " . $nombres;

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

        <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarPagoInversion" id="nueva-pago-inversion-form" autocomplete="off" method="post">

                    <input type="hidden" name="id_inversor" value="<?= $id_inversor ?>">
            
                    <div class="col-md-6 form-group">
                        <label >Monto: <i class="required"></i></label>
                        <input type="tel" name="monto" class="form-control" id="monto" value="0" min="0" required>                        
                    </div>

                    <div class="col-md-6">
                        <label for="tipo_pago">Tipo de pago: </label>                                
                        <select id="tipo_pago" name="tipo_pago" class="form-control">
                            <option value="0">No especificado</option>
                            <option value="13">Transferencia</option>
                            <option value="1">Efectivo</option>
                            <option value="36">Tarjeta</option>
                            <option value="46">Comisionista</option>
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
        $movimientos_inversores->tipo =  'I';
        $movimientos_inversores->tipo_pago =  $_POST['tipo_pago'];

        //
        $fechaActual = new DateTime();

        $cantidadDias = $_POST['plazo'];

        $fechaActual->modify("+" . $cantidadDias . " days");
        //

        $movimientos_inversores->fecha_vencimiento = $fechaActual->format("Y-m-d");

        $movimientos_inversores->observaciones = $_POST['observaciones'];

        $movimientos_inversores->save();

        $url = '/ls-admin/inversores';

        header('Location: '.$url);

    }

    // ----------------------------------------------------------------
    //  Almacena un pago a un cliente que invirtio en la empresa
    // ----------------------------------------------------------------
    public function guardarPagoInversion()
    {
        $movimientos_inversores = new MovimientosInversores();

        //
        $movimientos_inversores->id_inversor =  $_POST['id_inversor'];
        $movimientos_inversores->monto_inversion = $_POST['monto'];
        $movimientos_inversores->tipo =  'P';
        $movimientos_inversores->tipo_pago =  $_POST['tipo_pago'];
        $movimientos_inversores->observaciones = $_POST['observaciones'];

        $movimientos_inversores->save();

        $url = '/ls-admin/inversores';

        header('Location: '.$url);

    }

    // ****************************************************************
    // Baja inversor
    // ****************************************************************
    public function eliminarInversor()
    {

        $id_inversor = $_POST['id'];

        //
        $persona = Persona::find($id_inversor);

        $persona->borrado = "1";

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
    // Abre un modal para simulador
    // ----------------------------------------------------------------
    public function simulador()
    {

        ob_start();

        ?>

        <div class="panel panel-default">

        <div class="panel-heading" style="padding:5px;text-align: center">Simulador</div>

            <div class="panel-body">
                <!--  -->
                <div class="row">
            
                    <div class="col-md-6 form-group">
                        <label >Capital a invertir: <i class="required"></i></label>
                        <input type="tel" name="monto_inversion" class="form-control" id="monto_inversion" value="1000" min="1000" required>                        
                    </div>

                    <div class="col-md-6 form-group">
                        <label >Rendimiento Porcentual (%):<i class="required"></i></label>
                        <input type="tel" name="rendimiento_porcentual" class="form-control" id="rendimiento_porcentual" value="8" required>
                    </div>

                    <!-- <div class="col-md-6 form-group">
                        <label >Plazo (en dias): </label>
                        <input type="tel" name="plazo" class="form-control" id="plazo" value="30">
                    </div> -->
                </div>
                <div class="row form-group">
                    <div class="col-md-6 form-group">
                         <button onclick="calcularInversion()">Calcular</button>
                    </div>
                </div>
                    <!--  -->
                    <hr>
                
                <div class="row">

                    <div class="col-md-6 form-group">
                        <label >Monto inicial: </label> <div id="calculo_monto_inicial">$0</div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label >Intereses: </label> <div id="calculo_intereses">$0</div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label >Neto a cobrar: </label> <div id="calculo_neto_cobrar">$0</div>
                    </div>
                </div>
               
            </div>
        </div>

        <script>
            const monto_inversion = document.getElementById("monto_inversion");
            const rendimiento_porcentual = document.getElementById("rendimiento_porcentual");
            const plazo = document.getElementById("plazo");

            // monto_inversion.addEventListener("change", () => {
            //     calcularInversion();
            // });

            // rendimiento_porcentual.addEventListener("change", () => {
            //     calcularInversion();
            // });

            // inputElement.addEventListener("change", () => {
            //     calcularInversion();
            // });

            // disparar al modificar cualquier valor de entrada***
            function calcularInversion() {
                var montoInicial = parseFloat(document.getElementById("monto_inversion").value);
                // var cantidadDias = parseInt(document.getElementById("plazo").value);
                var porcentajeGanancias = parseFloat(document.getElementById("rendimiento_porcentual").value);
                
                var ganancia = (montoInicial * porcentajeGanancias) / 100;
                var montoTotal = montoInicial + ganancia;
                
                // document.getElementById("calculo_neto_cobrar").textContent = "Monto total después de " + cantidadDias + " días: $" + montoTotal.toFixed(2);
                document.getElementById("calculo_neto_cobrar").textContent = "$ " + montoTotal.toFixed(2);
                document.getElementById("calculo_intereses").textContent = "$ " + ganancia.toFixed(2);
                document.getElementById("calculo_monto_inicial").textContent = "$ " + montoInicial.toFixed(2);
            }
        </script>


        <?php

        $this->setBlockModal(ob_get_clean());

    }

    // ----------------------------------------------------------------
    // Abre un modal para simulador simulador
    // ----------------------------------------------------------------
    public function simulador_compuesta()
    {

        ob_start();

        ?>

        <div class="panel panel-default">

        <div class="panel-heading" style="padding:5px;text-align: center">Simulador - Inversion compuesta</div>

            <div class="panel-body">
                <!--  -->
                <div class="row">
            
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
                </div>
                <div class="row form-group">
                    <div class="col-md-6 form-group">
                         <button onclick="calcularInversionCompuesta()">Calcular</button>
                    </div>
                </div>
                    <!--  -->
                    <p class="small"><i class="required"></i>Interés se capitaliza diariamente (n = 365)</p>
                <hr>
                <div class="row">

                    <div class="col-md-6 form-group">
                        <label >Monto inicial: </label> <div id="calculo_monto_inicial">$0</div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label >Intereses: </label> <div id="calculo_intereses">$0</div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label >Neto a cobrar: </label> <div id="calculo_neto_cobrar">$0</div>
                    </div>
                </div>
               
            </div>
        </div>

        <script>
            const monto_inversion_compuesta = document.getElementById("monto_inversion");
            const rendimiento_porcentual_compuesta = document.getElementById("rendimiento_porcentual");
            const plazo_compuesta = document.getElementById("plazo");

            // disparar al modificar cualquier valor de entrada***
            function calcularInversionCompuesta() {
                const montoInicial = parseFloat(document.getElementById("monto_inversion").value);
                const tasaInteresAnual = parseFloat(document.getElementById("rendimiento_porcentual").value) / 100;
                const duracionDias = parseInt(document.getElementById("plazo").value);
                
                // Supongamos que el interés se capitaliza diariamente (n = 365).
                const n = 365;
                const t = duracionDias / 365;
                
                const ganancia = montoInicial * Math.pow(1 + (tasaInteresAnual / n), n * t) - montoInicial;
                const calculo_neto_cobrar = montoInicial + ganancia;
                
                document.getElementById("calculo_neto_cobrar").textContent = "$ " + calculo_neto_cobrar.toFixed(2);
                document.getElementById("calculo_intereses").textContent = "$ " + ganancia.toFixed(2);
                document.getElementById("calculo_monto_inicial").textContent = "$ " + montoInicial.toFixed(2);
            }
        </script>


        <?php

        $this->setBlockModal(ob_get_clean());

    }


}