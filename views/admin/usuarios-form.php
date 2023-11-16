<div class="row dt_row" id="row_<?= $usuario->id_usuario ?>">
    <div class="col-md-4">&nbsp;</div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="!AdminUsuarios/guardarUsuario" onsubmit="return submit_form(this)" autocomplete="off">
                    <input type="hidden" name="tipo_usuario" value="<?= $usuario->tipo_usuario ?>"/>
                    <input type="hidden" name="id_usuario" value="<?= $usuario->id_usuario ?>"/>
                    <div class="">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="<?= $usuario->nombre ?>">
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" name="apellido" id="apellido" class="form-control" value="<?= $usuario->apellido ?>">
                        </div>
                        <div class="form-group">
                            <label for="username">Usuario</label>
                            <?php if ( $usuario->usuario ) : ?>
                                <div class="form-control"><?= $usuario->usuario ?></div>
                            <?php else : ?>
                                <input type="text" name="username" id="username" class="form-control" value="<?= $usuario->usuario ?>">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo electr&oacute;nico</label>
                            <?php if ( $usuario->correo && false ) : ?>
                                <div class="form-control"><?= $usuario->correo ?></div>
                            <?php else : ?>
                                <input type="text" name="correo" id="correo" class="form-control" value="<?= $usuario->correo ?>">
                            <?php endif; ?>
                        </div>
                        <div class="form-group text-center">
                            <a href="javascript:void(0)" id="a-cambiar-pwd"><i class="fa fa-key"></i> Modificar Contrase&ntilde;a</a>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button class="btn btn-primary" type="submit">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    <?php if($usuario && $puede_editar) : ?>
    $('#a-cambiar-pwd').on('click', function (e) {
        e.preventDefault();
        var data = {};
        data.id_usuario = '<?=$usuario->id_usuario?>';
        get_modal_form(data, "formCambioPass");
    });
    <?php endif; ?>
</script>