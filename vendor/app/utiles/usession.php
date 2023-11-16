<?php

class USession
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

    public static function removerSession($key)
    {
        unset($_SESSION[$key]);
        return;
    }
}