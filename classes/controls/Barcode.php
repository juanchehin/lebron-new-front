<?php

class Barcode
{
    protected $primer_monto;
    protected $primer_vto;
    protected $segundo_monto;
    protected $segundo_vto;

    private static function formatoCeros($value, $length = 6, $split = true)
    {
        $value = preg_split("#(\.|\,)#", $value);
        $formato = str_pad($value[0], $length, 0, STR_PAD_LEFT);
        if ( $split )
        {
            $formato .= str_pad($value[1], 2, 0, STR_PAD_RIGHT);
        }

        return $formato;
    }

    #fecha formato ddmmaa
    private static function formatoFecha($fecha)
    {
        $explode = explode('/', $fecha);
        $format = null;
        if ( !empty($explode) )
        {
            $format = $explode[0] . "{$explode[1]}" . substr($explode[2], 2, 2);
        }

        return $format;
    }

    /*
     * @param fecha d/m/Y
     *
     */
    public function setPrimerVto($value)
    {
        $this->primer_vto = static::formatoFecha($value);

        return;
    }

    /*
     * @param fecha d/m/Y
     *
     */
    public function setSegundoVto($value)
    {
        $this->segundo_vto = static::formatoFecha($value);

        return;
    }

    public function setSegundoMonto($value)
    {
        $this->segundo_monto = $value;

        return;
    }

    public function setPrimerMonto($value)
    {
        $this->primer_monto = $value;

        return;
    }

    private static function calcularDigitoVerificador($string)
    {
        $_string = str_split($string);
        $suma = array_shift($_string);
        $secuencia = array(3, 5, 7, 9);
        $i = 0;
        foreach ($_string as $value)
        {
            $suma += $value * $secuencia[$i];
            $i++;
            if ( $i == count($secuencia) )
            {
                $i = 0;
            }
        }
        $digito = intval($suma / 2) % 10;

        return $digito;
    }

    /*
     *
     * @return barcode image
     *
     */
    public static function getBarcode($code, $image = false)
    {
        $barcode = new Code128();
        $barcode->setData($code);
        //$barcode->setDimensions(290, 50);
        $barcode->setSubType(Code128::TYPE_C);
        #--
        $bcode = "<img src='{$barcode->draw()}' style='width:100%;' alt='{$code}'/>";
        $bcode .= "<p style='margin:0;font-size:10px;font-weight:500'>{$code}</p>";
        if ( $image )
        {
            $bcode = $barcode->draw();
        }
        return $bcode;
    }

    public function codigoBarras($id_operacion)
    {
        #-- Código Fijo Rapipago (3)
        $code = "175";
        #-- ID cliente (6)
        $code = static::formatoCeros($id_operacion, 10, false);
        #-- fecha 1er vto ddmmaa (6)
        $code .= $this->primer_vto;
        #-- monto 1er vto (5)
        $code .= static::formatoCeros($this->primer_monto, 5, false);
        #-- fecha 2do vto ddmmaa (6)
        $code .= $this->segundo_vto;
        #-- segundo importe (5)
        $code .= static::formatoCeros($this->segundo_monto, 5, false);
        #-- Dígitos de verificación (1)
        $code .= static::calcularDigitoVerificador($code);
        $code .= HFunctions::calcularDigitoVerificador($code);

        #-- Generar BarCode
        return static::getBarcode($code);
        //$this->setBody();
    }
}