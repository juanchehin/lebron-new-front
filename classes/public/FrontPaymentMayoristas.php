<?php

// ini_set("display_errors", "On");

require 'C:\wamp64\www\lebron\vendor\autoload.php';

require 'C:\wamp64\www\lebron\vendor\mercadopago\sdk\lib\mercadopago.php';


class FrontPaymentMayoristas extends FrontCartMayoristas

{

    const resSuccess = "success";

    const resFailure = "failure";

    const resPending = "pending";

    const resApproved = "approved";

    protected $mp;



    public function __construct()

    {

        parent::__construct();

        // HArray::varDump($this->config['mp']);

        $this->mp = new \MP($this->config['mp']['access_token']);//del usuario dueño de la aplicacion

        //$this->renovarToken();

    }



    public function credenciales($refresh = false)
    {        


        $post = array(

            'client_id' => $this->config['mp']['app_id'],

            'client_secret' => $this->config['mp']['client_secret']

        );

        if ( $refresh )

        {

            $type = "refresh_token";

            $post['refresh_token'] = $this->config['mp']['refresh_token'];

        }

        else

        {

            $type = "authorization_code";

            $post['code'] = trim($_GET['code']);

            $post['redirect_uri'] = self::appUrl;

        }



        #--

        $post["grant_type"] = $type;

        $request = array(

            "uri" => "/oauth/token",

            "data" => $post,

            "headers" => array(

                "content-type" => "application/x-www-form-urlencoded"

            ),

            "authenticate" => false

        );

        //HArray::varDump($request, false);

        $result = $this->mp->post($request)['response'];



        if ( !$result['access_token'] )

        {

            file_put_contents("media/json-mp.log", '{"fecha":"' . date('Y-m-d H:i:s') . '","error":"No se renovó token"}', FILE_APPEND);

            return;

        }

        $result['control_expire'] = date('Y-m-d', strtotime($this->getHoy(false)) + ($result['expires_in'] - (86400 * 30)));

        $data = array_merge($this->config['mp'], $result);

        file_put_contents(self::$_ecommerce_file, json_encode($data));

        if ( $post['code'] )

        {

            HArray::varDump($data);

        }

        return;

    }



    public function renovarToken()
    {

        if ( $this->getHoy(false) >= $this->config['mp']['control_expire'] )

        {

            $this->credenciales(true);

        }

        return;

    }

    // mayoristas
    public function confirmarCheckout()
    {
        $conEnvio = trim($_POST['retiroLocal']);

        if($conEnvio == 'conEnvio')
        {
            $conEnvio = true;
        }else{
            $conEnvio = false;
        }

        $this->authForm($conEnvio);
    }


    public function response($result)
    {

        $status = trim($_GET['collection_status']);

        #-- quitar los parametros de la url de respuesta de mercadopago

        //Router::redirect(self::siteUrl . $redirect);

        #--

        $this->setPageTitle($title = "Resultado de la Operación");

        $borrar_compra = false;

        switch ( $status )

        {

            case self::resSuccess:

            case self::resApproved:

                $alert = "success";

                $response = "La operación se realizó correctamente";

                $borrar_compra = true;

                break;

            case self::resPending :

                $response = "Tu pago está siendo procesado.";

                $alert = "info";

                $borrar_compra = true;

                break;

            case self::resFailure :

                $alert = "danger";

                $response = "La operación no pudo ser realizada";

                break;

            default :

                $alert = "warning";

                $response = "Ha ocurrido un error desconocido.";

        }

        #--

        if ( $borrar_compra )

        {

            $this->setSessionCart(array(), true);

        }

        $data['html_alert'] = "<h3 style='padding:18px 0'>{$response}</h3>";

        $data['response_title'] = $title;

        $data['css_alert'] = $alert;

        $this->setParams($data);

        $this->setBody("front-cart-index");

    }



    public function crearUsuarioTest()
    {

        $body = array(

            "json_data" => array(

                "site_id" => "MLA"

            )

        );



        $result = $this->mp->post('/users/test_user', $body);

        HArray::varDump($result);

    }



    public function calcularEnvio()
    {

        $cp = floatval($_POST['cp'] ?: $_GET['cp']);

        $dimensiones = trim($_POST['dimension'] ?: "20x12x12,800");

        $precio = floatval($_POST['precio'] ?: 100.58);

        #--

        $params = array(

            "dimensions" => $dimensiones, // alto x ancho x largo (centímetros), peso (gramos)

            "zip_code" => $cp,

            "item_price" => $precio,

        );

        $costo = "Info no Disponible";

        if ( $cp > 1810 && $dimensiones && $precio )

        {

            //"https://api.mercadolibre.com/countries/ar/zip_codes"

            $response = $this->mp->get("/shipping_options", $params)['response'];

            $costo = "$ " . number_format($response['options'][0]['cost'], 2);

        }

        //HArray::varDump($response);

        $res['total'] = $costo;

        $res['ok'] = 1;

        #--

        HArray::jsonResponse($res);

    }



    public function mp_ver($id, $topic, $get = false)
    {
        ini_set("display_errors", "On");

        $order_id = $_GET['id'] ?: $id;

        $_topic = trim($_GET['topic'] ?: $topic);

        //HArray::varDump($_GET);

        if ( ($_topic == "payment") )

        {

            $payment_info = $this->mp->get_payment($order_id)['response'];

            $order_id = $payment_info["collection"]['merchant_order_id'];

        }

        $order = $this->mp->get("/merchant_orders/{$order_id}");

        if ( $order['status'] != 200 )

        {

            $order = null;

        }

        #--

        if ( !$get )

        {

            HArray::varDump($order);

        }

        return $order;

    }



    public function notificacion()

    {

        //ini_set("display_errors", "On");

        $from_logs = false;
        $access_token = addslashes(trim($_GET['tkn'])) ?: $this->config['mp']['access_token'];
        //HFunctions::filePutContent("media/mp-log.txt", date('d/m/Y H:i:s') . " => {$access_token}\r\n", FILE_APPEND);
        if ( !$order_id = $_GET['id'] )
        {

            // $log_content = file_get_contents("https://c512.cloud.wiroos.net/wbapp/notify.log");

            // $arr_log = json_decode("[" . preg_replace("#\,$#", "", $log_content) . "]", true);
            // if ( isset($_GET['logs']) )
            // {
            //     HArray::varDump($arr_log);
            // }
            // $_GET = array_pop($arr_log);
            $order_id = $_GET['id'];
            $from_logs = true;
        }

        #--

        $mp = new MP($access_token);

        $es_payment = ($_GET['topic'] == "payment");

        if ( $es_payment || !$from_logs )

        {

            //$payment_info = $mp->get("/v1/payments/{$ipn_id}");

            $payment_info = $mp->get_payment($order_id)['response'];

            $order_id = $payment_info["collection"]['merchant_order_id'];

        }



        $order = $mp->get("/merchant_orders/{$order_id}");

        if ( $order["status"] == 200 )
        {
            $order_data = $order['response'];
            if ( $es_payment )
            {
                file_put_contents("media/json-mp.log", json_encode($order_data) . ",<->\n", FILE_APPEND);
            }
            $es_prueba = preg_match("#\@testuser\.com#", $order_data['collector']['email']);
            if ( $es_prueba )
            {
                //file_put_contents("media/mercado-pago.txt", json_encode($order_data) . ",\n", FILE_APPEND);

                //echo "Fue pruebaaa<br/>";
            }

            #--

            $paid_amount = $total_paid_amount = 0;
            foreach ($order_data["payments"] as $payment)
            {
                if ( $payment['status'] == "approved" )
                {
                    $paid_amount += floatval($payment['transaction_amount']);
                    $total_paid_amount += floatval($payment['total_paid_amount']);
                }
            }

            if ( $paid_amount >= $order_data["total_amount"] )
            {
                $mensaje = "";

                $con_envio = 0;

                if ( count($order_data["shipments"]) > 0 )

                {

                    #-- Esto es por si tiene envio

                    $shipment = $order_data["shipments"][0];

                    $direccion = $shipment['receiver_address'];

                    $con_envio = 1;//($shipment["status"] == "ready_to_ship");

                }

                $items = $order_data["items"];

                list($id_cliente, $addrId) = explode("&", $items[0]['category_id']);

                if ( !($venta = Venta::where('external_id', $order_data['id'])->first()) )

                {

                    //HFunctions::filePutContent("media/mp-set.txt", $order_data['id'] . "\r\n", FILE_APPEND);

                    #--

                    //$venta->hasLineaVenta()->delete();

                    #--

                    $cliente = Cliente::find($id_cliente);

                    if ( !$cliente )

                    {

                        return;

                    }

                    $mensaje .= "<h3><i>{$cliente->nombre_apellido} ({$cliente->dni})</i> ha realizado una compra</h3>";

                    $mensaje .= "Op. Nº: <b>{$order_id}</b>";

                    if( $payment )

                    {

                        $mensaje .= " *";

                    }

                    $mensaje .= "<br/>";

                    #--

                    $arr = array();

                    if ( $direccion = Direccion::find($addrId) )

                    {

                        $addr = $direccion->array_body;

                        $arr[] = mb_strtoupper($addr['calle']) . " " . $addr['numero'];

                        $arr[] = mb_strtoupper($direccion['nombre']) . " (CP {$direccion->valor})";

                        $arr[] = mb_strtoupper($addr['provincia']);

                        $arr[] = "<br/>Referencia: " . (mb_strtoupper($addr['referencia'] ?: "-"));

                        $full_addr = implode(". ", $arr);

                        #--

                        if ( $cliente && false )

                        {

                            $cliente->direccion = $full_addr;

                            $cliente->celular = $direccion['phone'];

                            $cliente->save();

                        }

                        #--

                        $mensaje .= "Direcci&oacute;n de envío:";

                        $mensaje .= "<ul>";

                        $mensaje .= "<li>Dirección: {$full_addr}</li>";

                        //$mensaje .= "<li>Nombre Contacto: {$direccion['contact']}</li>";

                        $mensaje .= "<li>Teléfono: {$addr['telefono']}</li>";

                        $mensaje .= "</ul>";

                        //$envio = $shipment['shipping_option'];

                        //$mensaje .= "<h4>Condiciones de Envío</h4>";

                        //$mensaje .= $envio['name'] . ", $ " . round($envio['cost'], 2);

                        #-- Verificar si el envio es a cargo del vendedor

                        //$pago_envio = ($envio['id'] && !floatval($envio['cost'])) ? floatval($envio['list_cost']) : 0;

                    }

                    #--

                    /* $venta = Venta::where(['estado' => "tmp", 'id_cliente' => $id_cliente])->first() ?: new Venta;

                     $venta->id_usuario = ($con_envio * -1);

                     $venta->id_cliente = $id_cliente;

                     $venta->id_sucursal = Local::mercadoLibre;

                     $venta->tipo = Venta::tpVenta . "_publico";

                     $venta->external_id = $order_data['id'];

                     $venta->estado = Venta::estadoEspera;

                     $venta->visible = 0;

                     $venta->save();*/

                    #--

                    $lista_articulos = "<table width='100%' cellspacing='0' border='1'>";

                    $lista_articulos .= "<thead>";

                    $lista_articulos .= "<tr><th align='center'>#ID</th><th>Artículo</th><th align='center'>Cantidad</th><th>Precio Un</th></tr>";

                    $lista_articulos .= "</thead>";

                    $lista_articulos .= "</tbody>";

                    $total_compra = 0;

                    #--

                    $items[] = array(

                        'id' => 1,

                        'quantity' => 1,

                        'unit_price' => 0

                    );

                    foreach ($items as $item)
                    {
                        if ( preg_match("#\_\d+#", $item['id']) ) //es promo
                        {
                            $ids = explode("_", $item['id']);
                            $item['id'] = $ids[0];
                            unset($ids[0]);

                            if ( ($itemsPromo = Articulo::whereIn('id_producto', $ids)->get()) )
                            {
                                foreach ($itemsPromo as $itemPromo)
                                {
                                    $item['title'] .= "<p style='margin:0 0 0 10px;font-size:13px;'>{$itemPromo->id_producto} - {$itemPromo->nombre_producto}</p>";
                                }
                            }
                        }

                        #--

                        if ( $item['id'] == Articulo::shippingCost )

                        {

                            continue;

                        }

                        $subtotal = ($item['quantity'] * floatval($item['unit_price']));

                        $total_compra += $subtotal;

                        #--

                        /*$linea_vta = new LineaVenta();

                        $linea_vta->id_producto = $item['id'];

                        $linea_vta->id_venta = $venta->id_venta;

                        $linea_vta->cantidad = floatval($item['quantity']);*/

                        #-- Comsión mp

                        if ( $item['id'] == 1 && !$subtotal )

                        {

                            $costo = (($total_paid_amount ?: $paid_amount) * ((floatval($this->config['retencion_mp']) + floatval($this->config['comision_mp'])) / 100));

                            $subtotal = round(($costo * -1), 2);

                            $item['unit_price'] = $subtotal;

                        }

                        /*$linea_vta->subtotal = $subtotal;

                        $linea_vta->save();*/

                        #--

                        $lista_articulos .= "<tr>";

                        $lista_articulos .= "<td align='center'>{$item['id']}</td>";

                        $lista_articulos .= "<td>{$item['id']} - {$item['title']}</td>";

                        $lista_articulos .= "<td align='center'>{$item['quantity']}</td>";

                        $lista_articulos .= "<td align='right'>" . HFunctions::formatPrice($item['unit_price']) . "</td>";

                        $lista_articulos .= "</tr>";

                        /*if ( $articulo = Articulo::find($item['id']) )

                        {

                            $cnt = $articulo->cantidad_array;

                            $key = Local::deposito;

                            if ( $cnt[$key] < $cnt[Local::depositoSecco] )

                            {

                                $key = Local::depositoSecco;

                            }

                            $stock = ($cnt[$key] - $item['quantity']);

                            if ( $stock < 0 )

                            {

                                $stock = 0;

                            }

                            $cnt[$key] = $stock;

                            $articulo->cantidad_json = $cnt;

                            $articulo->save();

                        }*/

                    }

                    #--

                    $lista_articulos .= "</tbody>";

                    $lista_articulos .= "</table>";

                    #--

                    $mensaje .= "<h2 style='font-style:italic;text-align:right'>Total: $ " . HFunctions::formatPrice($total_compra) . "</h2>";

                    $mensaje .= "Artículos:";

                    $mensaje .= $lista_articulos;

                    #-- Envio de correo

                    $correo_ventas = $this->config['email_ventas'];

                    $remitente_correo = $cliente->email ?: $correo_ventas;

                    $remitente_nombre = $cliente->nombre_apellido ?: $this->config['site_name'];

                    $correo = new Emailer();

                    $correo->setDestino($correo_ventas, $this->config['site_name']);

                    //$correo->setCopiaOculta($this->config['email_contacto'], "LS");

                    $correo->setAsunto("Compra Online");

                    $correo->setRemitente($remitente_correo, $remitente_nombre);

                    $correo->setEmailView($mensaje);

                    $correo->enviarEmail();

                    #--

                    //Polling::set($venta->id_venta, "compra");

                } //end check venta

            }

            #--

            if ( !$es_payment )

            {

                HArray::varDump($order_data);

            }

        }

    }

}