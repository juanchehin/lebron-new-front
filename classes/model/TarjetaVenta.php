<?php



class TarjetaVenta extends MainModel

{

    protected $table = self::tablaTarjetaVenta;

    protected $primaryKey = "id_venta_tarjeta";

    public function hasGastos()

    {

        return $this->hasOne("id_venta_tarjeta", "user_id", "monto", "cantidad", "descripcion", "created_at");

    }


}