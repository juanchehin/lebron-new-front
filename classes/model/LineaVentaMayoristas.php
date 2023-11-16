<?php



class LineaVentaMayoristas extends MainModel

{

    protected $table = self::tablaLineaVentaMayoristas;

    public $primaryKey = "idlineas_venta_mayorista";

    const fkVenta = "id_venta_mayorista";



    public function hasVenta()

    {

        return $this->hasOne(Venta::class, self::fkVenta, self::fkVenta);

    }



    public function hasArticulo()

    {

        return $this->hasOne(Articulo::class, "id_producto", "id_producto");

    }



    public function getProductoAttribute()

    {

        $articulo = $this->hasArticulo;

        return $this->attributes['id_producto'] . " - " . mb_strtoupper($articulo->nombre_producto);

    }



    public function getFechaRegistroAttribute()

    {

        $fecha_hora = $this->attributes['fecha_hora'];

        if ( !$fecha_hora || $fecha_hora == "0000-00-00 00:00:00" )

        {

            $fecha_hora = $this->hasVenta->fecha_hora;

        }

        return HDate::dateFormat($fecha_hora, "d/m/Y H:i:s");

    }



    public function getArrayFlgAttribute()

    {

        if ( ($data['tipo'] = str_ireplace("_", " ", $this->attributes['atributo'])) == "Traspaso" )

        {

            $data['destino'] = $this->attributes['flag'];

        }

        $data['origen'] = floatval($this->attributes['valor']);

        return $data;

    }



    public function getArrayFlagAttribute()

    {

        return (array)json_decode($this->attributes['flag'], true);

    }



    public function getOperacionTraspasoAttribute()

    {

        if ( $operacion = $this->hasVenta )

        {

            return $operacion->es_traspaso;

        }

        return preg_match("#" . Venta::tpTraspaso . "#i", $this->attributes['atributo']);

    }



    public function getOperacionIngresoAttribute()

    {

        if ( $operacion = $this->hasVenta )

        {

            return $operacion->operacion_ingreso;

        }

        return preg_match("#" . Venta::tpIngreso . "#i", $this->attributes['atributo']);

    }

}