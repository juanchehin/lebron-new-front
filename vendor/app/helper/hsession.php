<?php

class HSession
{
    public static function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
        return;
    }

    public static function getSession($key)
    {
        return $_SESSION[$key];
    }

    public static function removeSession($key)
    {
        unset($_SESSION[$key]);
        return;
    }

    public static function sessionCount()
    {
        $data = array();
        $dir = session_save_path();
        if ( preg_match("#win#i", $_SERVER['HTTP_USER_AGENT']) )
        {
            $cmd = shell_exec("cd /d {$dir} && dir");
            $output = explode("\n", $cmd);
            foreach ($output as $r)
            {
                if ( preg_match("#sess\_#", $r) )
                {
                    $sess_fecha = substr($r, 0, 10);
                    //HArray::varDump($sess_fecha);
                    $sess_id = substr($r, 41, 26);
                    $data[$sess_id] = HDate::sqlDate($sess_fecha) . substr($r, 11, 6) . ":00";
                }
            }
        }
        else
        {
            $sessions = shell_exec("ls -t --full-time {$dir} && date");
            //preg_match_all("#\d+\-\d+\-\d+\s+\d+\:\d+\:\d+#",$sessions, $matches);
            $sessions = explode("\n",$sessions);
            //UFunciones::varDump($sessions);
            foreach ($sessions as $ses )
            {
                if (preg_match("#sess\_#", $ses))
                {
                    $ses_fecha = substr($ses,33,19);
                    $ses_id = substr($ses,74,26);
                    $data[$ses_id] =  $ses_fecha;
                }
            }
        }
        return $data;
    }
}