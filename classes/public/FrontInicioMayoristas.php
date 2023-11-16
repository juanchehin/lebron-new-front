<?php

class FrontInicioMayoristas extends FrontMainMayoristas

{

    protected $articulos;



    public function __construct()

    {

        parent::__construct();

    }



    public function index()

    {

        $this->setPageTitle();

        //$this->addStyle("static/plugin/carousel/owl.carousel.min.css");

        //$this->addScript("static/plugin/carousel/owl.carousel.min.js");

        #--

        // $promociones = Articulo::whereRaw("!`borrado` AND `publicado` AND `id_categoria`= '" . Categoria::ctgPromo . "'")->limit(15)->get();

        // $params['promoSection'] = self::renderView(self::publicViews . "/promo-section", ['titulo' => "Promociones", 'promociones' => $promociones]);

        // creatina; proteina; colageno; quemador de grasa
        $creatProtColagQuem = Articulo::whereRaw("!`borrado` AND `publicado` AND !`oculto` AND (`id_categoria`= 1 OR `id_categoria`= 8 OR `id_categoria`= 259 OR `id_categoria`= 2)")
                                
                                ->limit(15)
                                
                                ->get();
                        
        // Modifica precio si esta en USD
        foreach ($creatProtColagQuem as $creat)
        {
            
            if ( str_contains($creat->precio_compra, '|USD'))
            {
                $creat['precio_compra'] = $creat->precio_compra * $this->config['dolar_paralelo'];
            }

        }
        

        $params['creatProtColagQuemSection'] = self::renderView(self::publicViews . "/mix-section", ['titulo' => "creatina; proteina; colageno; quemador de grasa ", 'creatProtColagQuem' => $creatProtColagQuem]);

        // $promociones = $this->articulos(Categoria::ctgPromo, 15);        

        //$ofertas = Articulo::whereRaw("!`borrado` AND `publicado` AND `id_categoria`!='" . Categoria::ctgPromo . "' AND (`precio_online` < 0 OR dimension='o')")->orderBy("producto")->get();

        // $ofertas = Articulo::whereRaw("!`borrado` AND `publicado` AND !`id_parent` AND ((`id_categoria` != '" . Categoria::ctgPromo. "' AND `precio_online` < 0) OR dimension='o')")->orderBy("producto")->get();

        // $params['discountSection'] = self::renderView(self::publicViews . "/discount-section", ['titulo' => "Novedades / Ofertas", 'articulos' => $ofertas]);

        //$params['allProducts'] = $this->allProducts();

        $desde = HDate::modifyDate(date('Y-m-d'), "-30 days");

        $lineas = LineaVenta::selectRaw("id_producto, SUM(`cantidad`) AS qqq")

            ->whereRaw("`valor`= '" . Local::mitreNegocio . "' AND DATE(`fecha_hora`) >= '{$desde}' AND `atributo` LIKE '%_publico'")

            ->groupBy("id_producto")

            ->having("qqq", ">", 5)

            ->orderBy("qqq", "DESC")

            ->get();

        #--

        foreach ($lineas as $linea)

        {

            $articulo = $linea->hasArticulo;

            if ( $articulo->id_parent )

            {

                $articulo = $articulo->hasParent;

            }

            #--

            if ( $articulo->borrado || !$articulo->publicado )

            {

                continue;

            }

            $masVendidos[$articulo->id_producto] = $articulo;

        }

        //HArray::varDump(count($masVendidos));

        $options = array('slides' => 4, 'infinite' => 1);

        $params['masVendidos'] = FrontSectionMayoristas::blockSection($masVendidos, "Mas Vendidos", $options);

        // $indumentaria = $this->articulos(Categoria::ctgArticulosGym, 12);

        // $params['indumentariaSection'] = FrontSectionMayoristas::blockSection($indumentaria, "Accesorios / GYM", $options);

        // $params['indumentariaSection'] = FrontSectionMayoristas::blockSection($indumentaria, "Accesorios / GYM", $options);

        $params['creatProtColagQuem'] = FrontSectionMayoristas::blockSection($creatProtColagQuem, "Creatina / proteina / Colageno / Quemador de grasa", $options);

        //self::renderView(self::publicViews . "/popular-section", ['titulo' => "Mas Vendidos", 'populares' => $masVendidos]);

        $params['slider'] = static::renderView(self::publicViews . "/slider-section-mayoristas");

        $this->setParams($params);

        $this->setBody("front-inicio-mayoristas");

    }



    public function construccion()

    {

        $this->setPageTitle();

        $html = "<div class='container text-center'>";

        $html .= "<h1 style='font-size:42px'>LeBron Suplementos</h1>";

        $html .= "<h2>Sitio en Construcción</h2>";

        $html .= "<h4 style=''><i>Muy Pronto!!</i></h4>";

        $html .= "<hr>";

        $html .= "</div>";

        $html .= "<script>document.getElementsByClassName('nav-group')[0].innerHTML = '';</script>";

        $this->setBody($html, true);

    }



    public function contacto()

    {

        if ( !empty($_POST) && self::isXhrRequest() )

        {

            //ini_set("display_errors","On");

            $nombre = $this->logged_user->nombre_persona ?: trim($_POST['nombre']);

            $email = $this->logged_user->email ?: trim($_POST['email']);

            $telefono = trim($_POST['telefono']);

            $mensaje = trim($_POST['mensaje']);

            $tag = trim($_POST['tag']) ?: "contacto";

            #--

            if ( !$nombre )

            {

                HArray::jsonError("Ingresa tu nombre", "nombre");

            }



            if ( !filter_var($email, FILTER_VALIDATE_EMAIL) )

            {

                HArray::jsonError("Ingresa un correo electrónico válido.", "email");

            }



            if ( $telefono && strlen($telefono) < 10 )

            {

                HArray::jsonError("El número de teléfono debe contener código de área + número", "telefono");

            }



            if ( !strip_tags($mensaje) )

            {

                HArray::jsonError("Indique su mensaje.", "mensaje");

            }

            $body = "<h3>Se recibi&oacute; un mensaje desde la Web</h3>";

            $body .= "<h4>Nombre: <i>{$nombre}</i></h4>";

            $body .= "<h4>E-mail: <i>{$email}</i></h4>";

            $body .= "<h4>Tel&eacute;fono: <i>{$telefono}</i></h4>";

            $body .= "<h4>Mensaje:<br/><i>{$mensaje}</i></h4>";

            $body .= "<hr>" . date('d/m/Y H:i:s');

            #--

            $contacto = new Emailer();

            $contacto->setAsunto("Contacto desde la Web");

            $contacto->setDestino($this->config['email_' . $tag], SITE_NAME);

            $contacto->setRemitente($email, $nombre);

            //$contacto->setCopiaOculta($this->config['email_contacto']);

            $contacto->setEmailHtml($body);

            $contacto->enviarEmail();

            $mensaje = "<span class='text-info'>Hemos recibido su consulta, le responderemos a la brevedad. ";

            $mensaje .= "Muchas gracias por contactarse con nosotros. " . HDate::dayMoment() . ".</span>";

            $json['notice'] = $mensaje;

            $json['ok'] = true;

            HArray::jsonResponse($json);

            exit;

        }

        $this->setBody("form-contacto");

    }


}

?>