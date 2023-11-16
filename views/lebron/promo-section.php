<?php if ( $promociones ): ?>

    <section id="aa-promo" class="aa-slider-section">

        <div class="aa-client-brand-area">

            <h2><?= $titulo ?></h2>

            <div class="aa-client-brand-slider">

                <?php foreach ($promociones as $promo): ?>

                    <div class="col-md-3 text-center" rel="slider">

                        <h4 style='position:absolute;z-index:5;right:25px;background:#fb0200;color:#fff;padding:3px 5px'>

                            <?php 
                            
                                if ( $promo->id_producto == 4669 )
                                {
                                    $precio = '19900';
                                }else
                                {
                                    if ( $promo->id_producto == 4678 )
                                    {
                                        $precio = '9900';
                                    }else{
                                        $precio = $promo->ecommerce_precio;
                                    }
                                }
                                
                            ?>

                            $ <?=HFunctions::formatPrice($precio)?>

                        </h4>

                        <a href="<?= "./articulo/{$promo->id_producto}" ?>">

                            <img src="<?= $promo->imagenes(true) ?>" style="width:100%" alt="<?= $promo->id_producto ?>"/>

                            <h4 class="hh-title"><?= $promo->nombre ?></h4>

                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

    </section>

<?php endif; ?>