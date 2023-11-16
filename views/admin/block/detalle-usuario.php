<div class="row">
    <div id="detalle-usuario" class="col-md-8">
        <?php $es_usuario = ($usuario->user_type == Usuario::USR_USUARIO); ?>
        <?php if ( $usuario->usuarioEsComercio() ) : ?>
            <div class="row">
                <div class="col-md-6 form-group">
                    <p>cuit</p>
                    <?= $usuario->cuit ?>
                </div>
                <div class="col-md-6 form-group">
                    <p>raz&oacute;n social</p>
                    <?= $usuario->razon_social ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <p>Actividad</p>
                    <?= $usuario->hasActividad->actividad ?>
                </div>
                <div class="col-md-6 form-group">
                    <p>Condici&oacute;n iva</p>
                    <?= $usuario->hasCondicionIva->descripcion ?>
                </div>
            </div>
            <?php if ( $usuario->ubicacion ) : ?>
                <div class="row">
                    <div class="form-group col-md-12">
                        <p>Direcci&oacute;n</p>
                        <?= $usuario->ubicacion ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6 form-group">
                    <p>Nombre de fantas&iacute;a</p>
                    <?= $usuario->nombre_fantasia ?>
                </div>
                <div class="form-group col-md-6">
                    <p>Sitio web</p>
                    <?= $usuario->sitio_web ?>
                </div>
            </div>
            <div class="row">
                <h3 class="col-md-12">Datos de contacto</h3>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6 form-group">
                <p>DNI</p>
                <?= $usuario->dni ?>
            </div>
            <div class="col-md-6 form-group">
                <p>Nombre y apellido</p>
                <?= $usuario->nombre_apellido ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <p>Correo electr&oacute;nico</p>
                <?= $usuario->email ?>
            </div>
            <div class="col-md-6 form-group">
                <p>Nombre de usuario</p>
                <i><?= $usuario->usuario ?></i>
            </div>
        </div>
        <?php if ( $es_usuario ) : ?>
            <div class="row">
                <div class="col-md-6 form-group">
                    <p>Fecha de nacimiento</p>
                    <?= $usuario->fecha_nacimiento ?>
                </div>
                <div class="col-md-6 form-group">
                    <p>Sexo</p>
                    <?= $usuario->genero ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6 form-group">
                <p>Tel&eacute;fono</p>
                <?= $usuario->telefono ?>
            </div>
            <div class="col-md-6 form-group">
                <p>Celular</p>
                <?= $usuario->celular ?>
            </div>
        </div>
        <?php if ( $usuario->ubicacion && $es_usuario ) : ?>
            <div class="row">
                <div class="form-group col-md-12">
                    <p>Direcci&oacute;n</p>
                    <?= $usuario->ubicacion ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <p>Fecha de registro</p>
                <?= $usuario->fecha_registro ?>
            </div>
            <div class="col-md-6 form-group">
                <p>&Uacute;ltimo acceso</p>
                <?= $usuario->ultimo_acceso ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <p>cuenta verificada</p>
                <?= ($usuario->validado ? "Si" : "No") ?>
            </div>
            <div class="col-md-6 form-group">
                <p>Cuenta activa</p>
                <?= ($usuario->activo ? "Si" : "No") ?>
            </div>
        </div>
        <?php if ( DEVELOPMENT && $padron = $usuario->hasPadron ) : ?>
            <div class="row">
                <h4 class="col-md-12">Datos de padr&oacute;n</h4>
                <div class="form-group col-md-6">
                    <p>Nombre</p>
                    <?= ($padron->detalle ? $padron->detalle : $padron->nombre) ?>
                </div>
                <div class="form-group col-md-6">
                    <p>Direccion</p>
                    <?= $padron->direccion ?>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <p>Localidad</p>
                    <?= $padron->localidad ?>
                </div>
                <div class="col-md-6 form-group">
                    <p>C&oacute;digo postal</p>
                    <?= $padron->cp ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ( DEVELOPMENT && $patente = $usuario->hasPatente[0] ): ?>
            <div class="row">
                <div class="col-md-4">
                    <p>n&uacute;mero de patente</p>
                    <?= $patente->nro_pat ?>
                </div>
                <div class="col-md-4">
                    <p>Nombre</p>
                    <?= $patente->nom_pat ?>
                </div>
                <div class="col-md-4">
                    <p>Direcci&oacute;n</p>
                    <?= $patente->dom_pat ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <p>Detalle veh&iacute;culo</p>
                    <?= $patente->detalle_vehiculo ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <a href="<?= CP_ADMIN ?>/usuarios" class="btn btn-default">Volver</a>
        </div>
    </div>
</div>