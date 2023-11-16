<?php
class AdminAcceso extends MainTemplate
{
    public function __construct()
    {
        parent::__construct();
        $this->template = "admin/login";
        $panel_usuario = Usuario::getLoggedUser(Usuario::PANEL_SESSION);
        if ( $panel_usuario )
        {
            Router::redirect(self::appUrl . CP_ADMIN);
        }
    }

    public function index()
    {
        $this->setPageTitle("Ingresar");
        if ( DEVELOPMENT )
        {
            $this->setParams(['usuario' => "admincp", 'contrasena' => "admincp"]);
        }
        $this->setBody();
    }

    public function ingresar()
    {
        if ( !self::isXhrRequest() )
        {
            exit;
        }
        $tipo_usuario = array_keys(Usuario::$_ROLES);
        $username = addslashes(trim($_POST['username']));
        $password = trim($_POST['password']);
        #--
        $usuario = Usuario::where(['activo' => 1, 'borrado' => 0])->whereIn('tipo_usuario', $tipo_usuario)->where(function ($sql) use ($username) {
            $sql->where('usuario', $username)->orWhereHas('hasPersona', function ($sql) use ($username) {
                $sql->where('email', $username)->orWhere('dni', $username);
            });
        })->first();
        if ( $usuario )
        {
            if ( $usuario->checkPassword($password) )
            {
                $usuario->setUserLogin(Usuario::PANEL_SESSION);
                HArray::jsonSuccess();
            }
        }

        HArray::jsonError("Nombre de usuario / E-mail o contrase&ntilde;a incorrectos.");
        //HArray::jsonSuccess();
    }
}
