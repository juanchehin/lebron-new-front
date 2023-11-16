<?php if ( ($item->publicado) && !($item->borrado) && !($item->oculto)): ?>
<div class="col-md-9">

    <div class="aa-product-details-area">

        <div class="aa-product-details-content">

            <div class="row">

                <div class="form-group social_blog">

                    <a href="<?= $back_url ?>" class="aa-button" style="float: left">< Inicio</a>

                    <h4>*Las promociones s&oacute;lo son v&aacute;lidas para compras por la web</h4>

                    <a class="aa-share" href="<?= "http://facebook.com/sharer/sharer.php?u={$articulo_ref}&amp;t=" . ($articulo = $item->nombre); ?>"><i class="fab fa-facebook-square"></i></a>

                    <a class="aa-share" href="<?= "http://twitter.com/home?share?text={$articulo}&amp;url={$articulo_ref}" ?>"><i class="fab fa-twitter-square"></i></a>

                    <?php if ( false ): ?>

                        <a href="#"><i class="fab fa-instagram"></i></a>

                    <?php endif; ?>

                </div>

                <div class="col-md-6">

                    <div class="aa-product-view-content">

                        <h3><?= $articulo ?></h3>

                        <div class="form-group" style="color: #0b0b0b">

                            <h4><?= $detalle_articulo ?></h4>

                            <hr>

                        </div>

                        <div class="aa-price-block">

                            <?= $cart ?>

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="aa-product-view-slider">

                        <div class="sp-wrap" id="articulo-images">

                            <?php foreach ($imagenes as $index => $imagen) : ?>

                                <a href="<?= $imagen ?>"><img src="<?= $imagen ?>" alt="<?= $index ?>"/></a>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="aa-product-details-bottom">

            <hr>

            <?= ucfirst($item->texto) ?>

        </div>

    </div>

    <?php //AdSense::setAdsArticle("8326415101") ?>

</div>

<div class="col-md-3 aa-product-catg" style="padding:0 20px">

    <?php if ( count($related) > 1 ): ?>

        <h3 style="color:#000001;text-align: center;margin:0">Quiz&aacute;s te pueda interesar</h3>

        <?php foreach ($related as $res): if ( $item->id_producto == $res->id_producto ) continue; ?>

            <div class="col-md-12 product-item">

                <a href="<?= ($href = $site_url . "/articulo/{$res->id_producto}") ?>" class="class-image">

                    <img src="<?= $res->imagenes(true) ?>" width="100%" alt="<?= $res->id_producto ?>"/>

                </a>

                <h4 class="title"><?= $res->nombre ?></h4>

                <p><?= $res->marca ?></p>

                <div class="buttons_class">

                    <h4 style="display: inline-block;float: left;margin-top:4px">$ <?= HFunctions::formatPrice($res->ecommerce_precio) ?></h4>

                    <a href="<?= $href ?>">Ver</a>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="text-center">

            <img src="static/images/jay.jpg" width="100%" alt="banner"/>

        </div>

    <?php endif; ?>

    <div class="form-group">

        <?php //AdSense::setAdsArticle("8292069164"); ?>

    </div>

</div>

<?php else: ?>

    <h3>Articulo no disponible</h3>

<?php endif; ?>

<style>

    .buttons_class {

        text-align: right;

    }

</style>

<script>

    $('#articulo-images').smoothproducts();

    if ( (aaVer = document.getElementById('aa-ver')) )

    {

        aaVer.remove()

    }



    Array.from(document.getElementsByClassName('aa-share')).forEach(function ($aa) {

        $aa.onclick = function (evt) {

            evt.preventDefault();

            if ( !(url_ref = this.href) )

            {

                return;

            }

            window.open(url_ref, 'fbShareWindow', 'height=450, width=550, top=' + (window.innerHeight / 2 - 275) + ', left=' + (window.outerWidth / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');

        };

    });

</script>