<div class="form-group">
    <h1 class="m_11"><?= $response_title ?: "Tu Compra" ?></h1>
</div>
<?php if ( $html_alert ): ?>
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6 text-center">
            <div class="panel panel-<?= $css_alert ?>">
                <div class="panel-body">
                    <?= $html_alert ?>
                    <div class="form-group">
                        <a href="<?= $site_url ?>/cart" class="btn btn-default">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else : ?>
    <?php
    $frmCalculoEnvio = "<div class='col-md-12 form-group text-left'>";
    $frmCalculoEnvio .= "<label for='cp'>Calcular Env&iacute;o (Estimado)</label>";
    $frmCalculoEnvio .= "<div class='input-group'>";
    $frmCalculoEnvio .= "<input type='tel' maxlength='5' placeholder='Código Postal' name='cp' id='cp' class='form-control' value='{$user->array_direccion['cp']}' required>";
    $frmCalculoEnvio .= "<div class='input-group-addon' style='padding:0 8px;'>";
    $frmCalculoEnvio .= "<button class='btn btn-primary' type='submit' style='padding:4px 8px'>OK</button>";
    $frmCalculoEnvio .= "</div>";
    $frmCalculoEnvio .= "<input type='hidden' name='precio' id='hdn-total'>";
    $frmCalculoEnvio .= "</div>";
    $frmCalculoEnvio .= "<h3 id='hh-costo-envio' style='margin:6px 0;font-style:italic;text-align: center'>&nbsp;</h3>";
    $frmCalculoEnvio .= "</div>";
    ?>
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-primary">
                <div class="panel-body text-center">
                    <form id="frm-envio" action="!FrontPayment/calcularEnvio" autocomplete="off">

                    </form>
                    <h5 style="margin:0">Total Compra</h5>
                    <h2 style="margin-top:0">$ <span id="spn-total">0.00</span></h2>
                    <form action="!FrontPayment/pagar?pay" id="frm-pagar">
                        <div class="form-group">
                            <button class="btn btn-success" type="submit" id="btn-pagar" style="font-size:17px">Finalizar Compra!</button>
                        </div>
                        <input type="hidden" id="pay" name="pay">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-9 form-group" id="cart-group">
            <?= $cart_table ?>
        </div>
    </div>
    <script>
        const botonPagar = document.getElementById('btn-pagar');
        var total_compra = 0;
        document.getElementById('records_count').remove();
        document.getElementById('filter-group').remove();

        function borrar($id)
        {
            jconfirm(function ($true) {
                if ( !$true )
                {
                    return;
                }
                get_items();
                fetch("!FrontCart/quitar?id=" + $id).then(function () {
                    get_items();
                    get_count_cart();
                    //document.getElementById($id).remove();
                });
            }, "Eliminar item de la compra. ¿Continuar?")
        }

        function get_items()
        {
            const cartGroup = document.getElementById('cart-group');
            const tableBody = document.getElementById('table-body');
            const totalLabel = document.getElementById('spn-total');
            const numCols = 5;
            let $spin = document.createElement("div");
            //$spin.style["background"] = "#fff";
            //$spin.style["opacity"] = ".5";
            $spin.id = "table-spin";
            $spin.style["position"] = "absolute";
            $spin.style["z-index"] = "10";
            $spin.style["left"] = "0";
            $spin.style["right"] = "0";
            $spin.style["display"] = "flex";
            $spin.style["justify-content"] = "center";
            $spin.style["align-items"] = "center";
            $spin.style["height"] = (cartGroup.offsetHeight + 5) + "px";
            $spin.innerHTML = "<h4 style='padding-top:20px;font-style:italic'>Cargando...</h4>";
            cartGroup.prepend($spin);
            fetch(`!${document.getElementsByClassName('data-table')[0].getAttribute('data-url')}`).then(function (rs) {
                rs.text().then(function (rows) {
                    $spin.remove();
                    tableBody.innerHTML = rows;
                    if ( !tableBody.childNodes.length )
                    {
                        tableBody.innerHTML = "<tr class='not'><td colspan='" + numCols + "' class='text-center'>No se encontraron registros</td></tr>";
                        botonPagar.setAttribute("disabled", true);
                    }
                    else
                    {
                        tableBody.querySelectorAll('tr td').forEach(function (td) {
                            if ( (cell = (td.cellIndex + 1)) < numCols )
                            {
                                td.insertAdjacentHTML("afterbegin", `<h5 class='hlabel'>${document.getElementById('column-' + cell).innerText}</h5>`)
                            }
                        });
                    }
                    let dimensiones = "";
                    total_compra = 0;
                    if ( (relTr = document.querySelector('tr[rel^="tr-"]')) )
                    {
                        trTotal = relTr.getAttribute("rel").split("&");
                        dimensiones = trTotal[1];
                        total_compra = parseFloat(trTotal[0].replace(/[^\d+]/g, ""));
                        botonPagar.removeAttribute("disabled");
                    }
                    totalLabel.innerHTML = total_compra.toFixed(2);
                    frmEnvio = document.getElementById('frm-envio');
                    frmEnvio.innerHTML = "<h2 class='text-center'><i class='fa fa-spin fa-spinner'></i></h2>";
                    if ( !dimensiones )
                    {
                        frmEnvio.innerHTML = "";
                        return;
                    }
                    //**
                    if ( total_compra >= parseFloat(<?=$montoEnvioGratis?>) )
                    {
                        frmEnvio.innerHTML = "<img src='static/images/envio-gratis.png' width='180' style='max-width:100%' alt='envio-gratis'><p></p>";
                    }
                    else
                    {
                        frmEnvio.innerHTML = `<?=$frmCalculoEnvio?>`;
                        frmEnvio.insertAdjacentHTML("beforeend", `<input type='hidden' name="dimension" value="${dimensiones}">`);
                        document.getElementById('pay').value = dimensiones;
                        document.getElementById('hdn-total').value = total_compra;
                        frmEnvio.onsubmit = function (evt) {
                            evt.preventDefault();
                            submit_form(this, function ($rs) {
                                document.getElementById('hh-costo-envio').innerHTML = $rs["total"];
                            });
                        };
                    }
                })
            });
            //fetch();
        }

        document.getElementById('frm-pagar').onsubmit = function (event) {
            event.preventDefault();
            if ( !total_compra )
            {
                jdialog("<span class='text-danger'><?=$emptyCartMsg?></span>");
                return;
            }
            let this_form = this;
            jconfirm(function ($yes) {
                if ( $yes )
                {
                    submit_form(this_form);
                }
            }, "Serás redireccionado a MercadoPago para completar tu compra. ¿Continuar?");
        };

        get_items();
        document.getElementById('aa-cart').remove();
    </script>
<?php endif; ?>