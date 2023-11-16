<?php if ( !DEVELOPMENT ): ?>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script async src="//www.googletagmanager.com/gtag/js?id=UA-138434702-1"></script>
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
        margin: -15px 0 10px;
        color: #5cb85c;
        text-align: right;
        font-style: italic;
        font-weight: 400;
    }

    #aa-cart {
        z-index: 9;
        position: fixed;
        background: #ff0000;
        right: 5px;
        border-radius: 35px;
        border: 1px solid #000;
        margin-top: 15px;
    }

    #aa-cart:hover {
        opacity: .8;
    }

    /* .nav-group {
         background-image: url("static/images/usa-flag.png");
         background-position: center;
         background-size: contain;
     }*/
</style>
<?php
$wspUrl = "//wa.me/54" . preg_replace("#[\s+,\-]#", "", $arr_contacto['telefono']);
?>
<?php if ( !$hidden_header ): ?>
    <div id="page-header">
        <div class="header-bottom">
            <div class="container">
                <div class="header-bottom_left">
                    <a target="_blank" href="<?= $wspUrl ?>" style="font-size:22px;color: #1b9d63"><i class="fab fa-whatsapp"></i>&nbsp;<span><?= $arr_contacto['telefono'] ?></span></a>
                </div>
                <?= $social_links ?>
                <div class="clear"></div>
            </div>
        </div>
        <div class="menu" id="menu">
            <div class="navbar-wrapper">
                <div class="navbar" role="navigation">
                    <div class="container" id="menu-container">
                        <div id="hh-slogan">
                            <img src="static/images/usa-flag.png" alt="usa" style="width:38px;float:left;margin-top:-5px">
                            <?php if ( $nombre = $user->nombre_pila ): ?>
                                <div style="font-size:18px;float:right;color:#fff;text-align:right;">
                                    <?= $nombre ?>&nbsp;&nbsp;
                                    <a href="<?= $site_url ?>/!FrontMain/logout" id="aa-logout" style="color:#fff;"><i class='fa fa-sign-out-alt'></i></a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="navbar-header">
                            <a href="<?= $site_url ?>" class="navbar-brand"><img src="<?= $logo_src ?>" style="width:170px" alt="Logo"/></a>
                            <?php if ( false ): ?>
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                                    <span class="sr-only"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="nav-group">
                            <?php
                            //echo "<a href='{$site_url}/contacto.html'><i class='fa fa-envelope'></i></a>";
                            ?>
                            <!--<li class="active"><a href="< ?= $site_url ?>">Inicio</a></li>-->
                            <a href="<?= $site_url ?>/cart" id="aa-cart"><i class="fa fa-shopping-cart"></i>&nbsp;<sup></sup></a>
                        </div>
                        <span class="navbar-collapse collapse"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="main" id="page-body" style="min-height: 55vh">
    <div class="container">
        <div class="classes_wrapper" id="main-content">
            <?= $_view_content ?>
        </div>
    </div>
</div>
<?php if ( $show_location ) : ?>
    <div style="background: #f8f8f8">
        <div class="container">
            <div class="row" id="map-container">
                <div class="col-md-6" style="">
                    <h3>Sucursales y Horarios</h3>
                    <?php foreach ($sucursales as $sucursal) : ?>
                        <div class="form-group">
                            <i class="fa fa-map-marker-alt"></i>&nbsp;<?= $sucursal['direccion'] ?>
                            <?php foreach ($sucursal['horario'] as $dia => $horario): ?>
                                <div class="hours"><?= $dia . " {$horario}" ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-6">
                    <?= $location ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if ( false ) : ?>
    <div class="footer-top">
        <ul class="twitter_footer">
            <li>
                <i class="twt_icon"> </i>
                <p>aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel <span class="m_6">2 days ago</span></p>
                <div class="clear"></div>
            </li>
        </ul>
    </div>
<?php endif; ?>
<?php if ( !$hidden_footer ): ?>
    <div id="page-footer">
        <div class="footer-bottom" id="footer-bottom">
            <div class="container">
                <a href="<?=$wspUrl?>" target="_blank" style="position: fixed;bottom:6%;left:10px;z-index:99"><img width="55" alt="wsp" src="static/images/wsp-icon.png"></a>
                <div class="row" style="display: flex;justify-content:center;align-items: center;flex-wrap: wrap">
                    <div class="col-md-5">
                        <div class="row logos">
                            <div class="col-md-4"><img src="static/images/ultra-tech.jpg" alt="universal"></div>
                            <div class="col-md-4"><img style="background:#000" src="static/images/logo-idn.png" alt="idn"></div>
                            <div class="col-md-4"><img src="static/images/gold-nutrition-logo.png" alt="gold"></div>
                            <div class="col-md-4"><img src="static/images/hoch-sport.jpg" alt="hoch"></div>
                            <div class="col-md-4"><img src="static/images/xtrenght.png" alt="xtrenght"></div>
                            <div class="col-md-4"><img src="static/images/logo_ena.png" alt="ena sport"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="f-logo">
                            <img src="static/logo-lebron.jpg" width="150" alt="logo footer"/>
                            <img src="static/images/usa-flag.png" width="130" alt="usa-flag"/>
                        </div>
                        <div class="row" style="display: flex;justify-content: center;flex-wrap: wrap">
                            <div class="col-sm-9">
                                <div class="address">
                                    <a href="<?=$wspUrl?>" target="_blank"><i class="fab fa-whatsapp"></i> <span class="m_10"><?= $arr_contacto['telefono'] ?></span></a>
                                </div>
                                <div class="address">
                                    <i class="fa fa-envelope"></i> <span class="m_10"><?= $arr_contacto['email'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h4 class="m_7">Institucional</h4>
                        <ul class="list1">
                            <?php foreach ($institucional as $url => $label): ?>
                                <li><a href="<?= $url ?>"><?= $label ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <div style="color:#fff;font-size:28px">
                            <h5 style="font-size:12px;margin:0">SEGUINOS EN NUESTRAS REDES PARA MAS NOVEDADES</h5>
                            <?php
                            unset($cnf_contacto['contacto']);
                            foreach ($cnf_contacto as $icon => $item):
                                if ( !$item )
                                {
                                    continue;
                                }
                                echo "<a href='{$item}' style='color: #fff;'><i class='fab fa-{$icon}'></i></a>&nbsp;&nbsp;";
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="copy">
                    <p>© 2019 - <?= date('Y') ?></p>
                </div>
                <?= $social_links ?>
                <div class="clear"></div>
            </div>
        </div>
    </div>
<?php endif; ?>
<script type="text/javascript">
    $(".scroll").click(function (event) {
        event.preventDefault();
        $('html,body').animate({
            "scrollTop": $(this.hash).offset().top
        }, 1200);
    });

    function get_count_cart()
    {
        if ( !(cartCount = document.getElementById('aa-cart')) )
        {
            return;
        }
        cartCount.lastElementChild.innerHTML = "<i class='fa fa-spin fa-spinner'></i>";
        fetch("!FrontCart/getItemsCart?q").then(function ($res) {
            $res.text().then(function ($txt) {
                cartCount.lastElementChild.innerHTML = $txt;
                cartCount.setAttribute("title", `Artículos agregados: ${$txt}`);
            })
        });
        document.getElementById('main-content').insertAdjacentHTML("afterbegin", "<h4 id='free-ship'>Con tu compra de $ <?=$montoEnvioGratis?> o más tenés envío Gratis a todo el país!!</h4>")
    }

    get_count_cart();
</script>