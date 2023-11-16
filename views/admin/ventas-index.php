<style>

    .tr-realizada td {

        background: #c4ee76;

    }



    .tr-<?=Venta::estadoEspera?> td {

        background: #caecee;

    }



    .tr-<?=Venta::estadoEnviado?> td {

        background: #fffe9f;

    }



    .data-table tr.parpadea {

        animation-name: parpadeo;

        animation-duration: 1s;

        animation-timing-function: linear;

        animation-iteration-count: infinite;

        -webkit-animation-name: parpadeo;

        -webkit-animation-duration: 1s;

        -webkit-animation-timing-function: linear;

        -webkit-animation-iteration-count: infinite;

    }



    @-moz-keyframes parpadeo {

        0% {

            opacity: 1.0;

        }

        50% {

            opacity: 0.4;

        }

        100% {

            opacity: 1.0;

        }

    }



    @-webkit-keyframes parpadeo {

        0% {

            opacity: 1.0;

        }

        50% {

            opacity: 0.4;

        }

        100% {

            opacity: 1.0;

        }

    }



    @keyframes parpadeo {

        0% {

            opacity: 1.0;

        }

        50% {

            opacity: 0.4;

        }

        100% {

            opacity: 1.0;

        }

    }

</style>

<div class="row">

    <?= $data_table ?>

</div>

<script type="text/javascript">

    $('.table-container').prepend("<span id='total-ventas' class='amount pull-right'></span>");

    if ( parseInt(<?=$online?>) )

    {

        document.getElementById('aa-nuevo').remove();

    }

    else

    {

        if ( !(document.getElementById('desde').value && document.getElementById('hasta').value) )

        {

            //document.querySelector('[rel="dia"]').click();

        }

    }



    function set_estado($data)

    {

        if ( typeof $data !== "object" )

        {

            return;

        }

        fetch("!AdminVenta/setEstado", {

            "method": "POST",

            "body": new URLSearchParams($data)

        }).then(function () {

            get_rows();

            control_incidencias();

        });

    }

    
    function get_form_venta(opt)
    {

        id_venta = opt.closest('tr, .dt_row').getAttribute('id');

        linkUrl = "updateVentaForm";

        get_modal_form_editar_venta({'id_venta': id_venta}, "!AdminVenta/" + linkUrl)

    }

    function get_form_tipo_venta(opt)
    {
        id_venta = opt.closest('tr, .dt_row').getAttribute('id');

        linkUrl = "updateTipoVentaForm";

        get_modal_form_editar_tipo_venta({'id_venta': id_venta}, "!AdminVenta/" + linkUrl)

    }

</script>