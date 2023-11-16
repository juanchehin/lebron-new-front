<?php

class ArticuloDetalle extends MainModel
{
    protected $table = self::tablaArticuloDetalle;
    protected $primaryKey = "id";

    public function hasArticulo()
    {
        return $this->hasOne("Articulo", "id_producto", "id_articulo");
    }

    public function hasImagen()
    {
        return $this->hasMany("Imagen", "id_relacion", $this->primaryKey);
    }

    public function hasCategoria()
    {
        return $this->hasOne("Categoria", ($key = "id_categoria"), $key);
    }

    public function getArticuloPrincipalAttribute()
    {
        return $this->hasArticulo->producto;
    }
}