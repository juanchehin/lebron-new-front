<div class="row">
    <?php
    //ini_set("display_errors", "On");
    $articulo_ref = "{$site_url}/articulo/{$item->id_producto}";
    $back_url = preg_match("#{$site_url}#", $_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $site_url;
    ?>
    <div class="col-md-9" style="background: #fff;">
        <div class="form-group social_blog">
            <a href="<?= $back_url ?>" class="aa-button" style="float: left">< Inicio</a>
            <a class="aa-share" href="<?= "http://facebook.com/sharer/sharer.php?u={$articulo_ref}&amp;t=" . ($articulo = $item->nombre); ?>"><i class="fab fa-facebook-square"></i></a>
            <a class="aa-share" href="<?= "http://twitter.com/home?share?text={$articulo}&amp;url={$articulo_ref}" ?>"><i class="fab fa-twitter-square"></i></a>
            <?php if ( false ): ?>
                <a href="#"><i class="fab fa-instagram"></i></a>
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col-md-6" style="padding-bottom:12px;">
                <h2 class="title"><?= $articulo ?></h2>
                <h4 class="m_17"><?= $detalle_articulo ?></h4>
                <hr>
                <?= $cart ?>
            </div>
            <?php if ( $imagenes ): ?>
                <div class="col-md-6" style="display: inline-block">
                    <div class="sp-wrap" id="articulo-images">
                        <?php foreach ($imagenes as $index => $imagen) : ?>
                            <a href="<?= $imagen ?>"><img src="<?= $imagen ?>" alt="<?= $index ?>"/></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-12" style="padding: 20px;line-height:1.6em">
                <?= ucfirst($item->texto) ?>
                <?php AdSense::setAdsArticle("8326415101") ?>
            </div>
            <?php if ( false ): ?>
                <div class="col-md-12 form-group">
                    <div class="fb-comments" data-href="<?= $articulo_ref ?>" data-width="100%" data-numposts="6"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Related -->
    <div class="col-md-3">
        <?php if ( count($related) > 1 ): ?>
            <h3 style="color:#000001;text-align: center;margin:0">Quiz&aacute;s te pueda interesar</h3>
            <?php foreach ($related as $res): if ( $item->id_producto == $res->id_producto ) continue; ?>
                <div class="form-group item-box">
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
            <?php AdSense::setAdsArticle("8292069164"); ?>
        </div>
    </div>
</div>
<script src="static/public/js/JsArticulo.js?ver=<?= time() ?>" type="text/javascript"></script>
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