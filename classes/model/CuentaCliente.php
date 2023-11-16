<?php

class CuentaCliente extends Movimiento
{
    protected $attributes = array(
        'modulo' => self::moduloCuenta
    );

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function ($query) {
            $query->where('modulo', "LIKE", self::moduloCuenta . "%");
        });
    }

    public function hasProveedor()
    {
        return $this->hasOne(Persona::class, "id", "valor");
    }

    public function hasPersona()
    {
        return $this->hasOne(Persona::class, "id", "accion");
    }

    public function getElMontoAttribute()
    {
        $importe = floatval($this->attributes['importe']);
        if ( !$this->attributes['id_operacion'] )
        {
            $importe *= -1;
        }
        return $importe;
    }

    static public function calculoSaldos($persona_id, $esProveedor = false, $resume = false)
    {
        $where = ($esProveedor ? "`valor`" : "`accion`") . "='{$persona_id}'";
        $last = self::whereRaw("{$where} AND `id_sucursal` = -1")->orderBy("fecha_registro", "DESC")->first();
        //$where = $whr;
        if ( $last_id = $last->id )
        {
            $where .= " AND fecha_registro >= '{$last->fecha_registro}' OR id = '{$last_id}'";
        }
        //die($where);
        //HArray::varDump($last);
        $saldo = array();
        $result = self::whereRaw($where)->orderBy("fecha_registro")->get();
        if ( $last_id && ($result[0]->id == $last_id) )
        {
            $saldo[$last_id] = $last->saldo;
            if ( $resume )
            {
                $saldo = $last->saldo;
            }
            return $saldo;
        }
        //$result = self::where('accion', $persona_id)->orderByRaw("fecha_registro")->get();
        $saldo = array();
        $i = $result[0]->id;
        //HArray::varDump($i);
        foreach ($result as $index => $res)
        {
            $importe = $res->el_monto;
            $saldo_anterior = floatval($saldo[$i]);
            $saldo[$res->id] = $tmp_saldo = ($saldo_anterior + $importe);
            //$saldo["k{$res->id}"] = $tmp_saldo;
            $periodo = substr($res->fecha_registro, 0, 7);
            // HArray::varDump($periodo, false);
            if ( $periodo < date('Y-m') && floatval($res->saldo) != $tmp_saldo )
            {
                $res->saldo = $tmp_saldo;
                $res->id_sucursal = -1;
                $res->save();
                //$res->save();
            }
            //HArray::varDump("{$i} > {$res->id}: " . $saldo_anterior . " + " . $importe, false);
            $i = $res->id;
        }
        #--
        if ( $resume )
        {
            $saldo = floatval(array_pop($saldo));
        }
        //self::actualizarSaldo($persona_id, $saldo);
        return $saldo;
    }

    public static function actualizarSaldo($cid, $monto, $add = true)
    {
        if ( !$add )
        {
            //hace un pago ?
            $monto *= -1;
        }
        #--
        $saldo = 0;
        if ( ($persona = Persona::find($cid)) && $monto )
        {
            $persona->saldo += $monto;
            $persona->otros_datos_json = array('revende' => 1, 'saldo_update' => date('Y-m-d'));
            $persona->save();
            $saldo = $persona->saldo;
        }
        return $saldo;
    }
}