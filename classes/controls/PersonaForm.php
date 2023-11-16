<?php



class PersonaForm

{

    const REQUIRED = "<i class='required'>&nbsp;</i>";

    private $es_modal = false;

    private $es_usuario = true;

    private $puede_editar = true;

    private $form_action;

    private $back_url;

    private $data;

    private $email_required = true;

    private $dni_required = false;

    private $dob_required = false;

    private $tipos_usuario = array();

    private $otros_datos = null;

    private $nuevo_registro = false;



    /**

     * @param bool $es_modal

     */

    public function setEsModal()

    {

        $this->es_modal = true;

        return;

    }



    /**

     * @param bool $es_usuario

     */

    public function setNoEsUsuario()

    {

        $this->es_usuario = false;

        return;

    }



    /**

     * @param bool $nuevo_registro

     */

    public function setNuevoRegistro()

    {

        $this->nuevo_registro = true;

        return;

    }



    /**

     * @param array $tipos_usuario

     */

    public function setTiposUsuario(array $tipos_usuario)

    {

        $this->tipos_usuario = $tipos_usuario;

        return;

    }



    /**

     * @param bool $puede_editar

     */

    public function setPuedeEditar($puede_editar)

    {

        $this->puede_editar = $puede_editar;

    }



    /**

     * @param !Clase/metodo $form_action

     */

    public function setFormAction($form_action)

    {

        $this->form_action = $form_action;

        return;

    }



    /**

     * @param bool $email_required

     */

    public function setEmailNoRequired()

    {

        $this->email_required = false;

        return;

    }



    /**

     * @param bool $dni_required

     */

    public function setDniRequired()

    {

        $this->dni_required = true;

        return;

    }



    /**

     * @param bool $dob_required

     */

    public function setDobRequired()

    {

        $this->dob_required = true;

        return;

    }



    /**

     * @param mixed $back_url

     */

    public function setBackUrl($back_url)

    {

        $this->back_url = $back_url;

        return;

    }



    /**

     * @param Collection $data

     */

    public function setData($data)

    {

        $this->data = $data;

        return;

    }



    /**

     * @param string $otros_datos

     */

    public function setOtrosDatos($otros_datos)

    {

        $this->otros_datos = $otros_datos;

        return;

    }



    public static function validarCuit($_cuit)

    {

        $cuit = preg_replace("#[^\d+]#", "", (string)$_cuit);

        if ( strlen($cuit) != 11 )

        {

            return false;

        }

        $acumulado = 0;

        $digitos = str_split($cuit);

        $digito = array_pop($digitos);



        for ($i = 0; $i < count($digitos); $i++)

        {

            $acumulado += $digitos[9 - $i] * (2 + ($i % 6));

        }

        $verif = 11 - ($acumulado % 11);

        $verif = $verif == 11 ? 0 : $verif;



        return ($digito == $verif) ? floatval($cuit) : null;

    }



    public function drawForm($submit = true)

    {

        ob_start();

        $persona = $this->data;

        if ( $this->es_usuario )

        {

            $usuario = $this->data;

            $persona = $usuario->hasPersona;

        }

        ?>

        <div class="panel panel-<?= $this->es_modal ? "default" : "info"; ?>">

            <?php if ( $this->es_modal ) : ?>

                <div class="panel-heading">

                    <?= $persona ? "Datos de \"{$persona->nombre_apellido}\"" : "Nuevo registro"; ?>

                </div>

            <?php endif; ?>

            <div class="panel-body">

                <form action="<?= $this->form_action ?>" id="frm-persona" autocomplete="off">

                    <?php if ( $usuario ) : ?>

                        <input type="hidden" name="id_usuario" value="<?= $usuario->id_usuario ?>"/>

                    <?php endif; ?>

                    <input type="hidden" name="id_persona" value="<?= $persona->id ?>"/>

                    <div class="row">

                        <div class="col-md-6 form-group" id="name-group">

                            <label for="nombre">Nombre <?= self::REQUIRED //.string                                      ?></label>

                            <input type="text" maxlength="40" name="nombre" id="nombre" class="string form-control" value="<?= $persona->nombre ?>" autofocus required>

                        </div>

                        <div class="col-md-6 form-group" id="last-group">

                            <label for="apellido">Apellido <?= self::REQUIRED // .string                                      ?></label>

                            <input type="text" maxlength="25" name="apellido" id="apellido" class="string form-control" value="<?= $persona->apellido ?>" required>

                        </div>

                    </div>

                    <div class="row" id="row-2">

                        <div class="col-md-6 form-group" id="id-group" data-required="<?= $this->dni_required ?>">

                            <label for="dni">DNI / CUIT | CUIL</label>

                            <?php if ( $this->puede_editar ) : ?>

                                <div class="input-group-addon" style="width:.7%">

                                    <select name="tipo_id" id="tipo-id" class="form-control" rel="<?=strlen($dni=$persona->dni)?>">

                                        <option value="">Tipo</option>

                                        <?php foreach ([1 => "DNI", 2 => "CUIT/CUIL"] as $k => $item): ?>

                                            <option value="<?= $k ?>"><?= $item ?></option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="input-group-addon">

                                    <input type="tel" maxlength="8" id="dni" name="dni" class="form-control" disabled>

                                </div>

                            <?php else : ?>

                                <div class="form-control"><?= $persona->dni ?></div>

                            <?php endif; ?>

                        </div>

                        <div class="col-md-6 form-group" id="dob-group" data-required="<?= $this->dob_required ?>">

                            <label for="dob">Fecha de Nacimiento</label>

                            <input type="text" name="dob" id="dob" class="form-control" value="<?= $persona->dob ?>">

                        </div>



                        <div class="col-md-6 form-group" id="genre-group">

                            <label>G&eacute;nero <?= self::REQUIRED ?></label>

                            <h5 class="input-group">

                                <?php foreach (Persona::$_GENEROS as $k => $value) : ?>

                                    <input type="radio" id="<?= $k ?>" name="genero" value="<?= $k ?>" required><label for="<?= $k ?>"><?= $value ?></label>&nbsp;

                                <?php endforeach; ?>

                            </h5>

                        </div>

                        <div class="col-md-6 form-group" id="email-group" data-required="<?= $this->email_required ?>">

                            <label for="correo">Correo electr&oacute;nico</label>

                            <?php if ( $persona->email && false ) : ?>

                                <div class="form-control"><?= $persona->email ?></div>

                            <?php else : ?>

                                <input type="text" name="correo" id="correo" class="form-control" value="<?= $persona->email ?>"/>

                            <?php endif; ?>

                        </div>

                    </div>

                    <div class="row" id="row-4">

                        <div class="col-md-6 form-group">

                            <label for="direccion">Direcci&oacute;n</label>

                            <input type="text" name="direccion" id="direccion" value="<?= $persona->direccion ?>" class="form-control">

                        </div>

                        <div class="col-md-6 form-group">

                            <div class="input-group-addon" <?= $css = 'style="text-align:left;background:none;padding:2px 0;border:none"' ?>>

                                <label for="telefono">Tel&eacute;fono</label>

                                <input type="tel" id="telefono" class="form-control" name="telefono" maxlength="14" value="<?= $persona->telefono ?>">

                            </div>

                            <div class="input-group-addon" <?= $css ?>>

                                <label for="celular">Celular</label>

                                <input type="tel" id="celular" class="form-control" name="celular" maxlength="14" value="<?= $persona->celular ?>">

                            </div>

                        </div>

                        <div class="col-md-6 form-group">

                        <label for="zona">Zona</label>
                            <select name="zona" id="zona" class="form-control" rel="">
                                <option value="N">Sin especificar</option>
                                <option value="S">Santiago</option>
                                <option value="T">Tucumán Sur</option>
                                <option value="G">Gimnasio y centro</option>
                            </select>                        
                        </div>
                        
                    </div>

                    <?php if ( $this->es_usuario ) : ?>

                        <div class="row">

                            <div class="col-md-6 form-group">

                                <label for="tipo_usuario">Tipo de Usuario <?= self::REQUIRED ?></label>

                                <?php if ( $this->puede_editar && $this->tipos_usuario ) : ?>

                                    <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>

                                        <option value="">Seleccionar</option>

                                        <?php foreach ($this->tipos_usuario as $tipo => $label) : ?>

                                            <option value="<?= $tipo ?>" <?= ($tipo == $usuario->tipo_usuario) ? "selected" : "" ?>><?= $label ?></option>

                                        <?php endforeach; ?>

                                    </select>

                                <?php else : ?>

                                    <div class="form-control"><?= $usuario->rol ?></div>

                                    <input type="hidden" name="tipo_usuario" value="<?= $usuario->tipo_usuario ?>">

                                <?php endif; ?>

                            </div>

                            <div class="col-md-6 form-group">

                                <label for="username">Usuario <?= self::REQUIRED ?></label>

                                <?php if ( $usuario->usuario ) : ?>

                                    <div class="form-control"><?= $usuario->usuario ?></div>

                                <?php else : ?>

                                    <input type="text" minlength="8" name="username" id="username" class="form-control" value="<?= $usuario->usuario ?>">

                                <?php endif; ?>

                            </div>

                        </div>

                        <?php if ( $usuario ) : ?>

                            <div class="form-group text-center" id="a-cambiar-pwd">

                                <a href="javascript:void(0)" onclick="get_modal_form({'id_usuario': '<?= $usuario->id_usuario ?>', 'nombre': '<?= $persona->nombre_apellido ?>'}, 'cambiarClave');">

                                    <i class="fa fa-key">&nbsp;</i> Modificar Contrase&ntilde;a

                                </a>

                            </div>

                        <?php else : ?>

                            <div class="row">

                                <div class="col-md-6 form-group">

                                    <?= \HForm::inputPassword('contrasena', "Contraseña " . self::REQUIRED) ?>

                                </div>

                                <div class="col-md-6 form-group">

                                    <label for="re_clave">Repetir contrase&ntilde;a <?= self::REQUIRED ?></label>

                                    <input type="password" name="re_contrasena" id="re_clave" class="form-control" required>

                                </div>

                            </div>

                        <?php endif; ?>

                    <?php endif; ?>

                    <div id="otros-datos-group">

                        <?= $this->otros_datos ?>

                    </div>

                    <div class="form-group" id="detalle-group">

                        <label for="comentario">Observaciones</label>

                        <textarea name="comentario" class="form-control" id="comentario" rows="3"><?= $persona->comentario ?></textarea>

                    </div>

                    <div class="form-group small">

                        <span class=""><i class="required">&nbsp;</i> Datos requeridos</span>

                        <?php if ( $this->nuevo_registro ) : ?>

                            <div class="pull-right" style="margin-top: -3px">

                                <input id="nuevo" name="nuevo" type="checkbox" checked><label for="nuevo">&nbsp;Nuevo Registro</label>

                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="form-group text-right" id="form-buttons-group">

                        <?php if ( $this->puede_editar ) : ?>

                            <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>

                        <?php endif; ?>

                        <?php if ( $this->back_url ) : ?>

                            <a href="<?= $this->back_url ?>" id="btn-close" class="btn btn-default">Cancelar</a>

                        <?php endif; ?>

                    </div>

                </form>

            </div>

        </div>

        <script type="text/javascript">

            $('#dob').calendario();

            $('#dni').numeric();

            $('#telefono, #celular').addClass('idf').mask('0000-0000000000');

            $('#genre-group [value="<?=$persona->genero?>"]').attr("checked", true);

            <?php if($this->es_modal) : ?>

            $('#btn-close').attr("data-dismiss", "modal");

            <?php endif; ?>

            <?php if( $submit && $this->puede_editar ) : echo "\n"; ?>

            document.getElementById('frm-persona').onsubmit = function (e) {

                e.preventDefault();

                submit_form(this, function (res) {

                    if ( res.data && typeof select_persona !== "undefined" )

                    {

                        var data = res.data;

                        select_cliente.html("<option value='" + data.id + "'>" + data.id + " - " + data.label + "</option>");

                    }

                    document.getElementById('btn-close').click();

                });

            };

            <?php endif; echo "\n"; ?>

            var group_required = $('[data-required="1"]');

            group_required.find('label').append(" <?=self::REQUIRED?>");

            group_required.find('select, input, textarea').attr("required", true);

            (selectIdTipo = document.getElementById('tipo-id')).onchange = function () {

                (inputDni = document.getElementById('dni')).setAttribute("maxlength", 8);

                if ( !(opcion = parseInt(this.value)) )

                {

                    inputDni.value = "";

                    inputDni.setAttribute("disabled", true);

                    return;

                }

                inputDni.removeAttribute("disabled");

                strMask = "00.000.000";

                if ( opcion === 2 )

                {

                    strMask = "00-00000000-0";

                    inputDni.setAttribute("maxlength", 13);

                }

                inputDni.value = "<?=$dni?>";

                $(inputDni).mask(strMask);

                inputDni.focus();

            };

			if ( (lng = parseInt(selectIdTipo.getAttribute("rel"))) )

			{

				selectIdTipo.value = (lng > 9 ? 2 : 1);

			}

            selectIdTipo.onchange();

        </script>

        <?php

        $output = ob_get_clean();

        //ob_end_flush();

        return $output;

    }



    public static function guardar($rol = null)

    {

        if ( !$_POST )

        {

            exit;

        }

        $id_persona = floatval($_POST['id_persona']);

        $esCuit = (intval($_POST['tipo_id']) == 2);

        $dni = floatval(str_replace([",", ".", "-"], "", $_POST['dni']));

        $nombre = trim($_POST['nombre']);

        $apellido = trim($_POST['apellido']);

        $fecha_nacimiento = trim($_POST['dob']);

        $genero = $_POST['genero'];

        $direccion = trim($_POST['direccion']);

        $telefono = trim($_POST['telefono']);

        //$numero_telefono = $_REQUEST['celular'];

        $celular = trim($_POST['celular']);

        $correo = trim($_POST['correo']);

        $zona = trim($_POST['zona']);

        $_rol = trim($_POST['rol'] ?: $rol);

        #-- Es un array

        $otro_dato = $_POST['otro_dato'];

        $comentario = trim($_POST['comentario']);

        #--

        $roles = array('cliente', 'USUARIO', 'NO_CATEGORIA');

        if ( $rol == Persona::ROL_USUARIO )

        {

            $roles = array(Persona::ROL_USUARIO);

        }

        $persona = Persona::findOrNew($id_persona);

        $original = $persona['original'];

        #--

        if ( strlen($nombre) < 3 )

        {

            HArray::jsonError("Ingrese un nombre válido", "nombre");

        }



        if ( $apellido && strlen($apellido) < 3 )

        {

            HArray::jsonError("Ingrese un Apellido válido", "apellido");

        }



        if ( $fecha_nacimiento && !HDate::isDate($fecha_nacimiento) )

        {

            HArray::jsonError("Ingrese una Fecha de nacimiento válida", "dob");

        }



        if ( $dni )

        {

            if ( $esCuit && !static::validarCuit($dni) )

            {

                HArray::jsonError("El CUIT/L ingresado es inválido", "dni");

            }

            #--

            if ( !$esCuit && ($dni < 1000000) )

            {

                HArray::jsonError("Ingresar un DNI válido", "dni");

            }

            #--

            $existe_dni = Persona::where('id', '<>', $id_persona)->where(['dni' => $dni, 'borrado' => 0])->whereIn('rol', $roles)->first();

            //HArray::varDump($existe_dni);

            //$existe_dni = Persona::whereRaw("`dni` = '{$dni}' AND `rol` != '" . Persona::ROL_USUARIO . "' AND `id` <> '{$id_persona}'")->first();

            if ( $existe_dni )

            {

                HArray::jsonError("Ya existe otra persona registrada con el DNI/CUIT/CUIL ingresado.", "dni");

            }

        }



        if ( $direccion && !preg_match("#.*(\s+\d+)#", $direccion) )

        {

            HArray::jsonError("Ingrese una dirección compuesta por calle y número", "direccion");

        }



        if ( $correo && !filter_var($correo, FILTER_VALIDATE_EMAIL) )

        {

            HArray::jsonError("El e-mail ingresado no es válido", "correo");

        }

        #--

        if ( $correo && Persona::existeEmail($correo, $id_persona) )

        {

            HArray::jsonError("Ya existe un usuario registrado con ese e-mail", "correo");

        }

        #--

        if ( $dni )

        {

            $persona->dni = $dni;

        }

        $persona->fecha_nacimiento = \HDate::sqlDate($fecha_nacimiento);

        $persona->nombre = mb_strtolower($nombre);

        $persona->apellido = mb_strtolower($apellido);

        $persona->genero = $genero;

        $persona->rol = $_rol;

        $persona->direccion = mb_strtolower($direccion);

        $persona->zona = $zona;

        if ( $correo )

        {

            $persona->email = strtolower($correo);

        }

        $persona->telefono = $telefono;

        $persona->celular = $celular;

        if ( array_values($otro_dato)[0] )

        {

            $persona->otros_datos = $otro_dato;

        }

        $persona->comentario = mb_strtolower($comentario);

        $persona->save();

        #-- indica si hubo cambios

        $estado['cambios'] = !empty(array_diff($persona['attributes'], $original));

        $estado['nuevo'] = !$id_persona;

        $persona['estado'] = $estado;

        return $persona;

    }

}