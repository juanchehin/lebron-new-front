<?php

class Incidencia extends MainModel
{
    const TIPO_CAMBIO_PASS = 'NUEVA_CONTRASENA';
    const TIPO_ELIMINA_CUENTA = 'ELIMINA_CUENTA';
    const TIPO_ACCESO = 'NUEVO_ACCESO';
    const TIPO_REGISTRO_USUARIO = 'REGISTRO_USUARIO';
    const TIPO_REGISTRO_COMERCIO = 'REGISTRO_COMERCIO';
    const TIPO_REGISTRO_TARJETA = 'REGISTRO_TARJETA';
    const TIPO_VERIFICACION_CELULAR = 'VERIFICACION_CELULAR';
    const TIPO_SOLICITUD_SALDO = 'SOLICITUD_SALDO';
    const TIPO_CUPON_RECARGA = 'CUPON_RECARGA';
    const controlVenta = "control_venta";
    protected $table = self::tablaIncidencia;
    public $primaryKey = "id";
    public static $_incidencias = array(
        self::TIPO_CAMBIO_PASS => 'Cambio de contraseña',
        self::TIPO_ACCESO => 'Acceso de usuario',
        self::TIPO_ELIMINA_CUENTA => 'Eliminación de cuenta',
        self::TIPO_REGISTRO_USUARIO => 'Registro de Usuario en ' . SITE_NAME,
        self::TIPO_REGISTRO_COMERCIO => 'Registro de Comercio en ' . SITE_NAME,
        self::TIPO_SOLICITUD_SALDO => "Solicitud de Saldo",
        self::TIPO_CUPON_RECARGA => "Cupon para recarga de Cuenta"
    );

    #--
    public function hasUsuario()
    {
        return $this->hasOne(Usuario::class, $this->primaryKey, $this->primaryKey);
    }

    public function getFechaAttribute()
    {
        return HDate::dateFormat($this->attributes['fecha_hora']);
    }

    public function getFechaRegistroAttribute()
    {
        return HDate::dateFormat($this->attributes['fecha_hora'], HDate::FORMAT_DMY_HMS);
    }

    public static function crear($tipo, $id_usuario, $fecha = null, $valor = null, $descripcion = null)
    {
        $incidencia = new Incidencia();
        $incidencia->operacion = $tipo;
        $incidencia->id_usuario = $id_usuario;
        $incidencia->valor = $valor;
        $incidencia->fecha_hora = ($fecha ?: date('Y-m-d')) . date(' H:i:s');
        if ( !$descripcion )
        {
            $descripcion = static::$_incidencias[$tipo];
        }
        $incidencia->detalle = $descripcion;
        $incidencia->save();

        return $incidencia;
    }

    public static function getIncidencia($tipo, $fecha = null, $id_usuario = null)
    {
        $where['tipo_incidencia'] = $tipo;
        if ( $id_usuario )
        {
            $where['id_usuario'] = $id_usuario;
        }
        $incidencias = self::where($where);
        if ( $fecha )
        {
            $incidencias = $incidencias->whereDate('fecha_incidencia', $fecha);
        }

        return $incidencias->get();
    }
}