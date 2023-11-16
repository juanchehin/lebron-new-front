<div class="row">

    <?= $_table ?>

</div>

<script type="text/javascript">

    function get_form(fechaGasto, form)

    {

        linkUrl = "nuevoQuimicoForm";

        if ( form )

        {

            linkUrl = "nuevoQuimicoForm";

        }

        get_modal_form({'fechaVentaQuimico': fechaVentaQuimico}, "!AdminVentaQuimico/" + linkUrl)

    }


    if ( (aaNuevo = document.getElementById('aa-nuevo')) )

    {

        aaNuevo.onclick = function (aa) {

            aa.preventDefault();

            get_modal_form_nuevo_gasto(0, 1);

        };

    }

</script>