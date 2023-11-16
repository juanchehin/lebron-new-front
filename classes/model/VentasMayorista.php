<?php



class VentasMayorista extends MainModel

{

    protected $table = self::tablaVentaMayorista;

    protected $primaryKey = "idventa_mayorista";

    public function hasVentaMayoristas()

    {

        return $this->hasOne("idventa_mayorista", "id_cliente", "monto", "tipo_envio","direccion","estado_venta_mayorista","comprobante","created_at");

    }

    public function hasLineaVenta()

    {

        return $this->hasMany(LineaVentaMayoristas::class, "id_venta_mayorista", $this->primaryKey);

    }


}