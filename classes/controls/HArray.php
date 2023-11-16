<?php

class HArray
{
    public static function varDump($data, $end = true)
    {
        echo "<pre style='width:100%;background:#66ccff;padding:15px'>";
        var_export($data);
        echo "</pre>";
        if ( $end )
        {
            die;
        }
    }

    public function listDir($dirname)
    {
        return array_diff(scandir($dirname, SORT_DESC), array('.', '..'));
    }

    public static function jsonResponse($param, $value = null, $end = true)
    {
        $json = array();
        if ( is_array($param) )
        {
            foreach ((array)$param as $k => $v)
            {
                $json[$k] = $v;
            }
        }
        else
        {
            $json[$param] = $value;
        }

        echo json_encode($json);
        if ( $end )
        {
            die;
        }
    }

    public static function jsonSuccess()
    {
        static::jsonResponse('success', true);
    }

    public static function jsonError($message, $field = null)
    {
        $json['error'] = "<span class='text-danger'>{$message}</span>";
        if ( $field )
        {
            $json['field'] = $field;
        }
        static::jsonResponse($json);
    }

    public static function jsonMessage($message)
    {
        $json['notice'] = "<span class='text-info'>{$message}</span>";
        $json['success'] = true;
        static::jsonResponse($json);
    }

    public static function jsonLocation($location = null)
    {
        if ( !$location )
        {
            $location = "./";
        }
        static::jsonResponse('location', $location);
    }

	public static function arraySortByColumn(&$arr, $col, $dir = SORT_ASC) 
	{
        $sort_col = array();
        foreach ($arr as $key=> $row)
        {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }
}
