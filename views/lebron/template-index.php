<?php if ( !DEVELOPMENT ): ?>

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-138434702-1"></script>

    <script>

        window.dataLayer = window.dataLayer || [];



        function gtag()

        {

            dataLayer.push(arguments);

        }



        gtag('js', new Date());

        gtag('config', 'UA-138434702-1');



        (adsbygoogle = window.adsbygoogle || []).push({

            google_ad_client: "ca-pub-9722141461016863",

            enable_page_level_ads: true

        });



    </script>

<?php endif; ?>

<style>

    #hh-slogan {

        font-style: italic;

        color: #ffcf40;

        font-size: 20px;

        margin: 0;

        text-align: center;

        letter-spacing: 0;

        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;

        line-height: 21px;

        display: block;

    }



    #free-ship {

        color: #5cb85c;

        text-align: right;

        font-style: italic;

        font-weight: 400;

        margin: -26px 0 10px;

    }



    #aa-cart {

        z-index: 9;

        position: fixed;

        right: 5px;

    }



    #aa-cart:hover {

    }



    #dv-publi-franquicia {

        display: flex;

        height: 50px;

        background: url('static/images/investment.jpg') center no-repeat;

        background-position: 0 55%;

        align-items: center;

        justify-content: center;

        background-size: cover;

        font-size: 23px;

        font-weight: 600;

        color: #13ab09;

        cursor: pointer;

        text-align: center;

        line-height: 22px;

        border: 1px solid #ffd208;

    }



    #dv-publi-franquicia:hover {

        opacity: .85;

    }

    /* ***Desplegable marcas*** */
        .dropbtn {
            background-color: #ee4532;
            color: white;
            padding: 16px;
            font-size: 16px;
            border: none;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            z-index: 9;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f1f1f1;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {background-color: #ddd;}

        .dropdown:hover .dropdown-content {display: block;}

        .dropdown:hover .dropbtn {background-color: #ee4532;}

        /* ***Fin Desplegable marcas*** */

        /* **** Boton mayoristas *** */

        .square {
            width: 190px;
            height: 60px;
            background-color: #fc0000;
            font-size: 35px;
            color: rgb(255, 255, 255);
            text-align: center;
        }

        .squareA {
        -webkit-animation: sqrA 4s infinite linear;
                animation: sqrA 4s infinite linear;
        }

        @-webkit-keyframes sqrA {
            from {
                -webkit-transform: translateX(120px) rotateY(0deg);
                        transform: translateX(120px) rotateY(0deg);
            }
            to {
                -webkit-transform: translateX(120px) rotateY(360deg);
                        transform: translateX(120px) rotateY(360deg);
            }
        }

        @keyframes sqrA {
            from {
                -webkit-transform: translateX(120px) rotateY(0deg);
                        transform: translateX(120px) rotateY(0deg);
            }
            to {
                -webkit-transform: translateX(120px) rotateY(360deg);
                        transform: translateX(120px) rotateY(360deg);
            }
        }


        /* ****** */

</style>

<?php

$wspUrl = "//wa.me/54" . preg_replace("#[\s+,\-]#", "", ($cellphone = $arr_contacto['telefono']));

?>

<div id="wpf-loader-two">

    <div class="wpf-loader-two-inner">

        <span>Loading</span>

    </div>

</div>

<?php if ( !$hidden_header ): ?>

    <a class="scrollToTop" href="#"><i class="fa fa-chevron-up"></i></a>

    <header id="aa-header">

        <!-- *** Telefono *** -->
        <div class="aa-header-top">

            <div class="col-md-12">

                <div class="aa-header-top-area">

                    <div class="aa-header-top-left">

                        <div class="cellphone">

                            <p><a href="<?= $wspUrl ?>"><i class="fab fa-whatsapp"></i> <?= $cellphone; ?></a></p>

                        </div>

                    </div>

                    <div class="aa-header-top-right">

                        <div style="margin:3px 15px;color:#fff;text-align:right;" id="dv-user-caption"></div>

                    </div>

                </div>

            </div>

        </div>

        <!-- *** Logo + Buscador + boton may *** -->
        <div class="container">

            <div class="aa-header-bottom">
                    <div class="aa-header-bottom-area">

                        <div class="row">

                            <div class="col-md-2">
                                <div class="aa-logo">

                                    <a href="./">

                                        <img src="<?= $logo_src ?>" alt="Lebron Suplementos" width="200">

                                    </a>

                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="dropdown">
                                    <button class="dropbtn">Marcas</button>
                                    <div class="dropdown-content">
                                        <a href="#">BCAA</a>
                                        <a href="#">IDN</a>
                                        <a href="#">Ena</a>
                                        <a href="#">Hoch</a>
                                        <a href="#">Ultra tech</a>
                                        <a href="#">Lebron</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-5">
                                <div class="aa-search-box" id="dv-search-box">

                                    <form id="frm-search" action="<?= $site_url . "/{$listPath}" ?>" autocomplete="off">

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

                            <div class="col-md-2">
                                <a href="<?= $site_url ?>/mayoristas">
                                    <div class="square squareA" style="margin-left: -110px; border-radius: 30px;">Mayoristas</div>   
                                </a>
                            </div>
                        </div>

                        <!-- *** Carrito floante *** -->
                        <div class="aa-cartbox" id="cartbox">
                            <a class="aa-cart-link" href="<?= $site_url ?>/checkout">

                                <span class="fa fa-shopping-basket"></span>

                                <span class="aa-cart-notify" id="aa-cart"></span>

                            </a>
                        </div>
                        <!-- *** Fin Carrito floante *** -->

                    </div>
            </div>

        </div>
        <!-- *** Fin Logo + Buscador + boton may *** -->     

    </header>

<?php endif; ?>

<?= $slider ?>

<section class="main-content">

    <div class="container">

        <div class="" id="body-header">

            <?php if ( false ): ?>

                <div class="col-md-4"></div>

                <div class="col-md-3">

                    <a id="dv-publi-franquicia" href="<?= $site_url . "/franquicia.html" ?>">

                        Conocé<br>Nuestra Franquicia

                    </a>

                </div>

            <?php endif; ?>

            <div class="clearfix"></div>

        </div>

        <div class="" id="page-body" style="min-height:65vh;margin-bottom:25px">

            <?= $_view_content ?>

        </div>

    </div>

    <?= $infoSection ?>

</section>

<?php if ( !$hidden_footer ): ?>

    <footer id="aa-footer">

        <div class="container-fluid">

            <div class="aa-footer-top">

                <a href="<?= $wspUrl ?>" target="_blank" style="z-index:100;position: fixed;bottom:6%;left:10px"><img width="55" alt="wsp" src="static/images/wsp-icon.png"></a>

                <div class="aa-footer-top-area">

                    <div class="col-md-5">

                        <?= $sucursales ?>

                    </div>

                    <div class="col-md-3 col-sm-6">

                        <div class="aa-footer-widget">

                            <h3>Institucional</h3>

                            <div class="aa-footer-nav">

                                <?php foreach ($institucional as $url => $label): ?>

                                    <a href="<?= $url ?>"><?= $label ?></a>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3 col-sm-6">

                        <div class="aa-footer-widget text-center">

                            <h3>&nbsp;</h3>

                            <img src="static/logo-lebron.jpg" width="180" alt="logo footer"/>

                            <img src="static/images/usa-flag.png" width="150" alt="usa-flag"/>

                            <p></p>

                            <div class="aa-footer-social">

                                <?= $social_links ?>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- footer-bottom -->

            <div class="aa-footer-bottom">

                <div class="row">

                    <div class="col-md-12">

                        <div class="aa-footer-bottom-area">

                        <div class="">
                            <div class="col-xs-12">                                
                                <script>
                                </script> <a href="https://juanchehin.github.io/slider/" target="_blank">Copyright &copy; <?= date('2019 - Y') ?></a>
                            </div>
                        </div>                            

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </footer>

<?php endif; ?>

<script type="text/javascript">

    $(".scroll").click(function (event) {

        event.preventDefault();

        $('html,body').animate({

            "scrollTop": $(this.hash).offset().top

        }, 1200);

    });



    function get_count_cart(checkLogin)

    {

        if ( checkLogin )

        {

            (dvUserCaption = document.getElementById('dv-user-caption')).innerHTML = "";

            if ( Object.keys(usr = checkLoginState()).length )

            {

                caption = usr.nombre + "&nbsp;&nbsp;";

                caption += `<a href="javascript:void(0)" id="aa-logout" style="color:#fff;"><i class='fa fa-sign-out-alt'></i></a>`;

                dvUserCaption.innerHTML = caption;

                document.getElementById('aa-logout').onclick = function (evnt) {

                    evnt.preventDefault();

                    fetch("!FrontMain/logout");

                    sessionStorage.removeItem(sesAuth);

                    get_count_cart(true);

                };

            }

            return;

        }

        if ( !(cartCount = document.getElementById('aa-cart')) )

        {

            return;

        }

        cartCount.innerHTML = "<i class='fa fa-spin fa-spinner'></i>";

        fetch("!FrontCart/getItemsCart?q").then(function ($res) {

            $res.text().then(function ($txt) {

                cartCount.innerHTML = parseInt($txt || 0);

                cartCount.setAttribute("title", `Artículos agregados: ${$txt}`);

            })

        });

    }



    get_count_cart();

</script>