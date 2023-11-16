<?php if ( $populares ): ?>
    <section id="aa-promo" class="aa-slider-section">
        <div class="aa-client-brand-area">
            <h2><?= $titulo ?></h2>
            <div class="aa-client-brand-slider" data-slides="4" data-infinite="1">
                <?php foreach ($populares as $id => $popular): ?>
                    <div class="col-md-3 text-center" rel="slider">
                        <?= FrontArticulo::drawArticulo($popular) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>