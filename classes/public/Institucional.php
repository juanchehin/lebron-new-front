<?php



class Institucional extends FrontMain

{

    public function terminosCondiciones()

    {

        $this->setPageTitle("Términos y condiciones");

        $this->setBody("terminos-y-condiciones.html");

    }



    protected function contactForm($tag = null)

    {

        ob_start();

        ?>

        <form class="comments-form" id="contact-form" action="!FrontInicio/contacto" autocomplete="off">

            <div class="row">

                <div class="col-md-12 form-group">

                    <input type="text" id="nombre" value="<?= $this->logged_user->nombre_apellido ?>" name="nombre" maxlength="40" required class="form-control" placeholder="Nombre">

                </div>

                <div class="col-md-6 form-group">

                    <input type="email" name="email" value="<?= $this->logged_user->email ?>" class="form-control" placeholder="Ingres&aacute; tu E-mail" required>

                </div>

                <div class="col-md-6 form-group">

                    <input type="tel" class="form-control" name="telefono" placeholder="Teléfono (Whatsapp)">

                </div>

                <div class="col-md-12 form-group">

                    <textarea style="resize: none" name="mensaje" class="form-control" rows="4" placeholder="Mensaje" required></textarea>

                </div>

            </div>

            <div class="form-group text-right">

                <input type="hidden" name="tag" value="<?= $tag ?>">

                <button type="submit" class="btn-success btn">Enviar <i class="fa fa-envelope"></i></button>

            </div>

        </form>

        <script>

            document.getElementById('contact-form').onsubmit = function (evt) {

                evt.preventDefault();

                let thisForm = this;

                //thisForm.append();

                submit_form(this, function () {

                    thisForm.reset();

                });

            };

        </script>

        <?php

        return ob_get_clean();

    }

    public function franquicia()

    {

        $this->setPageTitle("Franquicia");

        $this->setParams("modalBlock", $this->modalBlock());

        $this->setParams('contactForm', $this->contactForm("franquicia"));

        $this->setBody("franquicia");

    }



    public function politicasDeSeguridad()

    {

        $this->setPageTitle("Políticas de seguridad");

        $this->setBody("politicas-seguridad.html");

    }



    public function politicaDePrivacidad()

    {

        $this->setPageTitle("Políticas de privacidad");

        $this->setBody("politicas-privacidad.html");

    }



    public function defensaDelConsumidor()

    {

        $this->setPageTitle("Defensa del consumidor");

        $this->setBody("defensa-consumidor.html");

    }

    public function ingresarMiCv()

    {

        $this->setPageTitle("Ingresar mi CV");

        $this->setBody("front-ingresar-mi-cv");

    }



    public function contacto()

    {

        $this->setPageTitle("Sobre Nosotros");

        $this->setParams('show_location', true);

        $this->setPageTitle("Contacto");

        $clientId = "3bd1e3616229fb836b84031a8ad2dc6441d081dc";

        $clientSecret = "0IEbGtSsmKyTVZiZyy5sgz2am5hYWNeBU7Ez+Ew/5B4pZF6ItTTg38jCzh/DjYmTZKMcyF1SZGID+cI8q78lm8rmXD+ZcqpVe+spFC93SwMalsaPYVWggGqFwy3l0gjN";

        $vimeoCnf = [

            'access_token' => "3ecf21d95715f31d39e4ef20ecd03c86",

            'token_type' => "bearer",

            'scope' => "public",

            'channel' => "1703329"

        ];

        $vimeo = new CurlClass("https://api.vimeo.com");

        //$vimeo->setHeaderData(["Authorization" => "basic ".base64_encode("{$clientId}:{$clientSecret}"), "Content-Type" => "application/json", "Accept" => "application/vnd.vimeo.*+json;version=3.4"]);

        $vimeo->setHeaderData(["Authorization" => "bearer {$vimeoCnf['access_token']}", "Content-Type" => "application/json", "Accept" => "application/vnd.vimeo.*+json;version=3.4"]);

        $vimeo->setPath("channels/{$vimeoCnf['channel']}/videos");

        //$vimeo->setPath("oauth/authorize/client");

        //$vimeo->setData(["grant_type" => "client_credentials", "scope" => "public"]);

        $result = $vimeo->callAPI();

        //HArray::varDump($result);

        // $ubicacion = self::renderView(self::publicViews . "/sucursales-section", ['sucursales' => $this->config['location'], 'showMap' => 1]);

        $this->setParams('videos', $result['data']);

        $this->setParams('contactForm', $this->contactForm());

        // $this->setParams('ubicacion', $ubicacion);

        $this->setBody("front-contacto");

    }

}