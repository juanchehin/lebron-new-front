<?php



class VentaQuimico extends MainModel

{

    protected $table = self::tablaVentaQuimico;

    protected $primaryKey = "id_venta_quimico";

    public function hasGastos()

    {

        return $this->hasOne("id_venta_quimico", "fecha", "cliente", "direccion_envio","telefono","cadete","abonado","tipo","monto","observaciones");

    }

    public function hasLineaVenta()

    {

        return $this->hasMany(LineaVentaMayoristas::class, $this->primaryKey, $this->primaryKey);

    }


}