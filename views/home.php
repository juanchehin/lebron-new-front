<section id="team" class="section">
    <div class="container">
        <div class="row title text-center">
            <h2 class="margin-top">Campa&ntilde;as</h2>
            <h4 class="light muted"></h4>
        </div>
        <div class="row">
            <?php for ($x = 1; $x < 4; $x++) : ?>
                <div class="col-md-4">
                    <div class="team text-center">
                        <div class="cover" style="background:url('static/img/team/team-cover1.jpg'); background-size:cover;">
                            <div class="overlay text-center">
                                <h3 class="white"></h3>
                                <h5 class="light light-white"></h5>
                            </div>
                        </div>
                        <div class="title">
                            <h4>Campa&ntilde;a <?= $x ?></h4>
                            <h5 class="muted regular">Descripcion</h5>
                        </div>
                        <a href="" class="btn btn-blue-fill">Detalle</a>
                    </div>
                </div>
            <?php endfor; ?>
            <div class="cut cut-bottom"></div>
        </div>
    </div>
</section>
<section id="acceder" class="section">
    <div class="container">
        <?php if ( !$logged_user ) : ?>
            <div class="row title text-center">
                <h2 class="margin-top">Accede usando tus redes sociales</h2>
                <h4 class="light muted">No necesitas completar formularios de registro!</h4>
                <p>&nbsp;</p>
            </div>
            <?= $login_block ?>
        <?php endif; ?>
    </div>
</section>
<section id="proposito" class="section">
    <div class="container">
        <div class="row text-center title">
            <h2 class="margin-top">¿Cu&aacute;l es el prop&oacute;sito?</h2>
            <h4 class="light muted">La intenci&oacute;n de esta p&aacute;gina es...</h4>
        </div>
        <div class="row services">
            <?php //foreach ($campanias as $campania); ?>
            <div class="col-md-4">
                <div class="service">
                    <div class="icon-holder">
                        <img src="<?= $icon ?>" class="icon" alt=""/>
                    </div>
                    <h4 class="heading">Dar a conocer las campa&ntilde;as</h4>
                    <p class="description">Informarte de las distintas campa&ntilde;as de donaci&oacute;n que son impulsadas por organizaciones o particulares con la intenci&oacute;n de ayudar</p>
                </div>
            </div>
            <?php ?>
            <div class="col-md-4">
                <div class="service">
                    <div class="icon-holder">
                        <img src="<?= $icon ?>" alt="" class="icon">
                    </div>
                    <h4 class="heading">Registrar tu donaci&oacute;n</h4>
                    <p class="description">Podr&aacute;s indicar qu&eacute; tipo de ayuda realizar&aacute;s</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service">
                    <div class="icon-holder">
                        <img src="<?= $icon ?>" alt="" class="icon">
                    </div>
                    <h4 class="heading">Seguimiento de tu ayuda</h4>
                    <p class="description">Te permitir&aacute; saber si tu ayuda lleg&oacute; a su destino o en d&oacute;nde se encuentra</p>
                </div>
            </div>
        </div>
    </div>
</section>