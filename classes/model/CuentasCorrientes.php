<?php



class CuentasCorrientes extends MainModel

{

    protected $table = self::tablaCuentasCorrientes;

    protected $primaryKey = "idcuentas_corrientes";

    public function hasVentaMayoristas()

    {

        return $this->hasOne("idcuentas_corrientes", "monto", "fecha","cliente","abonado","observaciones","created_at","updated_at");

    }


}