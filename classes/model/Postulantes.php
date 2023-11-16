<?php



class Postulantes extends MainModel

{

    protected $table = self::tablaPostulantes;

    protected $primaryKey = "id_postulante";

    public function hasPostulantes()

    {

        return $this->hasOne("id_postulante", "apellidos", "nombres", "telefono", "dni", "email", "domicilio", "puesto", "cv");

    }


}