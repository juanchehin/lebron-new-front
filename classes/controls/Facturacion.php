<?php

//ini_set("display_errors", "On");

class Facturacion

{

    private $entidad_cuit;

    private $entidad_direccion;

    private $entidad_nombre;

    private $entidad_inicio;

    private $entidad_condicion_iva;

    private $punto_venta;

    private $sucursal;

    private $ingresos_brutos;

    private $factura_codigo;

    private $factura_emision;

    private $factura_desde;

    private $factura_hasta;

    private $factura_vto;

    private $factura_numero;

    private $factura_cae;

    private $factura_tipo_vta = "Contado";

    private $factura_vto_cae;

    private $factura_items = array();

    private $factura_total = 0;

    private $cliente_dni;

    private $cliente_nombre;

    private $cliente_direccion = "-";
    
    private static $_letraFactura = array(

        "01" => "A",

        "06" => "B",

        "11" => "C",

        "00" => "X",

        "91" => "R"

    );



    /**

     * @param mixed $entidad_cuit

     */

    public function setEntidadCuit($entidad_cuit)

    {

        $this->entidad_cuit = $entidad_cuit;

        return;

    }



    /**

     * @param mixed $entidad_direccion

     */

    public function setEntidadDireccion($entidad_direccion)

    {

        $this->entidad_direccion = $entidad_direccion;

        return;

    }



    /**

     * @param mixed $entidad_nombre

     */

    public function setEntidadNombre($entidad_nombre)

    {

        $this->entidad_nombre = $entidad_nombre;

        return;

    }



    public function setEntidadSucursal($value)

    {

        $this->sucursal = $value;

        return;

    }

    /**

     * @param mixed $entidad_inicio

     */

    public function setEntidadInicio($entidad_inicio)

    {

        $this->entidad_inicio = $entidad_inicio;

        return;

    }



    /**

     * @param mixed $entidad_condicion_iva

     */

    public function setEntidadCondicionIva($entidad_condicion_iva)

    {

        $this->entidad_condicion_iva = $entidad_condicion_iva;

        return;

    }



    /**

     * @param mixed $numero_factura

     */

    public function setFacturaNumero($numero_factura)

    {

        $this->factura_numero = str_pad($numero_factura, 11, "0", 0);

        return;

    }



    /**

     * @param array $items

     */

    public function setFacturaItems(array $items = array())

    {

        $this->factura_items[] = $items;

        return;

    }



    /**

     * @param float $total

     */

    public function setFacturaTotal($total)

    {

        $this->factura_total = $total;

        return;

    }



    public function setPuntoVenta($punto_venta)

    {

        //$this->punto_venta = str_pad($punto_venta, 5, "0", 0);

        $this->punto_venta = $punto_venta;

        return;

    }



    /**

     * @param mixed $codigo_factura

     */

    public function setFacturaCodigo($codigo_factura)

    {

        $this->factura_codigo = str_pad($codigo_factura, 2, "0", 0);

        return;

    }



    /**

     * @param mixed $factura_emision

     */

    public function setFacturaEmision($factura_emision)

    {

        $this->factura_emision = $factura_emision;

        return;

    }



    /**

     * @param mixed $factura_hasta

     */

    public function setFacturaHasta($factura_hasta)

    {

        $this->factura_hasta = $factura_hasta;

        return;

    }



    /**

     * @param mixed $factura_desde

     */

    public function setFacturaDesde($factura_desde)

    {

        $this->factura_desde = $factura_desde;

        return;

    }



    /**

     * @param mixed $factura_vto

     */

    public function setFacturaVto($factura_vto)

    {

        $this->factura_vto = $factura_vto;

        return;

    }



    /**

     * @param mixed $factura_cae

     */

    public function setFacturaCae($factura_cae)

    {

        $this->factura_cae = $factura_cae;

        return;

    }



    /**

     * @param mixed $factura_vto_cae

     */

    public function setFacturaVtoCae($factura_vto_cae)

    {

        $this->factura_vto_cae = $factura_vto_cae;

        return;

    }



    /**

     * @param string $factura_tipo_vta

     */

    public function setFacturaTipoVta($factura_tipo_vta)

    {

        $this->factura_tipo_vta = $factura_tipo_vta;

        return;

    }



    /**

     * @param mixed $cliente_dni

     */

    public function setClienteDni($cliente_dni)

    {

        $this->cliente_dni = $cliente_dni;

        return;

    }



    /**

     * @param mixed $cliente_nombre

     */

    public function setClienteNombre($cliente_nombre)

    {

        $this->cliente_nombre = $cliente_nombre;

        return;

    }



    /**

     * @param mixed $cliente_direccion

     */

    public function setClienteDireccion($cliente_direccion)

    {

        $this->cliente_direccion = $cliente_direccion;

        return;

    }



    public function drawFactura($pdf = true)

    {

        ob_start();

        $codigo = $this->factura_codigo;

        $columns = array_keys($this->factura_items[0]);

        $cantidad_lineas = count($this->factura_items);

        $entidad = $this->entidad_nombre;

        $maxLines = 20;

        if ( $vertical = ($cantidad_lineas > $maxLines) )

        {

            $maxLines = 48;

        }

        if ( ($blank_lines = ($maxLines - $cantidad_lineas)) < 0 )

        {

            $blank_lines = 0;

        }

        #--

        /*if ( $cantidad_lineas > 19 )

        {

            $maxLines = 28;

        }*/

        #-- Blank lines

        for ($x = 0; $x < $blank_lines; $x++)

        {

            foreach ($columns as $column)

            {

                $item[$column] = "&nbsp;";

            }

            $this->setFacturaItems($item);

        }

        $esPresupuesto = ($this->factura_codigo == "00");

        ?>

        <style type="text/css">

            @page {

                margin: .8cm 1cm;

            }



            .container {

                font-size: 10px;

                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;

                width: <?=($vertical ? 100 : 49.5)."%"?>;

                display: inline-block;

            }



            .square {

                width: 12px;

                height: 12px;

                border: 1px solid #333;

                display: inline-block;

                margin: 2px;

            }



            .fila p {

                margin: 8px 0;

            }



            table th {

                background: #cccccc;

                border: 1px solid #333;

            }



            tr td p {

                margin: 3px;

            }



            table td {

                padding: 1px 4px;

            }



            tr.no-border td {

                border: none;

            }

        </style>

        <div class="container">

            <table border="1" class="tbl-factura" cellspacing="0" align="" style="width: 100%;page-break-inside: avoid">

                <tr class="no-border">

                    <td valign="top" width="44%" style="text-align: center">

                        <div style="text-align: center;padding:6px">

                            <?php if ( false ): ?>

                                <img alt="<?= $entidad ?>" src="<?= $this->punto_venta ?>/static/logo-lebron.jpg" width="200">

                            <?php else: ?>

                                <h1><?= $entidad ?></h1>

                            <?php endif; ?>

                            <p><?= $this->entidad_direccion ?></p>

                            <p style="font-weight:600"><?= $codigo ? $this->entidad_condicion_iva : "&nbsp;" ?></p>

                            <?php if ( $sucursal = $this->sucursal ) : ?>

                                <h4>Sucursal <?= $sucursal ?></h4>

                            <?php endif; ?>

                            <p>Vendedor : ..............................</p>

                        </div>

                    </td>

                    <td style="width:12%;text-align:center;" valign="top">

                        <div style="font-size:28px;font-weight:bold;margin-top:-2px;border: 2px solid #333">

                            &nbsp;<?= static::$_letraFactura[$codigo] ?>

                            <p style="font-size:11px; margin:0"><?= $codigo ? ("COD {$codigo}") : "&nbsp;" ?></p>

                        </div>

                        <div style="width:0;border:1px solid #000;height:74px;margin-left:50%"></div>

                    </td>

                    <td colspan="2" valign="top" align="center">

                        <h2 style="margin:2px"><?= $esPresupuesto ? "PRESUPUESTO" : "FACTURA" ?></h2>

                        <h2 style="margin-top:4px">Nro: <?= $this->factura_numero ?></h2>

                        <h3 style="margin:5px;font-weight: normal">Fecha: <?= $this->factura_emision ?></h3>

                        <p style="margin-top:10px"><b>CUIT:</b> 20-35807355-8</p>

                        <p><b>Ing. Brutos:</b> <?= $this->ingresos_brutos = preg_replace("#\-#", null, $cuit) ?></p>

                        <?php if ( $this->entidad_inicio ): ?>

                            <p><b>Inicio de Act: </b> <?= $this->entidad_inicio ?></p>

                        <?php endif; ?>

                    </td>

                </tr>

                <tr>

                    <td colspan="4">

                        <p><b>Nombre:</b> <?= $this->cliente_nombre ?></p>

                        <p><b>Direcci&oacute;n:</b> <?= $this->cliente_direccion ?></p>

                    </td>

                </tr>

                <tr>

                    <td colspan="3" valign="middle">

                        <b style="width:7%;">I.V.A |</b>

                        <div style="width: 93%; display:inline-block;">

                            <?php

                            foreach (["Efectivo.", "Cta. Cte.", "Transferencia", "QR", "Credito","Regalo"] as $i => $item)

                            {

                                echo "<div style='" . ($css = "width:26%;display:inline-block") . "'>{$item}<i class='square'></i></div> ";

                                if ( $i == 2 )

                                {

                                    echo "<br/>";

                                }

                            }

                            ?>

                        </div>

                    </td>

                    <td>DNI/CUIT: <?= $this->cliente_dni ?></td>

                </tr>

                <tr>

                    <td colspan="3">

                        <b style="width: 10%">Cond. de Venta |</b>

                        <?php

                        if ( ($medioPago = $this->factura_tipo_vta) )

                        {

                            echo "<i>$medioPago</i>";

                        }

                        else

                        {

                            // foreach (["Contado", "Tarj. Cred.", "Cta. Cte."] as $i => $item)

                            // {

                            //     echo "<div style='{$css}'>{$item}<i class='square'></i></div> ";

                            // }

                        }

                        ?>

                    </td>

                    <td>Remito N&deg;:</td>

                </tr>

                <tr>

                    <td colspan="4" style="padding: 0;border:none">

                        <table border="1" width="100%" cellspacing="0">

                            <tr>

                                <?php foreach ($columns as $key) : ?>

                                    <th style="text-transform: uppercase"><?= preg_replace("#[\_\-]#", " ", $key) ?></th>

                                <?php endforeach; ?>

                            </tr>

                            <tbody>

                            <?php foreach ($this->factura_items as $data) : ?>

                                <tr>

                                    <?php foreach ($columns as $key) : ?>

                                        <td style="text-align:<?= is_numeric($data[$key]) ? 'center' : 'left' ?>"><?= $data[$key] ?></td>

                                    <?php endforeach; ?>

                                </tr>

                            <?php endforeach; ?>

                            <tr>

                                <td colspan="<?= count($columns) - 1 ?>" align="right">TOTAL</td>

                                <td align="center"><?= $total_facturado = self::numberFormat($this->factura_total) ?></td>

                            </tr>

                            </tbody>

                        </table>

                    </td>

                </tr>

                <?php if ( $esPresupuesto ): ?>

                    <tr>

                        <td colspan="4">El precio cotizado puede sufrir variacion con respecto a inflaci&oacute;n o cambio de moneda extranjera, sugerimos hacer el dep&oacute;sito bien confirme su pedido.</td>

                    </tr>

                <?php endif; ?>

            </table>

            Observaciones: 
            <textarea rows="5" cols="33">
            </textarea>
           

        </div>


        <?php

        $html = ob_get_clean();

        if ( !$vertical )

        {

            $html .= $html;

        }

        #--

        if ( $pdf && class_exists("ExportOpts") )

        {

            ExportOpts::exportar($html, true, !$vertical);

            die;

        }

        return ob_get_clean();

    }


    // ***** Barcode ||| *******
    public function drawBarcode($pdf = true,$barcode)

    {

        ?>
        <style type="text/css">

            div.b128{
                border-left: 1px black solid;
	            height: 60px;
            } 

        </style>


        <?php

            echo $this->bar128($barcode);

        ?>

        <?php
        
        $html = ob_get_clean();

        #--

        if ( $pdf && class_exists("ExportOpts") )

        {

            ExportOpts::exportarBarcode($html, true, false);

            die;

        }

        return ob_get_clean();

    }

    ////Define Function
    function bar128($text) { // Part 1, make list of widths

                
        global $char128asc,$char128charWidth;

        $char128asc=' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~'; 

        $char128wid = array(
        '212222','222122','222221','121223','121322','131222','122213','122312','132212','221213', // 0-9 
        '221312','231212','112232','122132','122231','113222','123122','123221','223211','221132', // 10-19 
        '221231','213212','223112','312131','311222','321122','321221','312212','322112','322211', // 20-29 
        '212123','212321','232121','111323','131123','131321','112313','132113','132311','211313', // 30-39 
        '231113','231311','112133','112331','132131','113123','113321','133121','313121','211331', // 40-49 
        '231131','213113','213311','213131','311123','311321','331121','312113','312311','332111', // 50-59 
        '314111','221411','431111','111224','111422','121124','121421','141122','141221','112214', // 60-69 
        '112412','122114','122411','142112','142211','241211','221114','413111','241112','134111', // 70-79 
        '111242','121142','121241','114212','124112','124211','411212','421112','421211','212141', // 80-89 
        '214121','412121','111143','111341','131141','114113','114311','411113','411311','113141', // 90-99
        '114131','311141','411131','211412','211214','211232','23311120' ); // 100-106


        // global $char128asc,$char128wid; 

        $w = $char128wid[$sum = 104]; // START symbol

        $onChar=1;

        for($x=0;$x<strlen($text);$x++) // GO THRU TEXT GET LETTERS
        {
            if (!( ($pos = strpos($char128asc,$text[$x])) === false )){ // SKIP NOT FOUND CHARS
                
                $w.= $char128wid[$pos];
                $sum += $onChar++ * $pos;
            } 
        }

        $w.= $char128wid[ $sum % 103 ].$char128wid[106]; //Check Code, then END

        //Part 2, Write rows
        $html="<table cellpadding=0 cellspacing=0><tr>"; 

        for( $x=0; $x<strlen($w) ;$x+=2) // code 128 widths: black border, then white space
        {
            $html .= "<td><div class=\"b128\" style=\"border-left-width:{$w[$x]};width:{$w[$x+1]}\"></div></td>"; 
        }

        return "$html<tr><td colspan=".strlen($w)." align=left><font family=arial size=2>$text</td></tr></table>"; 

    }


    private function _barcode()

    {

        list($dia, $mes, $anio) = explode("/", $this->factura_vto_cae);

        $string = $this->ingresos_brutos;

        $string .= str_pad($this->factura_codigo, 3, "0", 0);

        $string .= $this->punto_venta;

        $string .= $this->factura_cae;

        $string .= $anio . $mes . $dia;

        #--

        $arr_string = str_split($string);

        $par = $impar = 0;

        foreach ($arr_string as $i => $v)

        {

            if ( !(($i + 1) % 2) )

            {

                $par += $v;

            }

            else

            {

                $impar += $v;

            }

        }

        $sum = $par + ($impar * 3);

        for ($i = 0; $i < 10; $i++)

        {

            if ( !is_float(($sum + $i) / 10) )

            {

                $digito = $i;

                break;

            }

        }



        return Barcode::getBarcode(($string . $digito), 11);

    }


    public static function numberFormat($value)

    {

        return number_format($value, 2, ",", ".");

    }

}