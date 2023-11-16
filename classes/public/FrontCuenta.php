<?php

class Cuenta extends Gestion
{
    private $verificacion;

    public function __construct()
    {
        parent::__construct();
        $this->verificacion = $this->logged_user->ultimaIncidencia(Incidencia::TIPO_VERIFICACION_CELULAR, 0);
        $this->setParams('codigo_sms', $this->verificacion->valor);
    }

    public function datosUsuario()
    {
        $params = array(
            'usuario' => $this->logged_user,
            'tipo_usuario' => $this->logged_user->user_type,
            'provincia' => $this->logged_user->provincia,
            'companias' => MainModel::$_COMPANIAS_TELEFONICAS,
        );

        $this->setPageTitle("Datos de Usuario");
        $this->setItemActual(self::ITEM_PERFIL);
        $this->setParams($params);
        $this->setBody("usuario-perfil");
    }

    public function verificarOrganizacion()
    {
        $codigo = addslashes($_POST['codigo']);
        $clave = $_POST['clave'];
        $organizacion = Organizacion::where(['codigo' => $codigo, 'activo' => 1])->first();
        if ( !$organizacion )
        {
            HArray::jsonError("No existe organizaci&oacute;n con ese c&oacute;digo", "codigo");
        }

        if ( $organizacion->clave != $clave )
        {
            HArray::jsonError("La clave no es correcta. Intente nuevamente", "clave");
        }

        Rol::crearRol($organizacion->id_organizacion, $this->id_usuario, Rol::ROL_CAMPANIA);

        HArray::jsonSuccess();
    }

    public function guardarOrganizacion()
    {
        $id = addslashes($_POST['id_organizacion']);
        $nombre = trim($_POST['organizacion']);
        $direccion = trim($_POST['direccion']);
        $correo = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']);
        if ( !$id && !$nombre )
        {
            HArray::jsonError("Ingresar el nombre", "organizacion");
        }

        if ( !$direccion )
        {
            HArray::jsonError("Ingresar la direcci&oacute;n", "direccion");
        }

        if ( !$id && !filter_var($correo, FILTER_VALIDATE_EMAIL) )
        {
            HArray::jsonError("Ingrese un correo electr&oacute;nico v&aacute;lido", "correo");
        }
        #--
        $organizacion = Organizacion::findOrNew($id);
        $organizacion->nombre = strtolower($nombre);
        $organizacion->direccion = strtolower($direccion);
        $organizacion->correo = strtolower($correo);
        $organizacion->telefono = $telefono;
        $organizacion->codigo = time();
        $organizacion->clave = uniqid();
        $organizacion->save();
        #--
        if ( !$id )
        {
            Rol::crearRol($organizacion->id_organizacion, $this->id_usuario, Rol::ROL_ADMIN);
        }
        HArray::jsonSuccess();
    }

    public function borrarCuenta()
    {
        $this->sessionControl();
        $contrasena = $_REQUEST['pass'];
        if ( !$this->logged_user->checkPassword($contrasena) )
        {
            HArray::jsonError("Contrase&ntilde;a incorrecta");
        }
        //unset($this->logged_user['tiempo']);
        $this->logged_user->borrado = 1;
        $this->logged_user->activo = 0;
        $this->logged_user->fecha_borrado = HDate::today();
        if ( $this->logged_user->save() )
        {
            $descripcion = "El usuario {$this->logged_user->nombre_persona} eliminó su cuenta";
            Incidencia::nuevaIncidencia(Incidencia::TIPO_ELIMINA_CUENTA, $this->logged_user->id_usuario, $descripcion);
            $this->salir();
        }
    }

    public function imagenPerfil()
    {
        $this->sessionControl(true);
        $imagen = $_FILES['_image'];
        $extension = pathinfo($imagen['name'])['extension'];
        if ( !in_array($extension, MainModel::$_EXTENSION_IMAGEN) )
        {
            HArray::jsonError("El archivo no es una imagen. Verifique.");
        }

        if ( ($imagen['size'] / 1024) > 1024 )
        {
            HArray::jsonError("El tama&ntilde;o de la imagen supera el permitido (1 MB).");
        }

        $file_name = "user_" . date('YmdHis') . ".{$extension}";
        if ( !$imagen['error'] && move_uploaded_file($imagen['tmp_name'], IMAGE_DIR . "/" . $file_name) )
        {
            #-- Borrar su otra imagen si tiene
            @unlink(IMAGE_DIR . "/{$this->logged_user->imagen}");
            #-- Asignar la nueva imagen
            $this->logged_user->imagen = $file_name;
            $this->logged_user->setUserLogin();
            HArray::jsonResponse('imagen', $this->logged_user->imagen_perfil);
            //HArray::jsonSuccess();
            exit;
        }

        HArray::jsonError("La imagen no pudo ser agregada.");
    }

    public function save()
    {
        //HArray::varDump($_SERVER['CONTENT_LENGTH']);
        $this->sessionControl(true);
        $usuario = Usuario::saveData();
        $cambios = array_diff($usuario['attributes'], $this->logged_user['original']);
        //HArray::varDump($cambios);
        if ( $usuario->id_usuario == $this->logged_user->id_usuario && $cambios )
        {
            $usuario->setUserLogin();
            HArray::jsonSuccess();
        }
    }
}