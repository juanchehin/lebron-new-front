<?php

class Maker
{
    const VIEWS_DIR = 'views/';
    protected $params;
    public static function renderView($view, $params = array())
    {
        ob_start();
        extract($params);
        if ( !preg_match("#\.\w+$#", $view) )
        {
            $view .= ".php";
        }
        include self::VIEWS_DIR . $view;
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    protected function setParams($key, $value = null)
    {
        if ( is_array($key) )
        {
            foreach ($key as $k => $v)
            {
                $this->params[$k] = $v;
            }
        }
        else
        {
            $this->params[$key] = $value;
        }

        return $this;
    }

    protected function loadView($view)
    {
        return static::renderView($view, $this->params);
    }
}