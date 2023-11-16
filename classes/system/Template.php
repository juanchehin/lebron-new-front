<?php

# Session lifetime of 1 day
// ini_set('session.gc_maxlifetime', 1);

// # Enable session garbage collection with a 1% chance of
// # running on each session_start()
// ini_set('session.gc_probability', 1);
// ini_set('session.gc_divisor', 100);

session_start();




class Template extends Maker

{

    protected $page_title = 'My Page';

    protected $favicon;

    protected $meta_attr;

    protected $fb_property;

    protected $styles;

    protected $scripts;

    protected $version;

    protected $firma;

    protected $body = null;



    #--

    public function __construct()

    {

        #--

        define('CURRENT_CLASS', get_called_class());

    }



    protected function addStyle($value)

    {

        $this->styles[] = $value;



        return $this;

    }



    protected function addScript($value, $foot = false)

    {

        if ( $foot )

        {

            $this->scripts['foot'][] = $value;

        }

        else

        {

            $this->scripts[] = $value;

        }



        return $this;

    }



    protected function addMetaAttr($key, $value = null)

    {

        if ( is_array($key) )

        {

            foreach ((array)$key as $k => $v)

            {

                $this->meta_attr[$k] = $v;

            }

        }

        else

        {

            $this->meta_attr[$key] = $value;

        }



        return $this;

    }



    protected function addFbProperty($key, $value = null)

    {

        if ( is_array($key) )

        {

            foreach ((array)$key as $k => $v)

            {

                $this->fb_property[$k] = $v;

            }

        }

        else

        {

            $this->fb_property[$key] = $value;

        }



        return $this;

    }



    protected function setFavicon($value)

    {

        $this->favicon = $value;



        return $this;

    }



    protected function setPageTitle($value)

    {

        $this->page_title = $value;



        return $this;

    }



    protected function getPageTitle()

    {

        return $this->page_title;

    }



    protected function setBody($view)

    {

        if ( $view )

        {

            $this->body = $this->loadView($view);

        }

        $this->_setTemplate();

    }



    private function _setTemplate()

    {

        $template = "<!DOCTYPE html>\n";

        $template .= "<html lang='es-AR'>\n";

        // $template .= "<html itemscope='' itemtype='http://schema.org/WebPage' lang='es-419'>";

        $template .= $this->firma . "\n";

        $template .= "<head>\n";

        $template .= "<meta charset='UTF-8'>\n";

        $template .= "<base href='" . HTTP_HOST . "/' data-class='" . CURRENT_CLASS . "'>\n";

        $template .= "<meta name='cache-control' content='no-cache'>\n";

        $template .= "<meta name='expires' content='0'>\n";

        $template .= "<meta name='pragma' content='no-cache'>\n";

        //$template .= "<meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0'>\n";

        $template .= "<meta name='viewport' content='width=device-width, shrink-to-fit=no, user-scalable=no'>\n";

        foreach ($this->meta_attr as $key => $value)

        {

            $template .= "<meta name='{$key}' content='{$value}'>\n";

        }



        foreach ($this->fb_property as $key => $value)

        {

            $template .= "<meta property='{$key}' content='{$value}'>\n";

        }

        if ( $this->favicon )

        {

            $template .= "<link rel='shortcut icon' href='{$this->favicon}' type='image/x-icon' />\n";

            //$template .= "<link rel='icon' href='{$this->favicon}' type='image/x-icon'>\n";

        }

        $template .= "<title>{$this->page_title}</title>\n";

        foreach ($this->styles as $css)

        {

            $template .= "<link type='text/css' href='{$css}' rel='stylesheet' />\n";

        }

        #--

        foreach ($this->scripts as $js)

        {

            if ( !is_array($js) )

            {

                $template .= "<script src='{$js}' type='text/javascript'></script>\n";

            }

        }

        $template .= "</head>\n";

        $template .= "<body>\n";

        if ( $this->body )

        {

            $template .= $this->body;

        }

        else

        {

            $template .= "<h2>Contenido no disponible.<br />Content unavaible.</h2>";

        }

        $template .= "\n";

        foreach ($this->scripts['foot'] as $js)

        {

            $template .= "<script src='{$js}' type='text/javascript'></script>\n";

        }

        $template .= "</body>\n";

        $template .= "</html>";

        echo $template;

        die;

    }

}