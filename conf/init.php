<?php
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
ini_set('display_errors', 'On');
date_default_timezone_set("America/Argentina/Tucuman");
define('DEVELOPMENT', in_array($_SERVER['SERVER_ADDR'], array('127.0.0.1', '::1')));
if ( DEVELOPMENT )
{
    ini_set('max_execution_time', 300);
}
#--
$protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
//define('SITE_NAME', 'LeBron Suplementos');
#-- SMTP config
define('SMTP_SECURE', "");
define('SMTP_HOST', DEVELOPMENT ? "173.237.189.61" : "mail.lebron-suplementos.com");
define('SMTP_PORT', 465);
define('SMTP_AUTH_USER', 'info@lebron-suplementos.com');
define('SMTP_AUTH_PASS', 'mKi-uCb4UWds');
#-- Recaptcha Google
define('RECAPTCHA_SCRIPT', "https://www.google.com/recaptcha/api.js?hl=es-419");
//define('RECAPTCHA_SCRIPT', "https://www.google.com/recaptcha/api.js");
define('RECAPTCHA_SITE_KEY', '6LcQ6ikUAAAAAOuaO96xsT46XcZkUIm3K9qFB1dO');
define('RECAPTCHA_SECRET', '6LcQ6ikUAAAAAAYakAXh8wbvAC6A03MxOH4BH_a3');
#--
define('FACEBOOK_APP_ID', DEVELOPMENT ? "1636312056640430" : "198666947347706");
define('GOOGLE_API_KEY', "AIzaSyAh1lu_W5H5RecaY8YRtqgNr6SjnpYep44");
define('GOOGLE_CLIENT_ID', "135493231088-h39qni7lcjom5ch3t8u2clgpd5g2g285.apps.googleusercontent.com");
//define('TELEGRAM_TOKEN',"371801139:AAH2uRA0GHAyK4GgY_bWmfRNaxyC3gS-_Zw");
$db_host = 'localhost';
$db_user = 'root';
$db_pass = "";
$db_name = 'lebronsu_admin';
$db_port = 3306;
if ( !DEVELOPMENT )
{
    ini_set('display_errors', 'Off');
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "lebronsu_admin";
    $db_port = 3306;
}
#-- Si el sitio estará en un subdirectorio
$script_filename = preg_replace("#((\w\:)?\/\w+\.\w+)$#", null, $_SERVER['SCRIPT_FILENAME']);
$script_filename = explode('/', $script_filename);
$use_tmp = (end(explode('/', $_SERVER['DOCUMENT_ROOT'])) != end($script_filename));
define('ROOT_SITE', $use_tmp ? "/" . end($script_filename) : null);
define('HTTP_HOST', "{$protocol}://{$_SERVER['HTTP_HOST']}" . ROOT_SITE);
//define('HTTP_ORIGEN',"//192.168.10.31/origen");
define('MEDIA_DIR', "media");
define('IMAGE_DIR', MEDIA_DIR . "/image");
define('IMAGE_HTTP', HTTP_HOST . "/" . IMAGE_DIR);
define('BARCODE_HTTP', HTTP_HOST . "/" . MEDIA_DIR . "/barcode");
#--
function _autoload($class)
{
    $classes = "./classes";
    $class_dir = array_diff(scandir($classes, SORT_DESC), array('.', '..'));
    foreach ($class_dir as $dir)
    {
        $info = pathinfo($dir);
        $file = $class . ".php";
        if ( !$info['extension'] )
        {
            $file = $dir . "/{$file}";
        }
        include_once $classes . "/{$file}";
    }
}

function _new_autoload($clase)
{
    $path = str_ireplace("\\", "/", $clase);
    include_once $path;
}

spl_autoload_register('_autoload');
#--
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ( !$mysqli->ping() && false )
{
    $error = "<h3>Ha ocurrido un error...</h3>";
    if ( DEVELOPMENT )
    {
        $error .= "<h4>Problemas de conexion a MySQL: {$mysqli->connect_errno} - {$mysqli->connect_error}</h4>";
    }
    die($error);
}

include_once "./vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$connection = array(
    'driver' => 'mysql',
    'host' => $db_host,
    'database' => $db_name,
    'username' => $db_user,
    'password' => $db_pass,
    'port' => $db_port,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
);

$capsule->addConnection($connection);
$capsule->setAsGlobal();
$capsule->bootEloquent();
#--