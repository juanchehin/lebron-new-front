<section id="aa-slider" style="margin-bottom: 20px">

    <div class="aa-slider-area">

        <div id="sequence" class="seq">

            <div class="seq-screen">

                <ul class="seq-canvas">

                    <?php for ($i = 1; $i < 3; $i++): ?>

                        <!-- single slide item -->

                        <li>

                            <div class="seq-model">
                                <img data-seq src="static/images/slider-<?= $i ?>.gif" alt="<?= $i ?>"/>
                            </div>

                            <div class="seq-title">

                                <?php if ( false ): ?>

                                    <span data-seq>Slider <?= $i ?></span>

                                    <h2 data-seq>Titulo <?= $i ?></h2>

                                    <p data-seq>Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>

                                <?php endif; ?>

                                <p></p>

                                <a data-seq href="./franquicia.html" class="aa-shop-now-btn aa-secondary-btn">M&aacute;s Informaci&oacute;n</a>

                            </div>

                        </li>

                    <?php endfor; ?>

                     <li>

                            <div class="seq-model">
                                <img data-seq src="static/images/slider-4.jpg" alt="slider-4"/>
                            </div>

                            <div class="seq-title">
                                
                                <a data-seq href="./franquicia.html" class="aa-shop-now-btn aa-secondary-btn">M&aacute;s Informaci&oacute;n</a>

                            </div>

                        </li>

                    <!-- single slide item -->



                </ul>

            </div>

            <!-- slider navigation btn -->

            <fieldset class="seq-nav" aria-controls="sequence" aria-label="Slider buttons">

                <a type="button" class="seq-prev" aria-label="Previous"><span class="fa fa-angle-left"></span></a>

                <a type="button" class="seq-next" aria-label="Next"><span class="fa fa-angle-right"></span></a>

            </fieldset>

        </div>

    </div>

</section>

<link href="static/public/tpl/css/sequence-theme.css" rel="stylesheet" type="text/css"/>

<script src="static/public/tpl/js/sequence.js"></script>

<script>

    var sequenceElement = document.getElementById("sequence");



    var options = {

        animateCanvas: false,

        phaseThreshold: false,

        preloader: true,

        reverseWhenNavigatingBackwards: true

    };



    var mySequence = sequence(sequenceElement, options);

</script>