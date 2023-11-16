<?php

use \Windwalker\Renderer\PhpRenderer;

class Loader
{
    #-- en la raiz del sitio
    const VISTAS = APP_DIR . 'vistas';
    protected $styles;
    protected $scripts;
    protected $render;
    protected $content;
    protected $parametros;

    public function __construct()
    {
        $this->render = new PhpRenderer(self::VISTAS);
        if(!defined(PANEL))
        {
            define('PANEL','');
        }
    }

    protected function add_styles($archivo)
    {
        $this->styles[] = $archivo;

        return $this;
    }

    protected function add_scripts($archivo)
    {
        $this->scripts[] = $archivo;

        return $this;
    }

    protected function setValues($parametro, $value=null)
    {
        if(is_array($parametro))
        {
            foreach ((array) $parametro as $k => $v)
            {
                $this->parametros[$k] = $v;
            }
        }
        else
        {
            $this->parametros[$parametro] = $value;
        }

        return $this;
    }

    protected function escape($value)
    {
        return $this->render->escape($value);
    }

    public static function loadBlock($block, $data=array())
    {
        ob_start();
        extract($data);
        include self::VISTAS . "/{$block}.php";
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    protected function loadView($vista)
    {
        $this->content = $this->render->render($vista, $this->parametros);
        return $this;
    }

    public function __toString()
    {
        return $this->content;
    }
}