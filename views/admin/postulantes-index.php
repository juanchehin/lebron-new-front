<div class="row">

    <?= $_table ?>

</div>

<script type="text/javascript">

    function get_form(fechaGasto, form)

    {

        linkUrl = "nuevoGastoForm";

        if ( form )

        {

            linkUrl = "nuevoGastoForm";

        }

        get_modal_form({'fechaGasto': fechaGasto}, "!AdminGastos/" + linkUrl)

    }


    if ( (aaNuevo = document.getElementById('aa-nuevo')) )

    {

        aaNuevo.onclick = function (aa) {

            aa.preventDefault();

            get_modal_form_nuevo_gasto(0, 1);

        };

    }

</script>