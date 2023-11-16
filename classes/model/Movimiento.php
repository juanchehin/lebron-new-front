<?php

class Movimiento extends MainModel
{
    protected $table = self::tablaMovimiento;
    const moduloCuenta = "cuenta";
    const moduloStock = "stock";
    public $primaryKey = "id";

    public function hasConcepto()
    {
        return $this->hasOne(Concepto::class, "id_concepto", "id_concepto");
    }

    public function hasCuenta()
    {
        return $this->hasOne(Concepto::class, "id_concepto", "id_cuenta");
    }

    public function hasChild()
    {
        return $this->hasMany(static::class, "id_relacion", $this->primaryKey);
    }

    public function hasPersona()
    {
        return $this->hasOne(Persona::class, $this->primaryKey, "accion");
    }

    public function getMontoAttribute()
    {
        return $this->signo . $this->attributes['importe'];
    }

    public function getDiasPagoAttribute()
    {
        return HDate::dateDiff($this->attributes['fecha_registro'], date('Y-m-d'));
    }

    public function getUltimoPagoAttribute()
    {
        $ultimoPago = $this->hasChild->sortByDesc('id')->first();
        $arr = array();
        $fecha_registro = $ultimoPago->fecha_registro;
        if ( $ultimoPago->id_cuenta == Concepto::itemCuentaCliente )
        {
            $arr['no_pago'] = 1;
            $fecha_registro = substr($ultimoPago->fecha_registro, 0, 8) . "01";
        }
        $arr['fecha_registro'] = $fecha_registro;
        $arr['id_cuenta'] = $ultimoPago->id_cuenta;
        return $arr;
    }

    public function getTotalPagadoAttribute()
    {
        $total_pagado = $this->hasChild->whereNotIn('id_cuenta', [0, Concepto::itemCuentaCliente])->sum('importe');
        return floatval($total_pagado);
    }

    public function getSaldoItemAttribute()
    {
        $total = floatval($this->attributes['importe']) + ($this->attributes['saldo']) - $this->getTotalPagadoAttribute();
        return $total;
        //return in_array($this->attributes['id_cuenta'], [Concepto::itemCuentaCliente]);
    }

    public function getMontoPagoAttribute()
    {
        /*$importe = floatval($this->attributes['importe']);
        if ( $this->attributes['id_relacion'] || $this->attributes['modulo'] == MenuPanel::menuContable )
        {
            return $importe;
        }
        //$monto_pago = floatval($this->attributes['saldo']) ?: floatval($this->attributes['importe']);
        $ultimo_pago = $this->getUltimoPagoAttribute();
        $pagado = $this->getTotalPagadoAttribute();
        //return $ultimo_pago;
        $dias = HDate::dateDiff($ultimo_pago['fecha_registro'], date('Y-m-d'));
        //if ( ($dias > 10) && in_array($this->attributes['id_cuenta'], [0, Concepto::itemCuentaCliente]) )
        if ( ($saldo = $this->getSaldoItemAttribute()) > 0 )
        {
            if ( $ultimo_pago['no_pago'] )
            {
                $dias -= 10;
            }
            #--
            if ( $dias < 0 )
            {
                $dias = 0;
            }
            $recargo = $dias * (floatval(static::confKey('recargo')) / 100);
            $monto_pago = round(($recargo * ($pagado > 0 ? $saldo : $importe)), 2);
            if ( $monto_pago != floatval($this->attributes['saldo']) )
            {
                $this->attributes['saldo'] = $monto_pago;
                $this->save();
            }
        }
        //HArray::varDump($importe."&".$this->attributes['saldo']);
        #--
        return ($importe + floatval($this->attributes['saldo']));*/
    }

    public function getFechaAcientoAttribute()
    {
        return HDate::dateFormat($this->attributes['fecha_registro']);
    }

    public function getFechaAttribute()
    {
        return HDate::dateFormat($this->attributes['fecha_hora'], HDate::FORMAT_DMY_HMS);
    }

    public function getArrayAccionAttribute()
    {
        return (array)json_decode($this->attributes['accion'], true);
    }

    public function getPagoPersonaAttribute()
    {
        return in_array($this->attributes['id_concepto'], [Concepto::itemDeudaCliente, Concepto::itemCuentaProveedor]);
    }

    public function getIdPersonaAttribute()
    {
        $id_persona = floatval($this->attributes[$this->attributes['id_concepto'] == Concepto::itemCuentaProveedor ? "valor" : "accion"]);
        return $id_persona;
    }

    public function setJsonAccionAttribute($value)
    {
        $data = array_merge($this->getArrayAccionAttribute(), (array)$value);
        $this->attributes['accion'] = json_encode($data, JSON_NUMERIC_CHECK);
        return;
    }

    public static function crear($id_cuenta, $id_concepto, $importe, $id_local = null, $id_operacion = null, $comentario = null)
    {
        $registro = new self;
        $registro->id_sucursal = intval($id_local);
        $registro->id_cuenta = $id_cuenta;
        $registro->id_concepto = $id_concepto;
        $registro->importe = $importe;
        $registro->id_operacion = $id_operacion;
        $registro->fecha_registro = date('Y-m-d');
        $registro->comentario = mb_strtolower($comentario);
        $registro->save();
        return $registro;
    }
}