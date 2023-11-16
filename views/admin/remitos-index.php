<div class="row">

    <?= $_table ?>

</div>

<script type="text/javascript">

    $('#search_box').attr("placeholder", "Buscar nro remito");


    // function get_form(fechaGasto, form)

    // {

    //     linkUrl = "nuevoGastoForm";

    //     if ( form )

    //     {

    //         linkUrl = "nuevoGastoForm";

    //     }

    //     get_modal_form({'fechaGasto': fechaGasto}, "!AdminGastos/" + linkUrl)

    // }


    // if ( (aaNuevo = document.getElementById('aa-nuevo')) )

    // {

    //     aaNuevo.onclick = function (aa) {

    //         aa.preventDefault();

    //         get_modal_form_nuevo_gasto(0, 1);

    //     };

    // }

    function get_form_comprobante(opt)
    {
        
        id_venta = opt.closest('tr, .dt_row').getAttribute('id');

        linkUrl = "updateComprobanteForm";

        get_modal_form_editar_comprobante({'id_venta': id_venta}, "!AdminRemitos/" + linkUrl)

    }

</script>