<div class="panel panel-default">
    <div class="panel-heading"><?= $titulo ?></div>
    <div class="panel-body">
        <form action="!AdminUsuarios/savePassword" id="frm-cambio-pass" autocomplete="off">
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="contrasena">Contrase&ntilde;a</label><span id="result"></span>
                    <input type="password" minlength="<?= Usuario::PASS_LENGTH ?>" name="contrasena" id="contrasena" class="strength form-control" required/>
                    <a href="javascript:void(0)" id="op-show-hide" class="small">Mostrar</a>
                </div>
                <div class="form-group col-md-6">
                    <label for="repetir_contrasena">Confirmar contrase&ntilde;a</label>
                    <input type="password" id="repetir_contrasena" name="repetir_contrasena" class="form-control" required/>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group text-right">
                <?php if ( $id_usuario ) : ?>
                    <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">
                <?php endif; ?>
                <button type="submit" class="btn btn-success" id="btn-save-pass">Aceptar</button>
                <a href="javascript:void(0)" id="a-close" data-dismiss="modal" class="btn btn-default">Cerrar</a>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('#frm-cambio-pass').submit(function (e) {
        e.preventDefault();
        submit_form(this, function () {
            $('#a-close').trigger('click');
        });
    });

    $.toggleShowPassword({'field': '#contrasena', 'control': '#op-show-hide'});
</script>