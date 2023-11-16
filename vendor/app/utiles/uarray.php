<?php

class UArray
{
    public static function jsonResponse($key, $value=null, $fin=true)
    {
        $data = array();
        if(is_array($key))
        {
            foreach ((array) $key as $k => $v)
            {
                $data[$k] = $v;
            }
        }
        else
        {
            $data[$key] = $value;
        }

        if($fin)
        {
            echo json_encode($data);
            die;
        }
    }

    public static function getFilesDir($directory)
    {
        return array_diff(scandir($directory, 1), array('.','..'));
    }

    public static function jsonError($message)
    {
        static::jsonResponse('error', "<span class='text-danger'>{$message}</span>");
    }

    public static function jsonUrl($url=null)
    {
        if(!$url)
        {
            $url = "./";
        }
        static::jsonResponse('url', $url);
    }

    public static function jsonSuccess()
    {
        static::jsonResponse('success', true);
    }

    public static function arraySortByColumn(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row)
        {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

}