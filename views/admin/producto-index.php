<div class="row">
    <?= $_table ?>
</div>
<script type="text/javascript">
    $('#search_box').attr("placeholder", "Buscar por Codigo o Producto");
    $('#alerta').click(function () {
        values['alerta'] = Number(this.checked);
        get_rows();
    });
    //$('#action-buttons').prepend("<a href='!AdminProducto/exportar' title='PDF' class='btn btn-info pull-right'><i class='fa fa-file-pdf-o'></i> Exportar</a>");
    //$('#action-buttons .dropdown-menu').append("<li><a target='_blank' href='!AdminProducto/exportar?bc=1'>C&oacute;digos de Barra</a></li>");
    document.getElementsByClassName('table-container')[0].insertAdjacentHTML("afterbegin", "<?=$log_url?>");

    function get_form(option)
    {
        get_modal_form({'id': get_row_id(option)}, "cantidadesForm")
    }

    if ( (selectMarca = document.getElementById('id_marca')) )
    {
        selectMarca.onchange = function () {
            let aa_export_bc = "<a href='!AdminProducto/exportar?pdf' target='_blank'>C&oacute;digos de Barra</a>";
            if ( !this.value )
            {
                aa_export_bc = "";
            }
            document.getElementById('dv-export').innerHTML = aa_export_bc;
        };

        function printElem()
        {
            fetch("!AdminProducto/exportar?pdf").then(function (rsp) {
                rsp.text().then(function (content) {
                    //var content = document.getElementById('dv-print').innerHTML;
                    var mywindow = window.open('', 'Print', 'height=600,width=800');

                    mywindow.document.write('<html lang="es"><head><title>Print</title>');
                    mywindow.document.write('</head><body >');
                    mywindow.document.write(content);
                    mywindow.document.write('</body></html>');

                    mywindow.document.close();
                    mywindow.focus();
                    mywindow.print();
                    mywindow.close();
                    return true;
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