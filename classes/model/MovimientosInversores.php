<?php



class MovimientosInversores extends MainModel

{

    protected $table = self::tablaMovimientosInversores;

    protected $primaryKey = "idmovimientos_inversores";

    public function hasVentaMayoristas()

    {

        return $this->hasOne("idmovimientos_inversores", "id_inversor", "tipo_pago","fecha_vencimiento","observaciones","estado_mi","created_at","updated_at");

    }


}