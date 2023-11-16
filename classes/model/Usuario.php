<?php

class Usuario extends MainModel
{
    const PASS_LENGTH = 6;
    const MIN_LENGTH_USER = 6;
    const CADUCIDAD_PASSWORD = 90; //dias
    const TIEMPO_SESSION = 10; //minutos
    const USER_COUNT = 'user_count';
    const MALE = 'm';
    const FEMALE = 'f';
    const CUIT_REGEX = "#^\d{2}\-\d{8}\-\d{1}$#";
    const USER_SESSION = "user_session";
    const PANEL_SESSION = "user_panel";
    const USR_USUARIO = 'USUARIO';
    const USR_PANEL_ADMIN = 'PANEL_ADMIN';
    const USR_PANEL_AUDITOR = 'PANEL_AUDITOR';
    const idMasterAdmin = 2;
    protected $table = self::tablaUsuario;
    public $primaryKey = 'id_usuario';
    public static $_USUARIO_PUBLICO = array(
        self::USR_USUARIO,
    );
    public static $_ROLES = array(
        self::USR_PANEL_ADMIN => "Administrador",
        self::USR_PANEL_AUDITOR => "Estándar"
    );

    #--
    public function hasPersona()
    {
        return $this->hasOne("Persona", "id", "id_persona");
    }

    public function hasIncidencia()
    {
        return $this->hasMany("Incidencia", $this->primaryKey, $this->primaryKey);
    }

    public function setTokenAttribute($value = null)
    {
        $this->attributes['token'] = $value ? md5($value . date('Ymd_His')) : null;

        return $this;
    }

    public function setGeneroAttribute($value)
    {
        $this->attributes['genero'] = strtolower(substr($value, 0, 1));
        return;
    }

    public function getFechaNacimientoAttribute()
    {
        $fecha = $this->attributes['fecha_nacimiento'];
        if ( $fecha && $fecha != '0000-00-00' )
        {
            $fecha = HDate::dateFormat($this->attributes['fecha_nacimiento']);
        }
        else
        {
            $fecha = null;
        }

        return $fecha;
    }

    public function getNombrePilaAttribute()
    {
        return ucfirst($this->hasPersona->nombre);
    }

    public function getNombreApellidoAttribute()
    {
        return $this->hasPersona->nombre_apellido;
    }

    public function getEsAdminAttribute()
    {
        return ($this->attributes['tipo_usuario'] == self::USR_PANEL_ADMIN);
    }

    public function getRolAttribute()
    {
        return static::$_ROLES[$this->attributes['tipo_usuario']];
    }

    public function getGeneroAttribute()
    {
        switch ( $this->attributes['sexo'] )
        {
            case self::MALE:
                $genero = "Masculino";
                break;
            case self::FEMALE:
                $genero = "Femenino";
                break;
            default :
                $genero = "No especificado";
                break;
        }

        return $genero;
    }

    public function setPermisoAttribute($value)
    {
        if ( is_array($value) )
        {
            $this->attributes['permiso'] = json_encode($value);
        }
        return;
    }

    public function getPermisosAttribute()
    {
        $permisos = (array)json_decode($this->attributes['permiso'], true);
        if ( !$permisos )
        {
            $permisos = Permiso::permisoRol($this->attributes['tipo_usuario']);
        }
        return $permisos;
    }

    public function getImagenPerfilAttribute()
    {
        $imagen = $this->attributes['imagen'];
        if ( $this->attributes['cuenta'] == "FACEBOOK" )
        {
            $imagen = "";
        }
        if ( !$imagen )
        {
            switch ( $this->attributes['genero'] )
            {
                case self::FEMALE:
                    $perfil = "female.jpg";
                    break;
                case self::MALE :
                    $perfil = "male.jpg";
                    break;
                default:
                    $perfil = "usuario.jpg";
                    break;
            }
            $imagen = "assets/img/{$perfil}";
        }

        return $imagen;
    }

    public function getFechaRegistroAttribute()
    {
        return HDate::dateFormat($this->attributes['fecha_registro'], HDate::FORMAT_DMY_HMS);
    }

    public function getUltimoAccesoAttribute()
    {
        $fecha = $this->attributes['ultimo_acceso'];
        if ( $fecha && $fecha != '0000-00-00 00:00:00' )
        {
            $fecha = HDate::dateFormat($fecha, HDate::FORMAT_DMY_HMS);
        }
        else
        {
            $fecha = "Sin información";
        }

        return $fecha;
    }

    public function setAuthToken()
    {
        //if ( !$this->token )
        //{
        $this->token = $this->attributes['email'];
        $this->save();

        //}
        return $this->token;
    }

    public function getAuthTokenAttribute()
    {
        $token = $this->attributes['token'];
        if ( !$token )
        {
            $token = $this->setAuthToken();
        }

        return $token;
    }

    public function getLocalAttribute()
    {
        return Local::$_LOCALES[$this->id_local] ?: "-";
    }

    public function ultimaIncidencia($tipo = null, $estado = null)
    {
        return Incidencia::ultimaIncidencia($this->id_usuario, $tipo, $estado);
    }

    public static function existeEmail($id_usuario = null, $email)
    {
        return self::getUsuario($id_usuario, null, null, $email);
    }

    public static function existeUsuario($username, $id_usuario = null)
    {
        return static::getUsuario($id_usuario, null, null, null, $username);
    }

    private static function getUsuario($id_usuario = null, $cuit = null, $dni = null, $email = null, $usuario = null)
    {
        if ( $cuit )
        {
            $where['cuit'] = $cuit;
        }

        if ( $dni )
        {
            $where['dni'] = $dni;
        }

        if ( $email )
        {
            $where['correo'] = $email;
        }

        if ( $usuario )
        {
            $where['usuario'] = $usuario;
        }

        #--
        return static::where('id_usuario', '<>', $id_usuario)->where($where)->first();
    }

    public function setContrasenaAttribute($value)
    {
        $this->attributes['contrasena'] = HFunctions::encrypt($value);
        return $this;
    }

    public function checkPassword($password)
    {
        return (HFunctions::encrypt($password) == $this->attributes['contrasena']);
    }

    public function setUserLogin($session = self::USER_SESSION)
    {
        HSession::setSession($session, serialize($this));
        /*if ( !is_dir(($dir = "conf/lang")) )
        {
            mkdir($dir, 700);
        }
        file_put_contents("{$dir}/" . $session, serialize($this), LOCK_EX);*/
    }

    public static function getLoggedUser($session = self::USER_SESSION)
    {
        return unserialize(HSession::getSession($session));
        //return unserialize(file_get_contents("conf/lang/" . $session));
    }

    public static function getPanelUser()
    {
        return static::getLoggedUser(self::PANEL_SESSION);
    }

    public static function logoutUser($session = self::USER_SESSION)
    {
        HSession::removeSession($session);
        //unlink("conf/lang/{$session}");
        return;
    }

    public function bajaLogica()
    {
        $this->hasPersona->borrado = 1;
        $this->hasPersona->save();
        $this->borrado = 1;
        $this->save();
    }
}

?>