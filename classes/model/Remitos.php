<?php



class Remitos extends MainModel

{

    protected $table = self::tablaVenta;

    protected $primaryKey = "id_venta";

    public function hasGastos()

    {

        return $this->hasOne("id_venta", "cliente", "tipo", "fecha_hora");

    }


}