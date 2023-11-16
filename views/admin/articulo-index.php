<div class="row">

    <?= $_table ?>

</div>

<script type="text/javascript">

    $('#search_box').attr("placeholder", "Buscar por Codigo o Producto");

    $('.table-container').prepend("<span id='total-ventas' class='amount pull-right'></span>");


    if ( (checkAlerta = document.getElementById('alerta')) )

    {

        checkAlerta.onclick = function () {

            values['alerta'] = Number(this.checked);

            get_rows();

        };

    }

    //$('#action-buttons').prepend("<a href='!AdminProducto/exportar' title='PDF' class='btn btn-info pull-right'><i class='fa fa-file-pdf-o'></i> Exportar</a>");

    //$('#action-buttons .dropdown-menu').append("<li><a target='_blank' href='!AdminProducto/exportar?bc=1'>C&oacute;digos de Barra</a></li>");

    //document.getElementsByClassName('table-container')[0].insertAdjacentHTML("afterbegin", "< ?=$log_url?>");

    if ( (aaNuevo = document.getElementById('aa-nuevo')) )

    {

        aaNuevo.onclick = function (aa) {

            aa.preventDefault();

            get_form(0, 1);

        };

    }



    function get_form(id_articulo, form)

    {

        linkUrl = "cantidadesForm";

        if ( form )

        {

            linkUrl = "productoForm";

        }

        get_modal_form({'id': id_articulo}, "!AdminArticulo/" + linkUrl)

    }




    if ( (selectMarca = document.getElementById('id_marca')) )

    {

        selectMarca.onchange = function () {

            let aa_export_bc = "<a href='!AdminArticulo/exportar?pdf' target='_blank'>C&oacute;digos de Barra</a>";

            if ( !this.value )

            {

                aa_export_bc = "";

            }

            document.getElementById('dv-export').innerHTML = aa_export_bc;

        };



        function printElem()

        {

            fetch("!AdminArticulo/exportar?pdf").then(function (rsp) {

                rsp.text().then(function (res) {

                    //var content = document.getElementById('dv-print').innerHTML;

                    aaRef = document.createElement("a");

                    aaRef.href = res;

                    aaRef.id = "aa-listado";

                    aaRef.innerHTML = "Pino";

                    aaRef.target = "_blank";

                    document.body.appendChild(aaRef);

                    document.getElementById('aa-listado').click();

                    //aaRef.remove();

                })

            });

            return true;

        }



        selectMarca.onchange();



        document.getElementById('a-reset').onclick = function (ee) {

            ee.preventDefault();

            selectMarca.onchange();

        }

    }

</script>