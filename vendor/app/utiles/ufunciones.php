<?php
use Carbon\Carbon;

class UFunciones
{
    const FORMATO_DMY = 'd/m/Y';
    const FORMATO_YMD = 'Y-m-d';

    public static function hasherPassword($value)
    {
        $hasher = new PasswordHash(8, FALSE);

        return $hasher->HashPassword($value);
    }

    public static function checkHasher($input, $stored)
    {
        $hasher = new PasswordHash(8, false);

        return $hasher->CheckPassword($input, $stored);
    }

    public static function randomPassword($long = 6)
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789!%&@=#";
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $long; $i++)
        {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }

    public static function formatoFecha($fecha, $formato = self::FORMATO_DMY)
    {
        $_fecha = null;
        if ($fecha && $fecha != '0000-00-00')
        {
            $_fecha = Carbon::parse($fecha)->format($formato);
        }

        return $_fecha;
    }

    public static function modificarFecha($fecha, $dias, $formato = self::FORMATO_DMY)
    {
        if (!preg_match('#[\-,\+]#', $dias))
        {
            $dias = '+' . $dias;
        }
        $_fecha = new DateTime(static::fechaSql($fecha));
        return $_fecha->modify($dias . ' day')->format($formato);
    }

    public static function calcularAnio($fecha)
    {
        $anio = date("Y");                 //Obtenemos la fecha actual del sistema
        $mes = date("m");                 //
        $dia = date("d");             //
        if (!$fecha)
        {
            return 0;
        }
        $ingreso = explode("/", $fecha);  //Divide la fecha en dia, mes y a&ntilde;o, en el vector $ingreso
        $d = $ingreso[0];
        $m = $ingreso[1];
        $a = $ingreso[2];
        $ant_anio = $anio - $a;
        if ($mes == $m)
        {
            if ($dia < $d)
            {
                $ant_anio = $ant_anio - 1;
            }
        }
        else
        {
            if ($mes < $m)
            {
                $ant_anio = $ant_anio - 1;
            }
        }
        return $ant_anio;
    }

    public static function fechaSql($fecha, $hora = true)
    {
        if ($fecha && $fecha != 'No definido.')
        {
            $fecha_sql = Carbon::createFromFormat(self::FORMATO_DMY, $fecha);
            if (!$hora)
            {
                $fecha_sql = substr($fecha_sql, 0, 10);
            }
            return $fecha_sql;
        }
        return null;
    }

    public static function dateDiff($start, $end=null)
    {
        if( !$end )
        {
            $end = date('Y-m-d H:i:s');
        }
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;

        return round($diff / 86400);
    }

    public static function fullDate()
    {
        $mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $dia = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');

        return $dia[date('w')] . " " . date('d') . " de " . $mes[date('m') - 1] . " de " . date('Y');
    }

    public static function varDump($data, $stop_run = true)
    {
        echo "<pre style='background:#d1edff;font-family: Monospace,monospace;padding:20px'>";
        var_export($data);
        echo "</pre>";
        if ($stop_run)
        {
            die;
        }
    }

    public static function cadenaGuion($cadena)
    {
        $url = strtolower($cadena);
        $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
        $repl = array('a', 'e', 'i', 'o', 'u', 'n');
        $url = str_replace ($find, $repl, $url);
        $find = array(' ', '&', '\r\n', '\n', '+');
        $url = str_replace ($find, '-', $url);
        $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
        $repl = array('', '-', '');
        $url = preg_replace ($find, $repl, $url);

        return $url;
    }

    public static function htmlIsEmpty($html)
    {
        $text = preg_replace("#\&\w+\;#",'',$html);
        $text = strip_tags($text);
        return trim($text);
    }

    public static function backupDatabase($tablesNo = null)
    {
        //$link = mysql_connect($host, $user, $pass);
        //mysql_select_db($name, $link);
        $cms_db_name = DB_NAME;
//get all of the tables
        $tablesNoBackup = explode(',', $tablesNo);
        $tables = array_column(query_array('SHOW TABLES'), "Tables_in_{$cms_db_name}");
        $return = null;
        foreach ($tables as $table)
        {
            if (!in_array($table, $tablesNoBackup))
            {
                $result = run_query("SELECT * FROM `{$table}`");

                $num_fields = mysql_num_fields($result);

                $return .= "DROP TABLE IF EXISTS `{$table}`;";
                $row2 = query_row("SHOW CREATE TABLE `{$table}`")['Create Table'];
                $return .= "\n\n" . $row2 . ";\n\n";

                for ($i = 0; $i < $num_fields; $i++)
                {
                    while ($row = mysql_fetch_row($result))
                    {
                        $return .= "INSERT INTO `{$table}` VALUES(";
                        for ($j = 0; $j < $num_fields; $j++)
                        {
                            $row[$j] = addslashes($row[$j]);
                            if (isset($row[$j]))
                            {
                                $return .= '"' . $row[$j] . '"';
                            }
                            else
                            {
                                $return .= '""';
                            }
                            if ($j < ($num_fields - 1))
                            {
                                $return .= ',';
                            }
                        }
                        $return .= ");\n";
                    }
                }
                $return .= "\n\n\n";
            }
        }

        //guardar archivo
        $sql_file = DOCS_DIR . 'db-backup-' . date("Y-m-d") . '.sql';
        $handle = fopen($sql_file, 'w+');
        fwrite($handle, $return);
        fclose($handle);
        $zip = new ZipArchive();
        $zip_file = "{$sql_file}.zip";
        if ($zip->open($zip_file, ZipArchive::CREATE) === true)
        {
            $zip->addFile($sql_file, str_ireplace(DOCS_DIR, "", $sql_file));
            $zip->close();
            unlink($sql_file);
        }
        return $zip_file;
    }
}