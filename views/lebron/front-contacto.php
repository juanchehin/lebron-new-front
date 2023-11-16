<div class="text-center" hidden>
    <h2>Conocenos</h2>
</div>
<div class="col-md-6 form-group">
    <div class="aa-contact-address-left">
        <h2>Contáctanos</h2>
        <p class="m_8">¡SOMOS MAYORISTAS!<br/>Complet&aacute; el siguiente formulario para contactarte con nosotros. Te responderemos a la brevedad</p>
        <?= $contactForm ?>
    </div>
    <?= $ubicacion ?>
</div>
<div class="col-md-6 form-group">
    <h2>Sobre Nosotros</h2>
    <p>
        LeBron es el único lugar en donde vas a encontrar productos nacionales e internacionales
        de máxima calidad al mejor precio. Además te brindamos un servicio de asesoramiento y
        dietas nutricionales para optimizar tu rendimiento.
    </p>
    <div class="row" style="display:flex;justify-content: center;flex-wrap: wrap">
        <?php foreach ($videos as $video): ?>
            <div class="col-md-6">
                <iframe src="//player.vimeo.com/video/<?= preg_replace("#[^\d+]#", "", $video['uri']) ?>?title=0&byline=0&portrait=0" width="100%" height="280" frameborder="0" allow=""></iframe>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script>
    document.getElementById('dv-direcciones').remove();
</script>