<div class="panel panel-default">
    <div class="panel-heading"><?= $titulo ?></div>
    <div class="panel-body">
        <form id="frm-rubro" action="!AdminCategoria/guardar" autocomplete="off">
            <div class="row">
                <div class="col-md-6 form-group">
                    <input type="hidden" name="id_item" value="<?= $item->id_item ?>">
                    <label for="nombre">Nombre <i class="required"></i></label>
                    <input type="text" id="nombre" class="form-control" name="nombre" value="<?= $item->nombre ?>" required/>
                </div>
                <div class="form-group col-md-6">
                    <?= $precioBlock ?>
                    <?php if ( false ): ?>
                        <label for="id_padre">Pertenece a:</label>
                        <select id="id_padre" class="form-control" name="id_padre" disabled>
                            <option value="">Seleccionar</option>
                            <?php foreach ($categorias as $categoria) : ?>
                                <option value="<?= $categoria->id_item ?>"><?= $categoria->titulo ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-7">
                    <?=$imageControl?>
                </div>
                <div class="col-md-12 form-group">
                    <input type="hidden" name="id_item_padre" value="<?= $item->id_item_padre ?>"/>
                    <label for="descripcion">Descripci&oacute;n</label>
                    <textarea id="descripcion" class="form-control" name="descripcion"><?= $item->descripcion ?></textarea>
                </div>
                <div class="col-md-12">
                    <?php
                    echo "<input type='hidden' name='tag' value='{$tag}'>";
                    if ( !$item->estatico )
                    {
                        echo "<label for='activo'><input type='checkbox' name='activo' id='activo' " . (($item->activo || !$item) ? 'checked' : null) . "> Activo</label>";
                        //echo HForm::inputCheck('activo', ($item->activo || !$item), null, true);
                    }
                    else
                    {
                        //echo $item->esActivo(true);
                        echo "<i class='fa fa-check-square'></i>";
                    }
                    ?>
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