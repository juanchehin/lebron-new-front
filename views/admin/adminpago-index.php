<div class="row">
    <?= $dataTable ?>
</div>
<style>
    .tr-totales td {
        background: #fff;
    }
</style>
<script>
    document.getElementById('hasta').remove();
    if ( (checkHoy = document.getElementById('hoy')) )
    {
        checkHoy.onclick = function () {
            values["month"] = Number(this.checked);
            //this.nextSibling.textContent = this.checked ? " HOY" : "";
            //selectMes.value = values["mes"] = "< ?=date('m')?>";
            //document.getElementById('anio').value = values["anio"] = "< ?=date('Y')?>";
            get_rows();
        };
        //checkHoy.checked = parseInt(document.getElementsByClassName('tr-totales')[0].id);
    }


    if ( (aaNuevo = document.getElementById('aa-nuevo')) )
    {
        aaNuevo.onclick = function (evt) {
            evt.preventDefault();
            get_modal_form({"adm": 1}, `!AdminPago/form`);
        };
    }
</script>