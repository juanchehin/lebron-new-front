<?php



class ProductosVencidos extends MainModel

{

    protected $table = self::tablaProductosVencidos;

    protected $primaryKey = "idproductos_vencidos";

    public function hasProductosVencidos()

    {

        return $this->hasOne("idproductos_vencidos", "producto","monto", "fecha_vencimiento","observaciones","created_at","updated_at");

    }


}