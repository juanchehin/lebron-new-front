<?php

class Polling
{
    const filePolling = "media/control.txt";
    const accion = "";

    public static function getFile()
    {
        return self::filePolling;
    }

    public static function getData()
    {
        $content = preg_replace("#,\n?$#", "", file_get_contents(self::filePolling));
        $data_row = explode(",", $content);
        //$data = json_decode("[{$content}]", true);
        $last_row = trim(array_pop($data_row));
        $items = explode("|", $last_row);
        $data = array();
        foreach ($items as $item)
        {
            list($key, $value) = explode("&", $item);
            $data[$key] = $value;
        }
        #--
        return $data;
    }

    public static function set($valor, $accion, $mesa = 1, $origen = "cliente")
    {
        $string = "origen&{$origen}|mesa&{$mesa}|valor&{$valor}|accion&{$accion},\n";
        file_put_contents(self::filePolling, $string, FILE_APPEND);
    }

    public static function setData($key, $value)
    {
        $string[$key] = $value;
        $string = "{$key}&{$value}";
        HFunctions::setLog("{$string},", self::filePolling);
    }

    public static function deleteFile()
    {
        unlink(self::getFile());
    }

    public function control()
    {
        session_write_close();
        ignore_user_abort(false);
        set_time_limit(40);
        try
        {
            if ( !isset($_COOKIE['lastUpdate']) )
            {
                setcookie('lastUpdate', 0);
                $_COOKIE['lastUpdate'] = 0;
            }
            $lastUpdate = $_COOKIE['lastUpdate'];
            if ( !file_exists($file = self::filePolling) )
            {
                //HArray::jsonResponse('status', false);
                file_put_contents($file, "");
            }
            while (true)
            {
                $fileModifyTime = filemtime($file);
                if ( $fileModifyTime === false )
                {
                    throw new Exception('Could not read last modification time');
                    //return;
                }

                if ( $fileModifyTime > $lastUpdate )
                {
                    setcookie('lastUpdate', $fileModifyTime);
                    $fileRead = self::getData();
                    $response = array('status' => true, 'time' => $fileModifyTime, 'content' => $fileRead);
                    HArray::jsonResponse($response);
                }
                clearstatcache();
                sleep(2);
            }
        } catch (Exception $e)
        {
            HArray::jsonResponse(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}