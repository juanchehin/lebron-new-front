<?php

class Atributo extends MainModel
{
	protected $table = self::TBL_ATRIBUTO;
	public $primaryKey = "id_atributo";
	const ID_PRODUCTO = "id_producto";
	
	public function hasArticulo()
	{
		return $this->hasOne("Articulo", self::ID_PRODUCTO, self::ID_PRODUCTO);
	}
}