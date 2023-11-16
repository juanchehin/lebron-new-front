<div id="estadisticas" class="text-center">
    <div class="row">
        <div class="col-md-3">
            <div class="alert alert-warning">
                <div class="panel-heading">Total de usuarios registrados</div>
                <h3 id="count-total-usuarios"><?= $total_usuarios ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="alert alert-info">
                <div class="panel-heading">Usuarios registrados en el d&iacute;a</div>
                <h3 id="count-total-hoy"><?= $registrados_hoy ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="alert alert-warning">
                <div class="panel-heading">Usuarios online</div>
                <h3 id="count-total-online"><?= $usuarios_online ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="alert alert-info">
                <div class="panel-heading">Usuarios conectados ayer</div>
                <h3 id="count-total-ayer"><?= $conectados_ayer ?></h3>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="alert alert-warning">
                <div class="panel-heading">Saldo Virtual</div>
                <h3 id="saldo-virtual"><?= $saldo_virtual ?></h3>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function get_estadisticas()
    {
        $.getJSON('!<?=CURRENT_CLASS?>/getEstadisticas', function (html)
        {
            $('#count-total-usuarios').html(html.total_usuarios);
            $('#count-total-hoy').html(html.registrados_hoy);
            $('#count-total-online').html(html.usuarios_online);
            $('#count-total-ayer').html(html.conectados_ayer);
            $('#saldo-virtual').html(html.saldo_virtual);
        });
    }
    setInterval(get_estadisticas, 60000);
</script>
