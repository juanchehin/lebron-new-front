<div class='container'>
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6" style="margin:14% 0">
            <h1 style="padding-bottom:6%;text-align: center;font-style: italic"><?= SITE_NAME ?></h1>
            <div class="panel panel-default">
                <div class="panel-heading text-center" style="font-style:italic;font-size: 20px">Acceso</div>
                <div class="panel-body">
                    <form action='!<?= CURRENT_CLASS ?>/ingresar' id='loginForm' autocomplete='off' onsubmit="return submit_form(this);">
                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <input type='text' class='form-control' name='username' placeholder="Usuario o Correo electr&oacute;nico" value="<?= $usuario ?>" autofocus/>
                        </div>
                        <?php //if ( !DEVELOPMENT ): ?>
                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            <input type='password' class='form-control' value="<?= $contrasena ?>" name='password' placeholder="Contrase&ntilde;a"/>
                        </div>
                        <?php //endif; ?>
                        <footer class="form-group text-right">
                            <button class="btn btn-primary" type='submit' id="submit">Acceder</button>
                        </footer>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('body').addClass("bootstrap-admin-without-padding");
</script>