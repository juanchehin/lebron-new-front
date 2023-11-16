<div class="row">
    <div class="col-md-<?= ($adm_form ? 1 : 3) ?>"></div>
    <div class="col-md-6">
        <?= $user_form ?>
    </div>
    <?php if ( $adm_form ) : ?>
        <div class="col-md-4" id="permisos"></div>
        <div class="col-md-1"></div>
    <?php endif; ?>
</div>
<script type="text/javascript">
    const select_rol = $('#tipo_usuario');
    select_rol.change(function () {
        let values = {
            "rol": this.value,
            "actual": <?=$permisos ?: "{}\n"?>
        };
        $.post('!AdminUsuarios/permisosForm', values, function (res) {
            $('#permisos').html(res);
        });
    });
    document.getElementById("genre-group").innerHTML = "<?=$selectLocal?>";
    document.getElementById("row-4").remove();
    <?php if($default_rol) : echo "\n";?>
    select_rol.val('<?=$default_rol?>');
    <?php endif; ?>
    select_rol.trigger('change');
    <?php if(!$cambia_clave) : ?>
    $('#a-cambiar-pwd').html("");
    <?php endif; ?>
</script>
