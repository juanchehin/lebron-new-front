<?php

class Componente extends MainModel
{
    protected $table = self::tablaComponente;
    protected $primaryKey = "id";
    const tipoSabor = "sabor";

    public function getLabelAttribute()
    {
        return ucwords($this->attributes['nombre']);
    }

    public function getArrayBodyAttribute()
    {
        return (array)json_decode($this->attributes['body'], true);
    }

    public function setJsonBodyAttribute($value)
    {
        $data = array_merge($value, $this->getArrayBodyAttribute());
        $this->attributes['body'] = json_encode($data);
        return;
    }
}