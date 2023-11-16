<?php

class Gestion extends Main
{
    const ITEM_INICIO = 'INICIO';
    const ITEM_PERFIL = 'PERFIL';
    const ITEM_SALDO = 'SALDO';
    const ITEM_MOVIMIENTOS = 'MOVIMIENTOS';
    const ITEM_RECARGA_CELULAR = 'RECARGA_CELULAR';
    const ITEM_SOLICITAR_TP = "SOLICITAR_TARJETA_PREPAGA";
    const ITEM_SOLICITAR_MC = "SOLICITAR_MICROCREDITO";
    const ITEM_TARJETAS = "TARJETAS";
    const ITEM_PRESTAMO = 'SOLICITUD_PRESTAMO';
    const ITEM_PAGOS = 'PAGOS';
    const ITEM_RECARGA_CUENTA = 'RECARGA_CUENTA';
    const ITEM_IMPUESTOS = 'IMPUESTOS';
    const ITEM_EXTRAER = 'EXTRAER';
    const ITEM_TRANSFERIR = 'TRANSFERIR';
    const ITEM_RESUMEN_COBROS = 'RESUMEN_COBROS';
    const ITEM_FACTURACION = 'FACTURACION';
    const ITEM_CONTACTO = 'CONTACTO';
    const ITEM_LOGOUT = 'LOGOUT';
    #--
    const NO_LINK = "javascript:void(0)";
    const URI_MIS_DATOS = HTTP_HOST . "/cuenta/mis-datos";
    const TIEMPO_INACTIVIDAD = Usuario::TIEMPO_SESSION * 60; //600 segs
    const POR_PAGINA = 15;
    const MUY_PRONTO = "Este servicio estar&aacute; disponible muy pronto. ";
    #--
    protected $id_usuario;
    protected $menu_usuario;
    protected $item_actual;
    protected static $_MENU_USUARIO = array(
        array('item' => self::ITEM_INICIO, 'label' => 'Inicio', 'enlace' => self::URI_USUARIO_HOME),
        array('item' => self::ITEM_PERFIL, 'label' => 'Perfil', 'enlace' => self::URI_MIS_DATOS),
    );

    #--
    public function __construct()
    {
        parent::__construct();
        $this->addMetaAttr('utoken', $this->logged_user->auth_token);
        $this->addScript("js/usuario.js");
        $this->sessionControl();
        $this->_prepareMenu();
    }

    protected function usuarioPermitido()
    {
        $usuarios = array(48, 1, 2, 43);

        return (DEVELOPMENT || in_array($this->id_usuario, $usuarios));
    }

    private function _prepareMenu()
    {
        foreach (static::$_MENU_USUARIO as $item)
        {
            $this->setMenuUsuario($item['item'], $item['label'], $item['enlace']);
        }
    }

    protected function setMenuUsuario($item, $label, $enlace)
    {
        $this->menu_usuario[$item] = array('label' => $label, 'enlace' => $enlace);

        return $this;
    }

    protected function setItemActual($value)
    {
        $this->item_actual = $value;

        return $this;
    }

    private function _menuUsuario()
    {
        $this->setMenuUsuario(self::ITEM_CONTACTO, "Contacto", "contacto");
        $this->setMenuUsuario(self::ITEM_LOGOUT, "Cerrar sesión", "salir");
        $params = array(
            '_current_item' => $this->item_actual,
            '_menu_usuario' => (object)$this->menu_usuario
        );
        $this->setParams($params);
    }

    public function setBody($view = null)
    {
        $this->setParams('url_inicio_usuario', self::URI_USUARIO_HOME);
        $this->setParams('_usuario_template_body', $this->loadView(self::USUARIO_VIEWS . $view));
        $this->_menuUsuario();
        parent::setBody(self::USUARIO_VIEWS . "usuario-template");
    }

    public function index()
    {
        $this->setItemActual(self::ITEM_INICIO);
        $this->setPageTitle("Panel del usuario");
        $this->setBody("usuario-home");
    }

    public function sessionControl($update = false)
    {
        $this->showHeader(false);
        $is_ajax = Router::isAjaxRequest();
        //$user = Usuario::find($this->logged_user->id_usuario);
        #-- Verificar si hay usuario con sesión iniciada.
        if ( !$this->logged_user )
        {
            $this->_redirect();
        }
        $this->id_usuario = $this->logged_user->id_usuario;
        if ( !$this->logged_user->hasRol && !(Router::isAjaxRequest() && !empty($_POST)) )
        {
            $this->nuevoUsuario();
        }
        $this->setParams('ultimo_acceso', $this->logged_user->ultimo_acceso);
        #-- Control de caducidad de contraseña.
        $acceso = $this->logged_user->ultimaIncidencia(Incidencia::TIPO_ACCESO);
        if ( $acceso->valor )
        {
            $vida_session = time() - $acceso->valor;
            if ( $vida_session > self::TIEMPO_INACTIVIDAD && !DEVELOPMENT )
            {
                $this->salir();
            }
        }

        #-- Actualiza la sesión sólo si el usuario realiza alguna acción.
        if ( $acceso && (!$is_ajax || $update) )
        {
            #-- Actualizar la sesión:
            $acceso->valor = time();
            $acceso->save();
            #--
        }
    }

    public function nuevoUsuario()
    {
        $item = "NUEVO_USUARIO";
        if ( preg_match("#salir$#", $_SERVER['REQUEST_URI']) )
        {
            return;
        }
        $this->setMenuUsuario($item, "Nuevo Usuario", "");
        $this->setItemActual($item);
        $this->setPageTitle("Nuevo usuario");
        $this->setParams('organizacion_form', self::loadView("gestion/organizacion-form"));
        $this->setBody("nuevo-usuario");
    }

    public function salir()
    {
        #-- Actualizar la sessión a cero
        $acceso = $this->logged_user->ultimaIncidencia(Incidencia::TIPO_ACCESO);
        if ( $acceso )
        {
            $acceso->valor = 0;
            $acceso->save();
        }
        #-- Remover el usuario de la sesión y redirigir
        Usuario::logoutUser();
        $this->_redirect();
    }

    private function _redirect()
    {
        $location = HTTP_HOST . "/" . self::URI_ACCESO;
        if ( Router::isAjaxRequest() )
        {
            HArray::jsonLocation(HTTP_HOST);
        }
        Router::redirect(HTTP_HOST);
    }

    protected function enviarIncidencia($tipo, $body)
    {
        $info = new Emailer();
        $info->setRemitente($this->logged_user->email, $this->logged_user->nombre_persona);
        $info->setDestino(EMAIL_NOTIFICACION, SITE_NAME);
        $info->setAsunto(Incidencia::$_INCIDENCIAS[$tipo]);
        $info->setEmailView(null, array('body' => $body));
        $info->enviarEmail();
    }
}