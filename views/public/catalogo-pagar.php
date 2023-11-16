<div class="col-lg-12">
    <?php if ( $html_alert ): ?>
        <h2><?= $response_title ?></h2>
        <?= $html_alert ?>
        <div class="form-group">
            <a href="<?= $site_url ?>/payment" class="btn btn-default">Volver</a>
        </div>
    <?php else : ?>
        <form action="!FrontPayment/pagar" id="frm-pagar">
            <div class="form-group text-right">
                <button class="btn btn-success" type="submit" style="font-size:17px">Finalizar Compra!</button>
            </div>
        </form>
        <script>
            document.getElementById('frm-pagar').onsubmit = function (event) {
                event.preventDefault();
                let this_form = this;
                jconfirm(function ($yes) {
                    if ( $yes )
                    {
                        submit_form(this_form);
                    }
                }, "Serás redireccionado a MercadoPago para completar tu compra. ¿Continuar?");
            };
        </script>
    <?php endif; ?>
</div>
