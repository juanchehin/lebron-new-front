<?php



class Categoria extends MainModel

{

    const TIPO_CATEGORIA = 'CATEGORIA';

    const TIPO_SECCION = 'SECCION';

    const tipoCategoria = "categoria";

    const tipoMarca = "marca";

    const tipoSucursal = "sucursal";

    const PK = 'id_item';

    const ID_ITEM_PADRE = 'id_item_padre';

    const ctgPromo = 39;

    const ctgIndumentaria = 36;

    const ctgArticulosGym = 38;

    const mrkForrejeria = 132;

    const marcaUltraTech = 16;

    const marcaStar = 188;

    protected $table = self::tablaCategoria;

    public $primaryKey = self::PK;



    public function hasCategoria()

    {

        return $this->hasOne(self::class, $this->primaryKey, self::ID_ITEM_PADRE);

    }



    public function hasSubcategoria()

    {

        return $this->hasMany(self::class, self::ID_ITEM_PADRE, $this->primaryKey);

    }



    public function hasArticulo()

    {

        return $this->hasMany(Articulo::class, "id_categoria", $this->primaryKey);

    }



    public function hasArticuloMarca()

    {

        return $this->hasMany(Articulo::class, "id_marca", $this->primaryKey);

    }



    public function hasImagen()

    {

        return $this->hasOne(Imagen::class, "id_relacion", $this->primaryKey)->where('entidad', self::tablaCategoria);

    }



    public static function rubrosActivos(array $columns = array('*'))

    {

        return self::where('activo', 1)->select($columns)->get();

    }



    public function getTodasLasOpcionesAttribute()

    {

        $opciones = $this->hasComponente;

        if ( $this->hasCategoria )

        {

            #-- obtener las opciones de categoria padre

            $opciones = $opciones->merge($this->hasCategoria->hasComponente);

        }

        return $opciones;

    }



    public function getUtilidadAttribute()

    {

        return explode("|", $this->attributes['valor']);

    }



    public static function getRubros($id_parent = null, $activo = false)

    {

        if ( !is_null($id_parent) )

        {

            $where[self::ID_ITEM_PADRE] = $id_parent;

        }

        if ( $activo )

        {

            $where['activo'] = 1;

        }

        return self::where($where)->orderBy('orden')->get();

    }



    public static function categorias($search = null, $activo = false, $tipo = null)

    {

        $items = array();

        $where['borrado'] = 0;

        $where['tipo'] = $tipo ?: self::tipoCategoria;

        $where['id_item_padre'] = 0;

        if ( $activo )

        {

            $where['activo'] = 1;

        }

        $categorias = self::where($where)->where('nombre', "LIKE", "%{$search}%")->orderBy("orden")->orderBy('nombre')->get();

        foreach ($categorias as $categoria)

        {

            unset($where['id_item_padre']);

            $categoria['subitems'] = $categoria->hasSubcategoria()->where($where)->orderBy("orden")->get();

            //$items[] = $categoria;

        }

        return $categorias;

    }



    public function getImagenAttribute()

    {

        return $this->hasImagen()->first();

    }



    public function getSrcImageAttribute()

    {

        if ( !$this_image = $this->getImagenAttribute() )

        {

            $this_image = new Imagen();

        }

        return $this_image->image_crop_src;

    }



    public function getIdCategoriaAttribute()

    {

        return $this->attributes[$this->primaryKey];

    }



    public function getCategoriaAttribute()

    {

        $rubro = $this->attributes['nombre'];

        if ( $this->attributes[self::ID_ITEM_PADRE] )

        {

            $rubro .= " ({$this->hasCategoria->titulo})";

        }

        return mb_strtoupper($rubro);

    }



    public function getTituloAttribute()

    {

        return mb_strtoupper($this->attributes['nombre']);

    }



    public static function admRubro($id_rubro = null, $nombre = null, $id_padre = 0, $tipo = null)

    {

        $_nombre = preg_replace("#\(.+#", "", strtolower($nombre));

        $rubro = self::findOrNew($id_rubro);

        $rubro->id_rubro_padre = $id_padre;

        $rubro->nombre = trim($_nombre);

        if ( $tipo )

        {

            $rubro->tipo = strtolower($tipo);

        }

        $rubro->save();

        #--

        return $rubro;

    }



    public function borrar()

    {

        $this->borrado = 1;

        $this->activo = 0;

        $this->save();

        $this->hasSubcategoria()->update(['borrado' => 1]);

    }

}