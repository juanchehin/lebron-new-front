<?php

class UFormato
{
    public static function formatoPrecio($value)
    {
        return number_format($value,2,',','.');
    }

    public static function urlSemantica($url)
    {
        return preg_replace('#[^\w+,\d+]#','-',strtolower($url));
    }

    public static function cortarTexto($string, $long = NULL)
    {
        //Si no se especifica la longitud por defecto es 50
        if ($long == NULL)
        {
            $long = 50;
        }
        //Primero eliminamos las etiquetas html y luego cortamos el string
        $stringDisplay = substr(strip_tags($string), 0, $long);
        //Si el texto es mayor que la longitud se agrega puntos suspensivos
        if (strlen(strip_tags($string)) > $long)
        {
            $stringDisplay .= ' ...';
        }

        return $stringDisplay;
    }
}