<?php



use Illuminate\Database\Eloquent\Model;



class MainModel extends Model

{

    #-- T A B L A S --

    const tablaUsuario = "usuario";

    const TBL_INCIDENCIA = "incidencia";

    const TBL_ARTICULO = "producto";

    const TBL_ATRIBUTO = "atributo";

    const tablaMovimiento = "movimiento";

    const tablaCaja = "caja";

    const tablaArticuloDetalle = "articulo_detalle";

    const tablaCategoria = "categoria";

    const tablaComponente = "componente";

    const tablaConcepto = "concepto";

    const tablaImagen = "imagen";

    const tablaIncidencia = "incidencia";

    const tablaPersona = "persona";

    const tablaVenta = "venta";

    const tablaLineaVenta = "linea_venta";

    const tablaGastos = "gastos";

    const tablaIva = "movimientos_iva";
    
    const tablaPostulantes = "postulantes";

    const tablaVentaQuimico = "venta_quimicos";  
    
    const tablaFacturacion = "facturacion";   
    
    const tablaVentaMayorista = "ventas_mayoristas";

    const tablaLineaVentaQuimico = "linea_venta_quimico";    

    const tablaLineaVentaMayoristas = "lineas_venta_mayorista";    

    const tablaCuentasCorrientes = "cuentas_corrientes";

    const tablaProductosVencidos = "productos_vencidos";

    const tablaMovimientosInversores = "movimientos_inversores";

    const tablaPedidos = "pedidos";

    const tablaIncidencias = "incidencias";

    const tablaTarjetaVenta = "nro_tarjeta_venta";

    #--

    const monedaDolar = "USD";

    const monedaPesos = "ARS";

    const unidadPorcentaje = "%";

    public $timestamps = false;

    public static $_EXTENSION_IMAGEN = array('png', 'gif', 'jpg', 'jpeg', 'bmp');

    public static $_monedas = array(self::monedaDolar, self::monedaPesos);



    public static function setConfig($value, $overwrite = false)

    {

        if ( !$overwrite )

        {

            $value = array_merge(static::config(), $value);

        }

        file_put_contents("conf/tsconfig.json", json_encode((array)$value));

    }





    public static function config()

    {

        return (array)json_decode(file_get_contents("conf/tsconfig.json"), true);

    }



    public static function confKey($key)

    {

        return static::config()[$key];

    }



    public static function getInfoDolar()

    {

        $data = (array)json_decode(file_get_contents($dolar_file = "conf/dolar.json"), true);

        return $data;

    }



    public static function setInfoDolar($data)

    {

        //$dolar =

    }

}

