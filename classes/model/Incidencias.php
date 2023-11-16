<?php



class Incidencias extends MainModel

{

    protected $table = self::tablaIncidencia;

    protected $primaryKey = "id";

    public function hasGastos()

    {

        return $this->hasOne("id");

    }


}