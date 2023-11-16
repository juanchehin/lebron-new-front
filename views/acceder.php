<section class="section">
    <div class="container">
        <?php if ( isset($_GET['timeout']) ) : ?>
            <div class="alert alert-info text-center">
                <p>Su sesi&oacute;n ha caducado.</p>
            </div>
        <?php endif ?>
        <div class="panel panel-default" id="login-container">
            <div class="panel-heading"><h4>Acceso</h4></div>
            <div class="panel-body">
                <form id="frm-acceso" action="!<?= CURRENT_CLASS ?>/authUser">
                    <div class="form-group">
                        <label for="_usuario">Usuario, correo electr&oacute;nico o celular</label>
                        <input type="text" name="usuario" id="_usuario" class="form-control" required autofocus/>
                    </div>
                    <div class="form-group">
                        <?php if ( !DEVELOPMENT ) $attr = array('required' => true) ?>
                        <?= HForm::inputPassword('contrasena', 'Contrase&ntilde;a', null, $attr); ?>
                        <br/>
                        <a href="javascript:void(0)" onclick="$('#restablecer-pass-modal').modal()">No recuerdo mi contrase&ntilde;a</a>
                    </div>
                    <?php if ( false ) : ?>
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
                        </div>
                    <?php endif; ?>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<div id="restablecer-pass-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Restablecer contrase&ntilde;a</h4>
            </div>
            <form action="!<?= CURRENT_CLASS ?>/restablecerContrasena" onsubmit="submit_form(this);return false;">
                <div class="modal-body">
                    <p>Ingrese su correo electr&oacute;nico a continuaci&oacute;n y le enviaremos instrucciones para restablecer su contrase&ntilde;a</p>
                    <div class="form-group">
                        <input type="email" id="correo" name="email" class="form-control" placeholder="Correo electr&oacute;nico" required/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>