<?php

class HFunctions
{
	public static $_arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
            'allow_self_signed' => true
        ),
    );
	
    public static function formatPrice($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public static function setLog($value, $file_path = "/log.txt")
    {
        file_put_contents($file_path, $value . "\n", FILE_APPEND);
    }
	
	public static function filePutContent($file, $data, $flags = 0)
    {
        return file_put_contents($file, $data, $flags, stream_context_create(static::$_arrContextOptions));
    }

    public static function fileGetContent($url)
    {
        return file_get_contents($url, false, stream_context_create(static::$_arrContextOptions));
    }

    public static function luhn($number)
    {
        $odd = true;
        $sum = 0;

        foreach (array_reverse(str_split($number)) as $num)
        {
            $sum += array_sum(str_split(($odd = !$odd) ? $num * 2 : $num));
        }

        return (($sum % 10 == 0) && ($sum != 0));
    }

    public static function validarCuit($cuit)
    {
        $cuit = preg_replace("#[^\d]#", '', (string)$cuit);
        if ( strlen($cuit) != 11 )
        {
            return false;
        }

        $acumulado = 0;
        $digitos = str_split($cuit);
        $digito = array_pop($digitos);

        for ($i = 0; $i < count($digitos); $i++)
        {
            $acumulado += $digitos[9 - $i] * (2 + ($i % 6));
        }
        $verif = 11 - ($acumulado % 11);
        $verif = $verif == 11 ? 0 : $verif;

        return ($digito == $verif) ? $cuit : false;
    }

    public static function getIp()
    {
        //Just get the headers if we can or else use the SERVER global
        if ( function_exists('apache_request_headers') )
        {
            $headers = apache_request_headers();
        }
        else
        {
            $headers = $_SERVER;
        }
        //Get the forwarded IP if it exists
        if ( array_key_exists('X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) )
        {
            $the_ip = $headers['X-Forwarded-For'];
        }
        elseif ( array_key_exists('HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) )
        {
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $the_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }

        return $the_ip;
    }

    public static function validPassword($password, $min_length = 6, $text = null)
    {
        $error = null;

        if ( !preg_match("#^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{" . $min_length . ",}$#", $password) )
        {
            $error = "Debe tener al menos <b>{$min_length}</b> caracteres, al menos una may&uacute;scula y al menos un n&uacute;mero. ";
        }

        if ( $text && preg_match("#{$text}#i", $password) )
        {
            $error .= "No debe coincidir con el nombre de usuario.";
        }

        return $error;
    }

    public static function encrypt($value)
    {
        return sha1($value);
    }

    public static function calcularDigitoVerificador($string)
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
}