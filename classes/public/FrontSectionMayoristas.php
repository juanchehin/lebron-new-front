<?php



class FrontSectionMayoristas

{

    public static function blockSection($collection, $titulo, $opts = array())

    {

        $dataAttr = array();

        if ( is_array($opts) )

        {

            foreach ($opts as $k => $v)

            {

                $dataAttr[] = "data-{$k}='{$v}'";

            }

        }

        ob_start();

        if ( $collection ): ?>

            <section id="aa-promo" class="aa-slider-section">

                <div class="aa-client-brand-area">

                    <h2><?= $titulo ?></h2>

                    <div class="aa-client-brand-slider" <?=implode(" ", $dataAttr)?>>

                        <?php foreach ($collection as $id => $popular): ?>

                            <div class="col-md-3 text-center" rel="slider">

                                <?= FrontArticuloMayoristas::drawArticulo($popular) ?>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            </section>

        <?php

        endif;

        return ob_get_clean();

    }

}