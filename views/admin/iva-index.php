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


    if ( (aaAltaIvaCompra = document.getElementById('aa-alta-iva-compra')) )

    {

        aaAltaIvaCompra.onclick = function (aa) {

            aa.preventDefault();

            get_modal_form_nuevo_iva_compra("modalFormIvaCompra");

        };

    }

    if ( (aaAltaIvaVenta = document.getElementById('aa-alta-iva-venta')) )

    {

        aaAltaIvaVenta.onclick = function (aa) {

            aa.preventDefault();

            get_modal_form_nuevo_iva_venta("modalFormIvaVenta");

        };

    }

</script>