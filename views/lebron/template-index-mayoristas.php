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

        <div class="container">

            <div class="aa-header-bottom">

                <div class="col-md-12">

                    <div class="aa-header-bottom-area">

                        <div class="aa-logo">

                            <a href="./">

                                <img src="<?= $logo_src ?>" alt="Lebron Suplementos" width="200">

                            </a>

                        </div>

                        <div class="col-md-5 aa-search-box" id="dv-search-box" style="margin-left: 160px;">

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


                        <div class="aa-cartbox" id="cartbox">

                            <a class="aa-cart-link" href="<?= $site_url ?>/mayoristas/checkout">

                                <span class="fa fa-shopping-basket"></span>

                                <span class="aa-cart-notify" id="aa-cart"></span>

                            </a>

                            <?php if ( false ): ?>

                                <div class="aa-cartbox-summary">

                                    <ul>

                                        <li>

                                            <a class="aa-cartbox-img" href="#"><img src="img/woman-small-2.jpg" alt="img"></a>

                                            <div class="aa-cartbox-info">

                                                <h4><a href="#">Product Name</a></h4>

                                                <p>1 x $250</p>

                                            </div>

                                            <a class="aa-remove-product" href="#"><span class="fa fa-times"></span></a>

                                        </li>

                                        <li>

                                            <a class="aa-cartbox-img" href="#"><img src="img/woman-small-1.jpg" alt="img"></a>

                                            <div class="aa-cartbox-info">

                                                <h4><a href="#">Product Name</a></h4>

                                                <p>1 x $250</p>

                                            </div>

                                            <a class="aa-remove-product" href="#"><span class="fa fa-times"></span></a>

                                        </li>

                                        <li>

                      <span class="aa-cartbox-total-title">

                        Total

                      </span>

                                            <span class="aa-cartbox-total-price">

                        $500

                      </span>

                                        </li>

                                    </ul>

                                    <a class="aa-cartbox-checkout aa-primary-btn" href="checkout.html">Checkout</a>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

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

                            <?= date('2019 - Y') ?>

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