<?php

class Accesos
{
    const DEFAULT_METHOD = 'index';
    const DEFAULT_ROUTE = 'root';
    const ERROR_404 = 404;
    public static $ACCESOS;
    public $continuar = false;

    private static function _setClass($route, $value = null)
    {
        $split = array_values(array_filter(explode('/', $route)));
        $classname = $split[0];
        $method = $split[1];

        if (class_exists($classname))
        {
            $new_class = new $classname();

            if (!$method)
            {
                $method = self::DEFAULT_METHOD;
            }

            if (method_exists($classname, $method))
            {
                define('CURRENT_METHOD', $method);
                $new_class->$method($value);
                exit;
            }
        }
        static::paginaNoEncontrada();
    }

    public static function isXhrRequest()
    {
        $http_requested = $_SERVER['HTTP_X_REQUESTED_WITH'];

        return (!empty($http_requested) && strtolower($http_requested) == 'xmlhttprequest');
    }

    private static function paginaNoEncontrada()
    {
        http_response_code(self::ERROR_404);
        $not_found  = "<div style='background:#ff9999;padding: 10px 4px;color:#FFF;font-size:1.6em'>\n";
        $not_found .= "P&aacute;gina no encontrada!\n";
        $not_found .= "</div>\n";
        echo $not_found;
        die;
    }


    public static function getAcceso($acceso)
    {
        global $rutas;

        return array_search($acceso, $rutas);
    }

    public static function redirigir($location=null)
    {
        global $tmpFolder;
        $location = preg_replace("#^\/#",null,$location);
        $url = preg_match("#(http|https)#", $location) ? $location : "/{$tmpFolder}{$location}";
        header("location: {$url}");
        die;
    }

    public function setContinuar($value)
    {
        $this->continuar = $value;

        return $this;
    }

    public function getContinuar()
    {
        return $this->continuar;
    }

    public static function procesarAcceso()
    {
        global $rutas, $tmpFolder;

        $current_uri = parse_url($_SERVER['REQUEST_URI'])['path'];
        #-- Es una petición ajax
        if (stripos($current_uri, '!') !== false)
        {
            self::_setClass(preg_replace("#.*\!#", '/', $current_uri));
        }

        if ($rutas)
        {
            foreach ($rutas as $route => $clase)
            {
                //if (preg_match("#^\/{$route}\/?$#", $current_uri, $matches))
                $route = $tmpFolder . $route;
                if (preg_match("#^\/{$route}(\/)?$#", $current_uri, $matches))
                {
                    $param = null;
                    $needle = array('d+','w+');
                    #
                    $split = explode('/', preg_replace("#[^\/,\w+]#",null,$route));
                    $segment_uri = explode('/',preg_replace("#^(\/)?#",null,$current_uri));

                    foreach ($needle as $v)
                    {
                        $key = array_search($v, $split);
                        if ($key)
                        {
                            $param = $segment_uri[$key];
                            //$_REQUEST['id'] = $param;
                            break;
                        }
                    }

                    $_GET['seccion'] = $split[0];
                    $_GET['subseccion'] = $split[1];
                    if ($split[2])
                    {
                        $_GET['accion'] = preg_replace('#[^\w+]#', '', $split[2]);
                    }

                    self::_setClass($clase, $param);
                }
            }
        }
        //die('nada para mostrar');
    }
}