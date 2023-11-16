<?php



class Articulo extends MainModel

{

    protected $table = self::TBL_ARTICULO;

    public $primaryKey = "id_producto";

    const prcMayorista = "mayorista";

    const tipoSucursal = "sucursal";

    const varios = 1329;

    const shippingCost = 3;

    public static $_SABORES = array();



    public static $_UNIDADES = array("Kg", "Gr", "Ml", "Libras", "Mg", "Un", "mm", "Capsulas", "Servicios", "Talle");



    public function hasAtributo()

    {

        return $this->hasMany(self::class, "id_parent", $this->primaryKey);

    }



    public function hasParent()

    {

        return $this->hasOne(self::class, $this->primaryKey, "id_parent");

    }



    public function hasCategoria()

    {

        return $this->hasOne(Categoria::class, "id_item", "id_categoria");

    }



    public function hasMarca()

    {

        return $this->hasOne(Categoria::class, "id_item", "id_marca");

    }



    public function hasLineaVenta()

    {

        return $this->hasMany(LineaVenta::class, $this->primaryKey, $this->primaryKey);

    }



    public function hasImagen()

    {

        return $this->hasMany(Imagen::class, "id_relacion", $this->primaryKey)->where('entidad', $this->table);

    }



    public function getNombreAttribute()

    {

        return mb_strtoupper($this->attributes['producto']);

    }



    public function getMarcaAttribute()

    {

        $marca = null;//$this->attributes['marca'];

        if ( $id_marca = $this->attributes['id_marca'] )

        {

            $marca = $this->hasMarca->titulo;

        }

        return mb_strtoupper($marca);

    }



    public function getCantidadStringAttribute()

    {

        $data = null;

        $data[0] = "Sin stock";

        foreach ($this->cantidad_array as $k => $v)

        {

            if ( !($local = Local::$_LOCALES[$k]) || !$v )

            {

                continue;

            }

            unset($data[0]);

            $string = "{$local} : <b>{$v}</b>";

            if ( $this->attributes['stock_alerta'] >= $v )

            {

                $string = "<mark>{$string}</mark>";

            }

            $data[] = "<p style='margin:0;font-size:13px;'>{$string}</p>";

        }

        return implode("", $data);

    }



    public function setCantidadJsonAttribute(array $value)

    {

        //$data = array_merge($this->cantidad_array, $value);

        $this->attributes['cantidad'] = json_encode($value, JSON_NUMERIC_CHECK);

        return;

    }



    public function getCantidadArrayAttribute()

    {

        return (array)json_decode($this->attributes['cantidad'], true);

    }



    public function stockUpdate($id_local, $cantidad, $id_local_destino, $add = false)

    {

        $stock = $this->getCantidadArrayAttribute();

        $arr_stck = array();

        $cantidad_actual = $quantity = floatval($stock[$id_local]);

        $operacion = "Venta/Egreso";

        if ( $add )

        {

            $cantidad_actual += $cantidad;

            $operacion = "Ingreso";

        }

        else

        {

            $cantidad_actual -= $cantidad;

            $quantity = $cantidad_actual;

            if ( $id_local_destino )

            {

                $arr_stck[2] = floatval($stock[$id_local_destino]);

                $stock[$id_local_destino] += $cantidad;

                $operacion = "Traspaso a " . Local::$_LOCALES[$id_local_destino] . " ({$stock[$id_local_destino]})";

            }

        }

        #--

        if ( $cantidad_actual <= 0 )

        {

            $cantidad_actual = $quantity = 0;

        }

        //$flagStock = ($operacionCompra || $operacionTraspaso || !$cantidad_actual);

        $stock[$id_local] = $cantidad_actual;

        $arr_stck[1] = $quantity;

        $this->cantidad_json = $stock;

        $this->save();

        $log = $this->attributes['id_producto'] . " - " . strip_tags($this->getNombreProductoAttribute()) . "@Stock:{$cantidad_actual}@Sucursal:" . Local::$_LOCALES[$id_local] . "@Operacion:{$operacion};";

        HFunctions::filePutContent("media/meta/stock_log_" . date('d-m-Y') . ".txt", $log . "\r\n", FILE_APPEND);

        ksort($arr_stck);

        return implode("&", $arr_stck);

    }



    public function getCategoriaAttribute()

    {

        $categoria = null;

        if ( $this->attributes['id_categoria'] > 0 )

        {

            $categoria = Categoria::find($this->attributes['id_categoria'])->titulo;

        }

        return $categoria;

    }



    #--

    public function getItemsPromoAttribute()

    {

        $itemsPromo = array();

        if ( ($ids_items = $this->getArrayItemsPromoAttribute()) )

        {

            $itemsPromo = self::whereRaw("!borrado AND id_producto IN ('" . implode("','", $ids_items) . "')")->get();

        }

        return $itemsPromo;

    }



    public function getCantidadOnlineAttribute()

    {

        if ( $itemsPromo = $this->getItemsPromoAttribute() )

        {

            $arr_stock = array();

            foreach ($itemsPromo as $i => $item)

            {

                if ( $item->hasAtributo->count() )

                {

                    foreach ($item->hasAtributo->push($item) as $child)

                    {

                        $arr_stock[$item->id_producto] += intval($child->cantidad_online);

                    }

                }

                else

                {

                    $arr_stock[$item->id_producto] = intval($item->cantidad_online);

                }



            }

            $stock_values = array_values($arr_stock);

            sort($stock_values);

            //file_put_contents("conf/tmp.dat", json_encode($stock_values) . "\n\r", FILE_APPEND);

            return array_shift($stock_values);

        }

        $maxStock = 20;

        $cantidades = $this->getCantidadArrayAttribute();

        unset($cantidades[Local::negocioLules]);

        //unset($cantidades[Local::mitreNegocio]);

        unset($cantidades[Local::bsas42]);

        unset($cantidades[Local::ventaPagina]);

        $stock = array_sum(array_values($cantidades));

        $stock = floatval($cantidades[Local::mitreNegocio]);

        if ( $stock > $maxStock )

        {

            $stock = $maxStock;

        }

        #--

        if ( $stock <= 3 )

        {

            $stock = 0;

        }

        return $stock;

    }



    #--



    public function getArrayCategoriaAttribute()

    {

        return (array)json_decode($this->attributes['id_categoria'], true);

    }



    public function setCategoriaJsonAttribute($values)

    {

        $data = array_merge($this->getArrayCategoriaAttribute(), $values);

        $this->attributes['id_categoria'] = json_encode($data, JSON_NUMERIC_CHECK);

        return;

    }



    public function getArrayItemsPromoAttribute()

    {

        return (array)json_decode($this->attributes['marca'], true);

    }



    public function setJsonItemsPromoAttribute($values)

    {

        $data = array_merge($this->getArrayItemsPromoAttribute(), $values);

        $this->attributes['marca'] = json_encode((array)$values, JSON_NUMERIC_CHECK);

        return;

    }



    public function getArticuloSaborAttribute()

    {

        if ( array_key_exists($this->attributes['sabor'], static::$_SABORES) )

        {

            $this->attributes['sabor'] = static::$_SABORES[$this->attributes['sabor']];;

            $this->save();

        }

        $sabor = $this->attributes['sabor'];

        return ucwords($sabor);

    }



    public function getVarianteAttribute()

    {

        $variante = $this->attributes['sabor'];

        if ( $this->getNoSaborAttribute() )

        {

            $variante = $this->attributes['peso'];

        }

        return mb_strtoupper($variante);

    }



    public function getNoSaborAttribute()

    {

        $sabor = mb_strtolower($this->attributes['sabor']);

        return in_array($sabor, ["", "otro", "ninguno"]);

    }



    public function getSaborLabelAttribute()

    {

        if ( $sabor = $this->attributes['sabor'] )

        {

            return "<b>Variante:</b> {$this->getArticuloSaborAttribute()}";

        }

    }



    public function getPesoLabelAttribute()

    {

        if ( $peso = $this->attributes['peso'] )

        {

            return "<b>Medida:</b> " . mb_strtoupper($this->attributes['peso']);

        }

    }



    public function getIntPesoAttribute()

    {

        //return preg_replace("#[^\d+]#", null, $this->attributes['peso']);

        return preg_replace("#\s+.+#", "", $this->attributes['peso']);

    }



    public function getUnidadAttribute()

    {

        return preg_replace("#.+\s+#", "", $this->attributes['peso']);

    }



    public function getEsImportadoAttribute()

    {

        return ($this->hasParent->id_sucursal || $this->attributes['id_sucursal']);

    }



    public function getPrecioVentaAttribute()

    {

        return floatval($this->getArrayPreciosAttribute()['publico']);

    }



    public function getArrPrecioCompraAttribute()

    {

        $precio_compra = $this->hasParent->precio_compra ?: $this->attributes['precio_compra'];



        list($valor, $moneda) = explode("|", trim($precio_compra));

        return array(

            'valor' => floatval($valor),

            'unidad' => $moneda

        );

    }



    public function getArrUtilidadAttribute()

    {

        $utilidad = $this->attributes['utilidad'] ?: $this->hasCategoria->valor;

        if ( ($parent = $this->hasParent) )

        {

            $utilidad = $parent->utilidad ?: $parent->hasCategoria->valor;

        }

        #--

        if ( !$utilidad )

        {

            $utilidad = self::confKey("utilidad");

        }

        list($valor, $unidad) = explode("|", $utilidad);



        return array(

            'valor' => $valor,

            'unidad' => $unidad

        );

    }



    public function getArrayPreciosAttribute()

    {

        $utilidad = $this->getArrUtilidadAttribute();

        $pc = $this->getArrPrecioCompraAttribute();

        $unidad = $utilidad['unidad'];

        $valor = $utilidad['valor'];

        #--

        $signo = "+";

        if ( $unidad == self::monedaDolar )

        {

            $valor *= floatval(!$this->getEsImportadoAttribute() ? self::confKey("precio_dolar") : self::confKey("dolar_paralelo"));

        }

        elseif ( $unidad == self::unidadPorcentaje )

        {

            $valor = ($valor / 100) + 1;

            $signo = "*";

        }

        eval("\$res = \$valor {$signo} \$this->getPrecioArsAttribute();");

        $precio['publico'] = round(floatval($res));

        $precio[self::prcMayorista] = $this->getPrecioArsAttribute();

        if ( $pc['unidad'] == self::monedaDolar )

        {

            $precio['usd '] = $pc['valor'];

        }

        return $precio;

    }



    public function getPrecioArsAttribute()

    {

        $precio = $this->getArrPrecioCompraAttribute();

        $valor = $precio['valor'];

        $precio_dolar = $this->getEsImportadoAttribute() ? self::confKey("dolar_paralelo") : self::confKey("precio_dolar");

        if ( $precio['unidad'] == self::monedaDolar )

        {

            $valor = round(floatval($valor) * floatval($precio_dolar));

        }

        return $valor;

    }



    public function getEcommercePrecioAttribute()

    {

        $recargo = 0;

        $value = floatval($this->attributes['precio']);

        #--

        if ( !$value )

        {

            $value = $this->getArrayPreciosAttribute()['publico'];

            $recargo = floatval(static::confKey('fee_mp')) + floatval(static::confKey('comision_mp'));

        }

        #--

        if ( $this->attributes['id_categoria'] == Categoria::ctgPromo )

        {

            if ( $itemsPromo = $this->getItemsPromoAttribute() )

            {

                $value = 0;

                foreach ($itemsPromo as $itemPromo)

                {

                    $value += floatval($itemPromo->precio ?: $itemPromo->ecommerce_precio);

                }

            }

            //comentar esta linea si se pondrá recargo a las promociones tambien

            $recargo /= 1.5;

        }

        #--

        $precio = round($value * (($recargo / 100) + 1));

        $id_marca = $this->attributes['id_marca'];

        if ( $id_marca == Categoria::marcaUltraTech )

        {

            $precio *= 1.73;

        }

        elseif ( $id_marca == "" )

        {



        }

        $precio += ($precio * floatval($this->getRecargoDescuentoAttribute()) / 100);

        if ( !$precio || !$this->getPrecioArsAttribute() )

        {

            $precio = $this->getOnlinePriceAttribute();

        }

        return $precio;

    }



    public function getRecargoDescuentoAttribute()

    {

        $valor = $this->attributes['precio_online'];

        if ( ($parent = $this->hasParent) )

        {

            $valor = $parent->precio_online;

        }

        return floatval($valor);

    }



    public function getOnlinePriceAttribute()

    {

        if ( $parent = $this->hasParent )

        {

            if ( !$precio = $parent->precio )

            {

                $precio = $parent->precio;

            }

        }

        elseif ( !$precio = $this->attributes['precio_online'] )

        {

            $precio = $this->attributes['precio'];

        }

        return $precio;

    }



    public function getDetalleAttribute()

    {

        $detalle[] = $this->marca;

        $detalle[] = $this->sabor_label;

        $detalle[] = $this->peso_label;

        return implode(", ", array_filter($detalle));

    }



    public function getItemAttribute()

    {

        $articulo = $this->getNombreAttribute();

        $articulo .= " [{$this->attributes['peso']}]";

        if ( !$this->getNoSaborAttribute() )

        {

            $articulo .= " (" . $this->getArticuloSaborAttribute() . ")";

        }

        $articulo .= ". {$this->getMarcaAttribute()}";

        return $articulo;

    }



    public function getNombreProductoAttribute()

    {

        $titulo = $this->getNombreAttribute();



        $detalle[] = $this->marca;

        $detalle[] = $this->getSaborLabelAttribute();

        $detalle[] = $this->attributes['peso'];

        $titulo .= " (" . implode('. ', array_filter($detalle)) . ")";

        #--

        return $titulo;

    }



    public function getDimensionArrayAttribute()

    {

        if ( !($dimension = (array)json_decode($this->attributes['dimension'], true)) )

        {

            $dimension = (array)json_decode($this->hasParent->dimension, true);

        }

        return $dimension;

    }



    public function setJsonDimensionAttribute($dimension)

    {

        $values = array_merge($this->getDimensionArrayAttribute(), (array)$dimension);

        $this->attributes['dimension'] = json_encode($values, true);

        return;

    }



    public function imagenes($first = false, $default = true)

    {

        $imagenes = array();

        $collection = $this->hasAtributo()->get()->push($this);

        //$collection = self::whereRaw("id_parent='{$this->id_producto}' OR id_producto='{$this->id_producto}'")->get();

        foreach ($collection as $index => $item)

        {

            foreach ($item->hasImagen as $imagen)

            {

                $src_image = $imagen->image_crop_src;

                if ( file_exists($src_image) )//&&!in_array($src_image, $imagenes))

                {

                    $imagenes[] = $src_image;

                }

            }

        }

        #--

        if ( !$imagenes && $default )

        {

            $imagenes[] = Imagen::defaultImage;

        }

        return $first ? array_shift($imagenes) : $imagenes;

    }



    public static function getProducto($id_producto = null, $codigo = null, $activo = false)

    {

        $condition['borrado'] = 0;

        $where[] = "!`borrado`";

        if ( $id_producto > 0 )

        {

            $where[] = "`id_producto` = '{$id_producto}'";

        }

        #--

        if ( $activo )

        {

            $where[] = "`activo`";

        }

        #--

        if ( $codigo )

        {

            $where[] = "`codigo` = '{$codigo}'";

        }

        return self::whereRaw(implode(" AND ", $where))->first();

    }

}