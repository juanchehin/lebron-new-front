<div class="row">
    <div class="col-md-7">
        <h2>Contáctanos</h2>
        <p class="m_8">¡SOMOS MAYORISTAS!<br/>Complet&aacute; el siguiente formulario para contactarte con nosotros. Te responderemos a la brevedad</p>
        <form id="contact-form" action="!FrontInicio/contacto" autocomplete="off">
            <div class="row">
                <div class="col-md-12 form-group">
                    <input type="text" id="nombre" value="<?= $user->nombre_apellido ?>" name="nombre" maxlength="40" required class="form-control" placeholder="Nombre">
                </div>
                <div class="col-md-6 form-group">
                    <input type="email" name="email" value="<?= $user->email ?>" class="form-control" placeholder="Ingres&aacute; tu E-mail" required>
                </div>
                <div class="col-md-6 form-group">
                    <input type="tel" class="form-control" name="telefono" placeholder="Teléfono (Whatsapp)">
                </div>
                <div class="col-md-12 form-group">
                    <textarea name="mensaje" class="form-control" rows="4" placeholder="Mensaje" required></textarea>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-default btn">Enviar <i class="fa fa-envelope"></i></button>
            </div>
        </form>
    </div>
    <div class="col-md-5">
        <h2>Acerca de</h2>
        <p class="m_8">
            LeBron es el único lugar en donde vas a encontrar productos nacionales e internacionales
            de máxima calidad al mejor precio. Además te brindamos un servicio de asesoramiento y
            dietas nutricionales para optimizar tu rendimiento.
        </p>
    </div>
</div>
<script>
    document.getElementById('contact-form').onsubmit = function (evt) {
        evt.preventDefault();
        let thisForm = this;
        submit_form(this, function () {
            thisForm.reset();
        });
    };
</script>