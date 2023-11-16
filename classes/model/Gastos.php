<?php



class Gastos extends MainModel

{

    protected $table = self::tablaGastos;

    protected $primaryKey = "id_gasto";

    public function hasGastos()

    {

        return $this->hasOne("id_gasto", "user_id", "monto", "cantidad", "descripcion", "created_at");

    }


}