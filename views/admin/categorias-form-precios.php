<div class="panel panel-default">

    <div class="panel-heading"><?= $titulo ?></div>

    <div class="panel-body">

        <form id="frm-rubro" action="!AdminCategoria/actualizar_precios" autocomplete="off">

            <div class="row">

                <div class="col-md-6 form-group">

                    <input type="hidden" name="id_item" value="<?= $item->id_item ?>">

                    <label for="nombre">Aumento porcentual % <i class="required"></i></label>

                    <input type="text" id="aumento_porcentual" class="form-control" maxlength="3" max="100" name="aumento_porcentual" placeholder="0" required/>

                </div>

            </div>

            <div class="form-btns text-right">

                <button type="submit" class="btn btn-primary">Guardar</button>

                <a href="javascript:void(0)" data-dismiss="modal" id="aa-modal-close" class="btn btn-default">Cerrar</a>

            </div>

        </form>

    </div>

</div>

<script>

    document.getElementById('frm-rubro').onsubmit = function (e) {

        e.preventDefault();

        submit_form(this, function (rsp) {

            if ( rsp["cambios"] )

            {

                list_rows();

            }

            document.getElementById('aa-modal-close').click();

        });

    };

    document.getElementById('first-group').remove();

    if ( (pgDiv = document.getElementById('second-group')) )

    {

        pgDiv.classList.remove("col-md-6");

        pgDiv.classList.add("col-md-12");

    }

    // $('.modal-header').css("border-bottom", "none");

</script>