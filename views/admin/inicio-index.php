<style type="text/css">
    .modulo-opciones li {
        padding: 12px 10px;
        list-style: none;
        border-bottom: 1px solid #ccc;
        text-transform: uppercase;
        vertical-align: middle;
    }

    .modulo-opciones li:nth-child(odd) {
        background: #eee;
    }

    .modulo-opciones li:hover {
        background: #f9f9f9;
    }

    .modulo-opciones li a {
        font-size: 21px;
        color: #222;
        letter-spacing: -1px;
        vertical-align: middle;
    }

    .modulo-opciones li .text {
        width: 85%;
        float: left;
        color: #222;
    }

    .modulo-opciones li h3 {
        padding: 5px;
    }

    .modulo-opciones li a .fa {
        min-width: 45px;
        text-align: center;
        font-size: 34px;
        padding: 3px 0;
        position: absolute;
        margin-top: -4px;
        right: 5px;
        color: #122b40;
    }
</style>
<div class="row">
    <?php //array_shift($menu_parent);?>
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="form-group row">
            <?php if ( $ses_user->es_admin ) : ?>
                <?= $select_sucursales ?>
            <?php endif; ?>
        </div>
        <ul class="list-unstyled modulo-opciones">
            <?php foreach ($menu_parent as $row) : ?>
                <li class="row">
                    <a href="<?= $row['link']?>"><span class="text"><?= $row['label'] ?></span><i class="<?= $row['icono'] ?>"></i></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<script type="text/javascript">
    <?php if($ses_user->es_admin) : echo "\n"; ?>
    $('#s-sucursal').on('change', function () {
        let values = {
            'id_sucursal': this.value,
            'pathname': window.location.pathname
        };

        $.post("!AdminInicio/seleccionarSucursal", values, function (res) {
            if ( res.reload === 0 )
            {
                location.reload();
            }
        }, 'json');
    });
    <?php endif; ?>
</script>