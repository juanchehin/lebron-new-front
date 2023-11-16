<section id="aa-client-brand">

    <div class="aa-client-brand-area">

        <div class="aa-client-brand-slider" data-noplay="1">

            <?php foreach ($marcas as $marca): ?>

                <div class="col-md-2 text-center">

                    <a href="javascript:void(0)" onclick="set_tag(this)" id="marca-<?= $marca->id_item ?>">

                        <div class="brand-image" style="background-image:url(<?= $marca->src_image ?>)">

                            <h4 style="text-align: center;<?= preg_match("#default#", $marca->src_image) ? '' : 'display:none' ?>"><?= $marca->titulo ?></h4>

                        </div>

                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>