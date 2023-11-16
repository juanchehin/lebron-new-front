<?php



class Iva extends MainModel

{

    protected $table = self::tablaIva;

    protected $primaryKey = "id_iva";

    public function hasGastos()

    {

        return $this->hasOne("id_iva", "user_id", "monto", "cantidad", "descripcion", "created_at");

    }


}