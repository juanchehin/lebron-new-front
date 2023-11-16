<?php



class Pedidos extends MainModel

{

    protected $table = self::tablaPedidos;

    protected $primaryKey = "id_pedido";

    public function hasVentaMayoristas()

    {

        return $this->hasOne("id_pedido", "id_cliente", "monto", "tipo_envio","direccion","estado_venta_mayorista","comprobante","created_at","updated_at");

    }

    public function hasLineaVenta()

    {

        return $this->hasMany(LineaVentaMayoristas::class, "id_venta_mayorista", $this->primaryKey);

    }


}