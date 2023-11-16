<?php



class FacturacionElectronica extends MainModel

{

    protected $table = self::tablaFacturacion;

    protected $primaryKey = "id_facturacion";

    public function hasFacturacion()

    {

        return $this->hasOne("id_facturacion", "id_venta", "cae", "fecha_vencimiento_cae","cuil_cliente","voucher_number","tipo_comprobante","punto_de_venta","monto","qr","estado","created_at","updated_at");

    }


}