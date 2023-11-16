<?php



class FrontMain extends MainTemplate

{

    const siteUrl = HTTP_HOST . tmp_url;

    const urlInicioUsuario = self::siteUrl . "/" . USER_CP;

    const montoEnvioGratis = 30000;

    const publicViews = "lebron";

    const listPath = "catalogo";

    protected $logged_user;

    protected $logo_src;

    protected $articulos;



    #--

    public function __construct()

    {

        parent::__construct();

        $this->addStyle("//fonts.googleapis.com/css?family=Lato");

        $this->addStyle("//fonts.googleapis.com/css?family=Raleway");

        //$this->addStyle("static/public/tpl/css/jquery.simpleLens.css");

        $this->addStyle("static/public/tpl/css/slick.css");

        //$this->addStyle("static/public/tpl/css/nouislider.css");

        $this->addStyle("static/public/tpl/css/red-theme.css");

        $this->addStyle("static/public/tpl/css/style.css?ver={$this->version}");

        //$this->addScript("static/public/tpl/js/sequence.js", true);

        //$this->addScript("static/public/tpl/js/nouislider.js");

        //$this->addScript("static/public/tpl/js/jquery.simpleGallery.js", true);

        //$this->addScript("static/public/tpl/js/jquery.simpleLens.js", true);

        $this->addScript("static/public/tpl/js/slick.js", true);

        $this->addScript("static/public/tpl/js/custom.js?ver={$this->version}", true);

        $this->addScript("static/public/js/JsArticulo.js?ver={$this->version}", true);

        $this->addScript("static/public/js/app-main.js?ver={$this->version}");

        $this->_prepare();

        $this->_userControl();

    }



    protected function getHoy($hora = true)

    {

        return HDate::today($hora);

    }



    private function _prepare()

    {

        $this->logo_src = self::siteUrl . "/static/lebron-transparent.png";

        $this->setViewSubdir(self::publicViews);

        $meta_tags = array(

            'country' => "Argentina",

            'Locality' => "San Miguel de Tucumán, Argentina",

            'language' => "es",

            'Googlebot' => "all",

            //'distribution' => "global",

            'robots' => "index, follow",

            'description' => "Suplementos Nacionales e Importados al mejor precio!",

            'keywords' => "suplementos, proteínas, aminoacidos, whey, creatinas, quemadores de grasa"

        );

        $this->addMetaAttr($meta_tags);

        $fb['og:description'] = $meta_tags['description'];

        $fb['og:type'] = "website";

        $fb['og:url'] = preg_replace("#^\/#", self::siteUrl . "/", $_SERVER['REDIRECT_URL']);

        $fb['og:image'] = self::siteUrl . "/media/image/default.jpg";

        $fb['og:country-name'] = $meta_tags['country'];

        $fb['og:locale'] = "es_AR";

        $fb['og:site_name'] = SITE_NAME;

        $this->addFbProperty($fb);

        #--

        $parm['site_url'] = preg_replace("#\/$#", "", self::siteUrl);

        $parm['listPath'] = self::listPath;

        $parm['montoEnvioGratis'] = self::montoEnvioGratis;

        $parm['logo_src'] = $this->logo_src;

        $parm['arr_contacto'] = $this->config['contacto'];

        #--

        $parm['sucursales'] = self::renderView(self::publicViews . "/sucursales-section", ['sucursales' => $this->config['location']]);

        $parm['infoSection'] = static::renderView(self::publicViews . "/info-section", ['montoEnvioGratis' => self::montoEnvioGratis]);

        $parm['institucional'] = array(

            self::siteUrl . "/contacto.html" => "Contacto",

            self::siteUrl . "/franquicia.html" => "Franquicia",

            self::siteUrl . "/terminos-y-condiciones.html" => "Términos y Condiciones",

            self::siteUrl . "/politicas-de-seguridad.html" => "Políticas de Seguridad"

        );

        $this->setParams($parm);

        $this->template = self::publicViews . "/template-index";

        $this->showHeader();

        $this->socialHtml();

    }



    protected function socialHtml()

    {

        $rs = $this->config['social'];

        $social = "<div class='social'>";

        $social = "";

        if ( $contactForm = $rs['contacto'] )

        {

            $social .= "<a href='{$contactForm}' title='Escríbenos!'><i class='fa fa-envelope'></i></a>";

        }

        if ( $fbk = $rs['facebook'] )

        {

            $social .= "<a href='{$fbk}'><i class='fab fa-facebook-square'></i></a>";

        }

        #--

        if ( $twitter = $rs['twitter'] )

        {

            $social .= "<a href='{$twitter}'><i class='fab fa-twitter'></i></a>";

        }

        #--

        if ( $instagram = $rs['instagram'] )

        {

            $social .= "<a href='{$instagram}'><i class='fab fa-instagram'></i></a>";

        }

        $this->setParams('social_links', $social);

    }



    protected function showHeader($value = true)

    {

        $this->setParams('show_header', $value);



        return;

    }



    protected function setPageTitle($title = null)

    {

        $_title[] = SITE_NAME;

        $_title[] = $title;

        parent::setPageTitle(implode(' | ', array_filter($_title)));

        $this->addFbProperty('og:title', $this->getPageTitle());

        if ( $_title[1] )

        {

            $this->setParams('titulo_seccion', $_title[1]);

        }

    }



    protected function getLoginSession()

    {

        return unserialize(HSession::getSession("sessionUser"));

    }



    protected function setLoginSession($collection = null)

    {

        $usrSession = "sessionUser";

        if ( $collection )

        {

            HSession::setSession($usrSession, serialize($collection));

        }

        else

        {

            HSession::removeSession($usrSession);

        }

        return;

    }



    private function _userControl()

    {

        /*$fb = new Facebook\Facebook(array(

            'app_id' => '355127731747742', // Replace {app-id} with your app id

            'app_secret' => "084b15105c0f9b49a50537a1546d26cb",

            'default_graph_version' => 'v3.2',

        ));



        $helper = $fb->getRedirectLoginHelper();



        $permissions = ['email']; // Optional permissions

        $set['loginUrl'] = $helper->getLoginUrl(self::siteUrl."/!FrontUsuario/auth", $permissions);*/

        #--

        if ( $token = $_GET['auth'] )

        {

            $where['token'] = $token;

            if ( $usuario = Cliente::where($where)->first() )

            {

                $this->setLoginSession($usuario);

            }

        }

        $user = $this->getLoginSession();

        $this->logged_user = Cliente::find($user->id);

        #--

        $this->setParams('user', $this->logged_user);

    }



    public function articulos($ctg = null, $return = 0)

    {

        $pp = 12;

        $str_buscar = trim($_REQUEST['q']);

        $id_marca = trim($_REQUEST['marca']);

        $id_categoria = floatval($ctg ?: $_REQUEST['ctg']);

        $where[] = "!`borrado`";

        $where[] = "`publicado`";

        // $where[] = "!`id_parent`";

        //$where[] = "`precio_online` >= 0";

        if ( $id_categoria )

        {

            $where[] = "`id_categoria` = '{$id_categoria}'";

        }

        else

        {

            $where[] = "`precio_online` >= 0 AND `id_categoria` NOT IN(" . Categoria::ctgPromo . ")";

        }

        #--

        if ( $id_marca )

        {

            $where[] = "`id_marca`='{$id_marca}'";

        }

        #--

        //$where[] = "(`precio` > 0 OR `precio_online` > 0)";

        $where[] = "`producto` LIKE '%{$str_buscar}%'";

        /*$query->whereHas("hasLineaVenta", function ($q) {

            $q->where('flag', "e")->selectRaw("COUNT(id_producto) as 'k'")->groupBy('id_producto')->having("k", ">", 8);

        });*/

        $str_where = implode(" AND ", $where);

        $query = Articulo::selectRaw("`producto`.*, CAST(JSON_EXTRACT(`cantidad`, '$.\"" . Local::mitreNegocio . "\"') AS UNSIGNED) as stock")->whereRaw($str_where);

        $query->orderBy("stock", "DESC");

        if ( $return )

        {

            return $query->limit($return)->get();

        }

        $this->articulos = $query->paginate($pp);

        //$this->setParams('articulos', $articulos);

        //$content = $this->loadView("lebron/articulos-list");

        $content = $this->articulosList();

        if ( self::isXhrRequest() )

        {

            die($content);

        }

        return $content;

    }



    private function articulosList()

    {

        ob_start();

        ?>

<div class="row">
            
<div class="col-md-5 aa-search-box" id="dv-search-box">

<form id="frm-search" action="<?= self::siteUrl . "/catalogo" ?>" autocomplete="off">

    <div class="input-group">

        <input type="text" name="q" id="q" placeholder="Buscar">

        <div class="input-group-addon">

            <button type="submit"><span class="fa fa-search"></span></button>

        </div>

    </div>

</form>

<p class="clearfix"></p>

</div>
</div>

<div class="row">
        <div class="aa-product-catg">



            <?php if ( !$this->articulos[0] ) : ?>

                <div class="col-md-12">

                    <div class="panel panel-info" style="margin-top:3px">

                        <div class="panel-body text-center">

                            <h4>No se encontraron art&iacute;culos</h4>

                        </div>

                    </div>

                </div>

            <?php else: ?>

                <?php foreach ($this->articulos as $articulo): ?>

                    <div class="col-md-4 form-group">

                        <?php

                        $data = null;

                        $id = $articulo->id_producto;

                        $href_url = self::siteUrl . "/articulo/{$id}";

                        ?>

                        <div class="product-item">

                            <figure>

                                <span class="aa-badge aa-sale" href="#" hidden>SALE!</span>

                                <a class="aa-product-img" href="<?= $href_url ?>">

                                    <img src="<?= $articulo->imagenes(true) ?>" alt="<?= $id ?>">

                                </a>

                                <figcaption>

                                    <h4 class="aa-product-title"><a href="<?= $href_url ?>"><?= $articulo->nombre ?></a></h4>

                                    <?= FrontArticulo::articuloOpcion($articulo); ?>

                                </figcaption>

                            </figure>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
        </div>
        <div class="text-center" style="margin-top: 15px">

            <?= preg_replace("#\<a\s+#", "<a onclick='paginar(this)' ", $this->articulos->links()); ?>

        </div>

        <?php

        return ob_get_clean();

    }



    protected function allProducts()

    {

        $categorias = Categoria::categorias(null, true);

        $articulos_list = $this->articulos();

        $marcas = Categoria::where(['tipo' => Categoria::tipoMarca, 'activo' => 1])->get();

        $brandSection = self::renderView(self::publicViews . "/brand-section", ['marcas' => $marcas]);

        ob_start();

        ?>

        <div class="" id="dv-articulos-container">

            <div class="col-md-12" id="dv-filter-group">

                <?= $brandSection ?>

                <div id="list-title" style="display: inline-block;min-height:40px">&nbsp;</div>

            </div>

            <div class="col-md-2">

                <div class="dropdown" id="group-menu">

                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Categor&iacute;as

                        <span class="caret"></span></button>

                    <div class="dropdown-menu" id="dd-menu">

                        <?php foreach ($categorias as $categoria): ?>

                            <a href="javascript:void(0)" onclick="set_tag(this)" id="ctg-<?= $categoria->id_item ?>"><?= $categoria->titulo ?></a>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

            <div class="col-md-10" id="articulos-list">

                <?= $articulos_list ?>

            </div>

        </div>

        <style>

            .hh-tag {

                background: #0D3349;

                color: #fff;

                margin-right: 12px;

                padding: 3px 12px;

                display: inline-block;

            }



            .hh-tag span {

                cursor: pointer;

                background: #FF0000;

                border-radius: 6px;

                padding: 0 4px;

            }



            #articulos-list {

                min-height: 60vh;

            }



            #group-menu .dropdown-toggle {

                display: none;

            }



            #group-menu #dd-menu a {

                padding: 8px;

                display: block;

                color: #0b0b0b;

                font-weight: 600;

            }



            #group-menu #dd-menu a:hover, #group-menu #dd-menu a.current {

                background: #ff0000;

                color: #fff;

            }



            #group-menu .dropdown-menu {

                position: relative;

                display: inline-block;

                width: 100%;

                padding: 5px;

                margin-bottom: 25px;

                box-shadow: none;

            }



            .navbar-primary.collapsed .glyphicon {

                font-size: 22px;

            }



            @media screen and (max-width: 990px) {

                #group-menu .dropdown-toggle {

                    display: block;

                }



                #group-menu .dropdown-menu {

                    display: none;

                    position: absolute;

                    box-shadow: none;

                    width: auto;

                }



                #group-menu.open .dropdown-menu {

                    display: block;

                }

            }

        </style>

        <script>

            // document.getElementById('dv-search-box').remove();

            params = {};

            document.getElementById('dv-search-box').innerHTML = `<a style="display:none" onclick="set_tag(this)" id="q-<?=$_GET['q']?>"><?=mb_strtoupper($_GET['q'])?></a>`;

            <?php

                unset($_GET['p']);

                foreach ($_GET as $key => $value):?>

                    if ( (option = document.getElementById('<?=$key . "-{$value}"?>')) )
                    {
                        option.click();
                    }

            <?php endforeach; ?>

            // document.getElementById('list-title').innerHTML = caption.join("");

        </script>

        <?php

        return ob_get_clean();

    }



    public function logout()

    {

        $this->setLoginSession();

        header("location:" . ($_SERVER['HTTP_REFERER'] ?: self::siteUrl));

    }



    public function paginaNoEncontrada()

    {

        $this->setPageTitle("Página no encontrada");

        $this->setBody("404.html");

    }

}