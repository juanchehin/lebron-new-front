<?php

class Concepto extends MainModel
{
    protected $table = self::tablaConcepto;
    public $primaryKey = "id_concepto";
    const tipoCuenta = "cuenta";
    const tipoCuentaActivo = "activo";
    const tipoCuentaPasivo = "pasivo";
    const cuentaCaja = 1;
    const cuentaCajaUsd = 7;
    const itemVarios = 11;
    const cuentaBanco = 13;
    const cuentaPF = 26;
    const debitoTarjeta = 36;
    const debitoMastercard = 38;
    const cuentaMP = 17;
    const cuentaBancaria = 27;
    const cuentaRegalo = 37;
    const itemCompra = 7;
    const itemVenta = 6;
    const itemPagoCliente = 2;
    const itemDeudaCliente = 5;
    const cuentaCorriente = self::itemDeudaCliente;
    const itemCuentaCliente = 3;
    const itemCuentaProveedor = 4;

    public function hasMovimiento()
    {
        return $this->hasMany(Movimiento::class, $this->primaryKey, $this->primaryKey);
    }

    public function hasDebe()
    {
        return $this->hasMany(Movimiento::class, "id_cuenta", $this->primaryKey);
    }

    public function hasCuenta()
    {
        return $this->hasOne(self::class, $this->primaryKey, "id_cuenta");
    }

    public function hasConcepto()
    {
        return $this->hasMany(self::class, "id_cuenta", $this->primaryKey)->where('tipo', "<>", self::tipoCuenta);
    }

    public function getNombreAttribute()
    {
        return mb_strtoupper($this->attributes['concepto']);
    }

    /*public function getIdCuentaAttribute()
    {
        return $this->attributes['id_concepto'];
    }*/

    public function getEsCuentaAttribute()
    {
        return !$this->attributes['id_cuenta'];
    }

    public function getEsConceptoAttribute()
    {
        return ($this->attributes['id_cuenta'] && ($this->attributes['tipo'] == "concepto"));
    }

    public function getTipoCuentaAttribute()
    {
        $tipo_cuenta = ucfirst($this->attributes['tipo']);
        if ( !$this->es_concepto && ($accion = $this->attributes['accion']) )
        {
            $tipo_cuenta = "Cuenta Pérdidas";
            if ( $accion == 'HABER' )
            {
                $tipo_cuenta = "Cuenta Ganancia";
            }
        }
        return $tipo_cuenta;
    }

    public function getConceptoCuentaAttribute()
    {
        $concepto = $this->getNombreAttribute();
        if ( $this->attributes['id_cuenta'] )
        {
            $concepto .= " (<i>{$this->hasCuenta->nombre}</i>)";
        }
        return $concepto;
    }

    public function getVecesUsadaAttribute()
    {
        return Movimiento::whereRaw("id_cuenta='{$this->id_concepto}' OR id_concepto='{$this->id_concepto}'")->count();
    }

    static public function cuentasPago()
    {
        return self::select("id_concepto", "concepto")->whereRaw("!`borrado` AND `categoria` = 'disponibilidad'")->orderBy("id_concepto")->get();
    }

    public static function getConceptos($visible = true, $accion = self::tipoCuenta)
    {
        if ( $visible )
        {
            $where['visible'] = 1;
        }
        $where['tipo'] = $accion;
        return self::where($where)->get();
    }

    public static function get($id_concepto)
    {
        return self::find($id_concepto)->nombre;
    }

    public static function crear($concepto, $accion, $id_concepto = null, $id_cuenta = null)
    {
        $_concepto = null;
        if ( $concepto )
        {
            $_concepto = self::findOrNew($id_concepto);
            $_concepto->concepto = mb_strtolower($concepto);
            $_concepto->accion = $accion;
            $_concepto->id_cuenta = intval($id_cuenta);
            $_concepto->visible = 1;
            $_concepto->save();
        }
        return $_concepto;
    }
}