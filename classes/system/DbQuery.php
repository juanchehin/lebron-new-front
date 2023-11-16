<?php

class DbQuery
{
    protected $connection;

    public static function runQuery($query)
    {
        global $mysqli;
        $execute = $mysqli->query($query);
        if ( !$execute )
        {
            $error = $query;
            $error .= "<br />{$mysqli->errno} - {$mysqli->error}";
            if ( DEVELOPMENT )
            {
                die($error);
            }
            exit;
        }

        return $execute;
    }

    public static function arrayQuery($query)
    {
        $rows = array();
        $_query = static::runQuery($query);
        while ($result = $_query->fetch_object())
        {
            $rows[] = $result;
        }

        return $rows;
    }

    public static function rowQuery($query)
    {
        $row = array_shift(static::arrayQuery($query));

        return $row;
    }
}