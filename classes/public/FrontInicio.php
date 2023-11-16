<?php

class FrontInicio extends FrontMain

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

        $promociones = Articulo::whereRaw("!`borrado` AND `publicado` AND `id_categoria`= '" . Categoria::ctgPromo . "'")->limit(15)->get();

        $params['promoSection'] = self::renderView(self::publicViews . "/promo-section", ['titulo' => "Promociones", 'promociones' => $promociones]);

        // creatina; proteina; colageno; quemador de grasa 
        $creatProtColagQuem = Articulo::whereRaw("!`borrado` AND `publicado` AND !`oculto` AND (`id_categoria`= 1 OR `id_categoria`= 8 OR `id_categoria`= 259 OR `id_categoria`= 2)")
                                
                                ->limit(15)
                                
                                ->get();

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

        $params['masVendidos'] = FrontSection::blockSection($masVendidos, "Mas Vendidos", $options);

        $indumentaria = $this->articulos(Categoria::ctgArticulosGym, 12);

        // $params['indumentariaSection'] = FrontSection::blockSection($indumentaria, "Accesorios / GYM", $options);

        // $params['indumentariaSection'] = FrontSection::blockSection($indumentaria, "Accesorios / GYM", $options);

        $params['creatProtColagQuem'] = FrontSection::blockSection($creatProtColagQuem, "Creatina / proteina / Colageno / Quemador de grasa", $options);

        //self::renderView(self::publicViews . "/popular-section", ['titulo' => "Mas Vendidos", 'populares' => $masVendidos]);

        $params['slider'] = static::renderView(self::publicViews . "/slider-section");

        $this->setParams($params);

        $this->setBody("front-inicio");

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

    public function cv()

    {

            //ini_set("display_errors","On");
            $apellidos = trim($_POST['apellidos']);

            $nombres = trim($_POST['nombres']);

            $email = trim($_POST['email']);

            $telefono = trim($_POST['telefono']);

            $domicilio = trim($_POST['domicilio']);

            $dni = trim($_POST['dni']);

            $puesto = trim($_POST['puesto']);

            #--

            
            if ( !$apellidos )

            {

                HArray::jsonError("Ingresa tu apellido", "apellido");

            }

            if ( !$nombres )

            {

                HArray::jsonError("Ingresa tu nombre", "nombre");

            }



            if ( !filter_var($email, FILTER_VALIDATE_EMAIL) )

            {

                HArray::jsonError("Ingresa un correo electrónico válido.", "email");

            }

            #--

            $file_type = $_FILES["curriculum"]["type"];
            
            $targetfolder = $_SERVER["DOCUMENT_ROOT"] . "/media/uploads/";

            $namedFile = rand() . ".pdf";

            $targetfolder = $targetfolder . $namedFile;

            $moved = move_uploaded_file($_FILES['curriculum']['tmp_name'], $targetfolder);

            $sizeFile = $_FILES['curriculum']['size'];

            if (($file_type != "application/pdf") || (!$moved) || ($sizeFile > 500000)) {
                    $mensaje = "Ocurrio un error";
            }

            // ***

            try{

                $postulante = new Postulantes;
    
                $postulante->apellidos =  $apellidos;
        
                $postulante->nombres = $nombres;
        
                $postulante->telefono = $telefono;
    
                $postulante->domicilio = $domicilio;
    
                $postulante->email = $email;
    
                $postulante->dni = $dni;
    
                $postulante->puesto = $puesto;
    
                $postulante->cv = $namedFile;
        
                $postulante->save();

                $mensaje = "Sus datos se cargaron de forma exitosa.</span>";

            }catch(Exception $e){
                $mensaje = "Ocurrio un error";
            }
           

            // ****

            if($moved && $mensaje != "Ocurrio un error")
            {
                $this->setPageTitle();

                $html = "<div class='container text-center'>";

                $html .= "<h1 style='font-size:42px'>LeBron Suplementos</h1>";

                $html .= "<h2>¡Sus datos se cargaron de forma exitosa!</h2>";

                $html .= "<h4 style=''><i>Sus datos se almacenaron en nuestras bases de datos. Nos pondremos en contacto con usted.</i></h4>";

                $html .= "<hr>";

                $html .= "</div>";

                $this->setBody($html, true);
            }else{
                $this->setPageTitle();

                $html = "<div class='container text-center'>";

                $html .= "<h1 style='font-size:42px'>LeBron Suplementos</h1>";

                $html .= "<h2>Ocurrio un error al cargar su CV</h2>";

                $html .= "<h4 style=''><i>Intentelo de nuevo mas tarde.</i></h4>";

                $html .= "<hr>";

                $html .= "</div>";

                $this->setBody($html, true);
            }

    }

}