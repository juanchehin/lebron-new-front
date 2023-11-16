<?php

use Carbon\Carbon;

class HDate
{
    const FORMAT_DMY = "d/m/Y";
    const FORMAT_YMD = "Y-m-d";
    const FORMAT_HIS = "H:i:s";
    const FORMAT_DMY_HMS = self::FORMAT_DMY . " " . self::FORMAT_HIS;
    const FORMAT_YMD_HMS = self::FORMAT_YMD . " " . self::FORMAT_HIS;
    const ANIOS = 'y';
    const MESES = 'm';
    const DIAS = 'd';
    const HORAS = 'h';
    const MINUTOS = 'i';
    const SEGUNDOS = 's';
    public static $_MESES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    public static $_DIAS = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");

    #--
    public static function sqlDate($value)
    {
        $sql_date = null;
        if ( $value )
        {
            list($day, $month, $year) = preg_split("#(\/|\-)#", $value);
            $sql_date = "{$year}-{$month}-{$day}";
        }

        return $sql_date;
    }

    public static function dateFormat($value, $format = self::FORMAT_DMY)
    {
        if ( preg_match("#\/#", $value) )
        {
            return $value;
        }
        return Carbon::parse($value)->format($format);
    }

    public static function today($time = true, $format = self::FORMAT_YMD)
    {
        if ( $time )
        {
            $format .= " H:i:s";
        }

        return date($format);
    }

    public static function dateDiff($begin, $end, $return = self::DIAS)
    {
        switch ( $return )
        {
            case self::MINUTOS :
                $value = 60;
                break;
            case self::SEGUNDOS:
                $value = 1;
                break;
            default :
                $value = 86400; //dias
                break;
        }
        $tiempo = (strtotime($end) - strtotime($begin)) / $value;
        $tiempo = abs($tiempo);
        $tiempo = floor($tiempo);

        return $tiempo;
    }

    public static function inicioFinSemana($_fecha = null, $_days)
    {
        $fecha = $_fecha ?: \HDate::modifyDate(date('Y-m-d'), "-" . date('w') + 1 . " days");
        $days = 7;
        $last_monday = \HDate::modifyDate($fecha, "-{$days} days");
        $next_monday = \HDate::modifyDate($fecha, "{$days} days");
        $w_fin = \HDate::modifyDate($fecha, ($days - ($_days > 7 ? 1 : 2)) . " days");
        $res['ini_week'] = $fecha;
        $res['fin_week'] = $w_fin;
        $res['pasado_lunes'] = $last_monday;
        $res['proximo_lunes'] = $next_monday;
        return $res;
    }

    public static function edad($fecha)
    {
        list($anio, $mes, $dia) = explode("-", $fecha);
        $anio_dif = date("Y") - $anio;
        $mes_dif = date("m") - $mes;
        $dia_dif = date("d") - $dia;
        if ( $dia_dif < 0 || $mes_dif < 0 )
        {
            $anio_dif--;
        }
        return $anio_dif;
    }

    public static function nombreDia($fecha)
    {
        $fechats = strtotime($fecha);
        $dia = (date('w', $fechats) - 1);
        if ( $dia < 0 )
        {
            $dia = 6;
        }
        return self::$_DIAS[$dia];
    }

    public static function modifyDate($date, $value, $format = self::FORMAT_YMD)
    {
        $_date = new DateTime($date);
        if ( !preg_match("#\-#", $value) )
        {
            $value = "+{$value}";
        }
        $_date->modify("{$value}");

        return $_date->format($format);
    }

    public static function isDate($date, $format = self::FORMAT_DMY)
    {
        $d = DateTime::createFromFormat($format, $date);

        return ($d && $d->format($format) == $date);
    }

    /*public static function fullDate($time = true)
    {
        $nro_dia = date('N');
        $dia = date('d');
        $nro_mes = date('m');
        $anio = date('Y');
        if ( $time )
        {
            $fecha .= ", " . date('H:i:s');
        }

        if ( $_date )
        {
            $date = date_create($_date);
            list($nro_dia, $dia, $nro_mes, $anio) = explode("-", date_format($date, "N-d-m-Y"));
        }
        $fecha = self::$_DIAS[$nro_dia - 1] . " {$dia} de " . self::$_MESES[$nro_mes - 1] . " de {$anio}";
        return $fecha;
    }*/

    public static function fullDate($time = true, $_date = null)
    {
        $nro_dia = date('N');
        $dia = date('d');
        $nro_mes = date('m');
        $anio = date('Y');
        if ( $time )
        {
            $fecha .= ", " . date('H:i:s');
        }

        if ( $_date )
        {
            $date = date_create($_date);
            list($nro_dia, $dia, $nro_mes, $anio) = explode("-", date_format($date, "N-d-m-Y"));
        }
        $fecha = self::$_DIAS[$nro_dia - 1] . " {$dia} de " . self::$_MESES[$nro_mes - 1] . " de {$anio}";
        return $fecha;
    }

    public static function daysInMonth($month = null, $year = null)
    {
        if ( !$month )
        {
            $month = date('m');
        }

        if ( !$year )
        {
            $year = date('Y');
        }
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    public static function dayMoment()
    {
        $hora = date("H");
        $mensaje = "Buenas ";
        switch ( true )
        {
            case ($hora >= 6 && $hora < 13) :
                $mensaje = "Buenos d&iacute;as";
                break;
            case ($hora >= 13 && $hora < 20):
                $mensaje .= "tardes";
                break;
            default:
                $mensaje .= "noches";
                break;
        }

        return $mensaje;
    }
}
