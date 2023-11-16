<div class="section contact">
    <div class="container">
            <h2 class="h3-w3l">Contacto</h2>
        <div class="row">
            <div class="col-md-5">
                <p>¡SOMOS MAYORISTAS!</p>
                <p>Si desea realizar alguna consulta, por favor complete el siguiente formulario indicando el motivo de la consulta y responderemos a sus dudas lo antes possible.</p>
            </div>
            <div class="col-md-7" id="contact-form-container">
                <form id="contact-form" action="!<?= CURRENT_CLASS ?>/contacto" onsubmit="return submit_form(this);">
                    <div class="form-group">
                        <label for="nombre">Nombre <span class="required"></span></label>
                        <?php if ( $logged_user ) : ?>
                            <input type="hidden" name="id_usuario" value="<?= $logged_user->id_usuario ?>"/>
                            <div class="form-control"><?= $logged_user->nombre_apellido ?></div>
                        <?php else: ?>
                            <input type="text" class="alphanumeric form-control" name="nombre" id="nombre" required autofocus/>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail <span class="required"></span></label>
                        <?php if ( $logged_user ) : ?>
                            <div class="form-control"><?= $logged_user->email ?></div>
                        <?php else: ?>
                            <input type="email" class="form-control" name="email" id="email" required/>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="asunto">Asunto <span class="required"></span></label>
                        <!--<input type="text" name="asunto" class="form-control alphanumeric" id="asunto"/>-->
                        <input type="text" name="asunto" id="asunto" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="mensaje">Mensaje <span class="required"></span></label>
                        <textarea name="mensaje" class="form-control" rows="4" id="mensaje"></textarea>
                        <span class="small"><i class="fa fa-info-circle"></i>&nbsp;Por favor intente ser lo m&aacute;s espec&iacute;fico posible.</span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                        <?php if ( $logged_user && false ) : ?>
                            <a href="gestion" class="btn btn-default">Volver</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>