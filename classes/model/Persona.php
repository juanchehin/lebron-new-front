<?php

class Persona extends MainModel
{
    protected $table = self::tablaPersona;
    public $primaryKey = "id";
    const MASCULINO = "M";
    const FEMENINO = "F";
    const ROL_USUARIO = "USUARIO";
    const rolCliente = "cliente";
    const rolEmpleado = "empleado";
    const rolProveedor = "proveedor";
    const ESTADO_ACTIVO = "ACTIVO";
    public static $_GENEROS = array(
        self::FEMENINO => "Femenino",
        self::MASCULINO => "Masculino",
    );

    public function hasUsuario()
    {
        return $this->hasOne(Usuario::class, "id_persona", $this->primaryKey);
    }

    public function hasCuenta()
    {
        return $this->hasMany(CuentaCliente::class, "accion", $this->primaryKey);
    }

    public function hasVenta()
    {
        return $this->hasMany(Venta::class, "id_cliente", $this->primaryKey);
    }

    public function getFullStreetAttribute()
    {
        list($val['direccion'], $val['piso'], $val['depto'], $val['cp']) = explode("&", $this->attributes['direccion']);
        return $val;
    }

    public function getUltimoPagoAttribute()
    {
        if ( !($ultimo_pago = $this->hasCuenta()->whereRaw("`modulo`='cuenta_pago'")->orderBy('fecha_hora', "DESC")->first()) )
        {
            $ultimo_pago = $this->hasCuenta()->orderBy('fecha_hora')->first();
        }
        /*foreach (["id_concepto" => "DESC", "id_cuenta" => "ASC"] as $w => $order)
        {
            $ultimo_pago = $this->hasCuenta()->where($w, Concepto::itemCuentaCliente)->orderBy('fecha_hora', $order)->first();
            if ( $ultimo_pago )
            {
                break;
            }
        }*/
        return $ultimo_pago;
    }

    public function getSaldoActualAttribute()
    {
        $ultimo = $this->getUltimoPagoAttribute();
        // HArray::varDump($ultimo, false);
        $saldo = CuentaCliente::calculoSaldos($this->id, ($this->attributes['rol'] == self::rolProveedor), true);
        /*if ( $desde = $ultimo->fecha_hora )
        {
            $where = "`fecha_hora` BETWEEN '{$desde}' AND NOW()";
            //die($where);
            $query = $this->hasCuenta()->selectRaw("IF(SUBSTR(modulo,8) <> '',SUM(importe)*-1, SUM(importe)) AS total")->whereRaw($where)->groupBy("modulo")->pluck('total')->toArray();
            if ( $query[0] < 0 )
            {
                $query[1] = $query[0];
                $query[0] = 0;
            }
            $saldo = ($saldo_anterior = floatval($ultimo->comentario)) + floatval($query[0]);
            //HArray::varDump($query, false);
            if ( !$saldo_anterior && ($pago = floatval($query[1])) )
            {
                $saldo += $pago;
            }
        }*/
        return $saldo;
    }

    public function getDobAttribute()
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
        return ucfirst($this->attributes['nombre']);
    }

    public function getNombreApellidoAttribute()
    {
        $nombre = $this->attributes['nombre'] . " " . $this->attributes['apellido'];

        return ucwords($nombre);
    }

    public function getApellidoNombreAttribute()
    {
        return ucwords($this->attributes['apellido'] . " {$this->attributes['nombre']}");
    }

    public function setGeneroAttribute($value)
    {
        $this->attributes['genero'] = strtoupper(substr($value, 0, 1));
        return;
    }

    public function getGeneroLabelAttribute()
    {
        switch ( $this->attributes['genero'] )
        {
            case self::MASCULINO:
                $genero = "Masculino";
                break;
            case self::FEMENINO:
                $genero = "Femenino";
                break;
            default :
                $genero = "No especificado";
                break;
        }

        return $genero;
    }

    public function getFechaHoraAttribute()
    {
        return HDate::dateFormat($this->attributes['fecha_registro'], HDate::FORMAT_DMY_HMS);
    }

    public function getDireccionAttribute()
    {
        return mb_strtoupper($this->attributes['direccion']);
    }

    public function getLocalidadAttribute()
    {
        return ucwords($this->attributes['localidad']);
    }

    public function getProvinciaAttribute()
    {
        return $this->hasCiudad->nombre;
    }

    public function getCallCodeAttribute()
    {
        return $this->hasCiudad->hasCountry->callingCodes;
    }

    public function getFullCellphoneAttribute()
    {
        $numero = null;
        if ( $this->attributes['codigo_area'] && $this->attributes['numero_telefono'] )
        {
            $numero = $this->call_code . "9{$this->attributes['celular']}";
        }

        return $numero;
    }

    public function setTokenAttribute($value = null)
    {
        $this->attributes['token'] = $value ? md5($value . date('Ymd_His')) : null;

        return $this;
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

    public function getArrayInfoAttribute()
    {
        $info['id'] = $this->attributes['id'];
        $info['nombre_completo'] = $this->nombre_apellido;
        $info['dni'] = $this->attributes['dni'];
        $info['dob'] = $this->dob;
        $info['email'] = $this->attributes['email'];
        $info['label'] = $this->label;
        return $info;
    }

    public function getLabelAttribute()
    {
        return $this->nombre_apellido . ($this->dni ? " ({$this->dni})" : "");
    }

    public function getArrayDireccionAttribute()
    {
        return (array)json_decode($this->attributes['direccion'], true);
    }

    public function getArrayOtrosDatosAttribute()
    {
        return (array)json_decode($this->attributes['json_otros_datos'], true);
    }

    public function setOtrosDatosJsonAttribute($value)
    {
        $datos = array_merge($this->getArrayOtrosDatosAttribute(), (array)$value);
        $this->attributes['json_otros_datos'] = json_encode($datos);
        return;
    }

    public static function existeEmail($email, $id_usuario = null)
    {
        return self::getPersona($id_usuario, null, $email);
    }

    private static function getPersona($id_usuario = null, $dni = null, $email = null)
    {
        if ( $dni )
        {
            $where['dni'] = $dni;
        }

        if ( $email )
        {
            $where['email'] = $email;
        }
        #--
        return static::where('id', '<>', $id_usuario)->where($where)->first();
    }
}