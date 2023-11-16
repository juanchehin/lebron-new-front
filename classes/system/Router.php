<?php

class Router
{
    const ERROR_404 = 404;
    const DEFAULT_METHOD = "index";

    #--
    public static function redirect($location = null)
    {
        if ( !$location || !static::urlIsExternal($location) )
        {
            $location = HTTP_HOST . "/{$location}";
        }
        header("location: {$location}");
        exit;
    }

    public static function urlIsExternal($url)
    {
        return preg_match("#(^http(s)?|^\/\/)#", $url);
    }

    public static function isAjaxRequest()
    {
        $http_requested = $_SERVER['HTTP_X_REQUESTED_WITH'];

        return (!empty($http_requested) && strtolower($http_requested) == 'xmlhttprequest');
    }

    public static function processRoutes($routes = null)
    {
        //global $routes;
        //$routes['not-found'] = 'Router/notFound';
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_ireplace(ROOT_SITE, null, $path);

        if ( stripos($path, '!') !== false )
        {
            /*if ( !static::isAjaxRequest() )
            {
                static::notFound();
            }*/
            static::_setClass(preg_replace("#.+\!#", null, $path));
            exit;
        }
        #--
        if ( $routes )
        {
            $params = array('\\d\+', '\\w\+');
            foreach ($routes as $route => $class)
            {
                //if(preg_match("#^\/?{$route}$#", $path))
                $match = preg_match("#^(\/)?{$route}\/?$#i", $path);
                //echo $match . " | {$path}<br/>";
                if ( $match )
                {
                    $value = null;
                    $split = explode("/", $route);
                    $key = null;
                    foreach ($params as $k => $v)
                    {
                        $key = preg_grep("#{$v}#", $split);
                        if ( $key )
                        {
                            $value = explode('/', $path)[array_keys($key)[0]];
                            break;
                        }
                    }
                    static::_setClass($class, $value);
                }
            }

            return static::notFound(DEVELOPMENT);
        }
    }

    private static function _setClass($route, $value = null)
    {
        list($class, $method) = explode('/', $route);
        if ( $class && class_exists($class) )
        {
            $page = new $class();
            if ( !$method )
            {
                $method = self::DEFAULT_METHOD;
            }

            if ( method_exists($class, $method) )
            {
                define('CURRENT_METHOD', $method);
                $page->$method($value);
                exit;
            }
        }

        return static::notFound(DEVELOPMENT);
    }

    public static function notFound($end = true)
    {
        http_response_code(self::ERROR_404);
        if ( $end )
        {
            $not_found = "<!DOCTYPE html>\n<html>\n";
            $not_found .= "<head>\n";
            $not_found .= "<title>- 404 -</title>\n";
            $not_found .= "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
            $not_found .= "</head>\n";
            $not_found .= "<body style='text-align: center;font-family: Helvetica, Arial, sans-serif'>\n";
            $not_found .= "<div style='font-size:18px;padding:35px 0'>\n";
            $not_found .= "<h1 style='color:#FF0000;font-size:40px'>404</h1>\n";
            $not_found .= "<h2>¡P&aacute;gina no encontrada!<br/>";
            $not_found .= "Page not found!</h2>\n";
            $not_found .= "<p>La p&aacute;gina solicitada no existe.</p>\n";
            $not_found .= "<p>The requested page does not exist.</p>\n";
            $not_found .= "</div>\n";
            $not_found .= "</body>\n";
            $not_found .= "</html>";
            die($not_found);
        }
        return http_response_code();
    }
}
