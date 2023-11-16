<div class="row">
    <?= $clientes_table ?: $cuenta_table ?>
</div>
<style>
    tr.unpaid td {
        background: #ffbaa9;
    }
</style>
<script>

    if ( (checkTodos = document.getElementById('check-all')) )
    {
        checkTodos.onclick = function () {
            values["all"] = Number(this.checked);
            get_rows();
        };
        checkTodos.checked = values["all"];
    }
    prms = {"mdl": values['model']};
    to_url = "";
    <?php if($cuenta_table) : ?>
    document.getElementsByClassName('page-header')[0].insertAdjacentHTML("beforeend", "<h4 style='margin:0' id='hh-saldo'></h4>");
    document.getElementById('hasta').remove();
    document.getElementsByClassName('fecha-criterios')[0].remove();
    prms["cid"] = "<?=$id_cliente?>";
    prms["persona"] = "<?=$persona?>";
    to_url = "!AdminPago/form";
    <?php endif; ?>

    if ( (aaNuevoBtn = document.getElementById('aa-nuevo')) )
    {
        aaNuevoBtn.onclick = function (evt) {
            evt.preventDefault();
            get_modal_form(prms, to_url);
        };
    }
</script>