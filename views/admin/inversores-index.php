<div class="row">

    <?= $_table ?>

</div>

<script type="text/javascript">


    if ( (aaNuevo = document.getElementById('aa-nuevo')) )

    {

        aaNuevo.onclick = function (aa) {

            aa.preventDefault();

            get_modal_form_nuevo_gasto(0, 1);

        };

    }

    if ( (aaNuevo = document.getElementById('simulador-costos')) )

    {
        console.log("Pasa")
        aaNuevo.onclick = function (aa) {

            aa.preventDefault();

            get_modal_form_nuevo_gasto(0, 1);

        };

    }

</script>