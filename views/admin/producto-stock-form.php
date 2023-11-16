<div class="panel panel-warning">
    <div class="panel-heading text-uppercase">#<?= $data->id_producto . " - " . $data->producto ?></div>
    <div class="panel-body">
        <form action="!AdminProducto/actualizarStock" id="frm-stock" autocomplete="off">
            <div class="form-group">
                <h4>Detalle</h4>
                <h5><?= $data->detalle ?></h5>
            </div>
            <div class="row">
                <div class="col-md-7 form-group">
                    <label for="">Cantidades</label>
                    <?= $data->cantidad_string ?>
                    <hr>
                </div>
                <div class="col-md-5 form-group">
                    <div class="form-group">
                        <label for="codigo">C&oacute;digo: <i class="required"></i></label>
                        <input type="tel" name="codigo" id="codigo" class="form-control" value="<?= $data->codigo ?>" required>
                    </div>
                    <?php if ( false ): ?>
                        <div class="form-group">
                            <label for="precio">Precio <i class="required"></i></label>
                            <input type="tel" name="precio" id="precio" class="form-control" value="<?= $data->precio ?>" required>
                        </div>
                    <?php endif; ?>
                    <?php if ( $adminCp ): ?>
                        <label for="loguear"><input type="checkbox" id="loguear" name="con_log"/> Con Log</label>
                    <?php else:; ?>
                        <input type="hidden" name="con_log" value="1">
                    <?php endif; ?>
                    <?php if ( true ): ?>
                        <div class="form-group">
                            <label for="precio">Precio <i class="required"></i></label>
                            <input type="tel" name="precio" id="precio" class="form-control" value="<?= $data->precio ?>" required>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="local">Origen</label>
                    <select name="local" id="local" class="form-control">
                        <option value="">Seleccionar</option>
                        <?php foreach ($locales as $id => $deposito) : ?>
                            <option value="<?= $id ?>"><?= $deposito ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="destino">Destino</label>
                    <select name="destino" id="destino" class="form-control" disabled>
                        <option value="">Egreso</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="cantidad">Cantidad</label>
                    <div class="input-group">
                        <input type="tel" id="cantidad" name="cantidad" class="form-control">
                        <?php if ( true ): ?>
                            <div class="input-group-addon" style="padding:2px 0">
                                <select id="accion" name="accion">
                                    <option value="RESTA">Restar</option>
                                    <option value="SUMA" selected>Sumar</option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group text-right">
                <br/>
                <input type="hidden" name="id_producto" value="<?= $data->id_producto ?>"/>
                <button type="submit" class="btn btn-warning">Aceptar</button>
                <a href="javascript:void(0)" id="a-close" data-dismiss="modal" class="btn btn-default">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    var accion = "", operacion = "";
    accionResta = 'RESTA';
    esVenta = false;
    selectDestino = document.getElementById('destino');
    $('#cantidad').on("input", function () {
        const selectLocal = $('#local');
        if ( this.value )
        {
            selectLocal.attr("required", true);
            return;
        }
        selectLocal.removeAttr("required");
    }).numeric();
    $('#precio').decimal(".");

    selectLocal1 = document.getElementById('local');
    selectLocal1.onchange = function () {
        let locales = <?=json_encode($locales ?: array())?>;
        let accion = document.getElementById('accion');
        id_local = parseInt(this.value);
        esVenta = (<?=json_encode(Local::$_puntosVenta)?>).includes(id_local);
        selectDestinoOptions = "<option value=''>Seleccionar</option>";
        delete locales[id_local];
        if ( esVenta )
        {
            operacion = "<?=Venta::tpVenta?>";
            //selectAccion.value = accionResta;
            //selectAccion.setAttribute("disabled", true);
            selectAccion.onchange();
        }
        else
        {
            /*-----*/
            for (local in locales)
            {
                selectDestinoOptions += `<option value="${local}">${locales[local]}</option>`;
            }
            selectDestino.innerHTML = selectDestinoOptions;
            selectDestino.removeAttribute("disabled");
            accion.removeAttribute("disabled");
        }
        //selectAccion.onchange();
    };

    selectDestino.onchange = function () {
        if ( parseInt(this.value) )
        {
            operacion = "<?=Venta::tpTraspaso?>";
        }
        else
        {
            operacion = "";
            selectAccion.value = "SUMA";
            selectAccion.removeAttribute("disabled");
        }
        selectAccion.onchange();
    };

    if ( (selectAccion = document.getElementById('accion')) )
    {
        selectAccion.onchange = function () {
            accion = (this.value !== 'SUMA') ? "Descontar" : "Agregar";
            if ( operacion === "<?=Venta::tpVenta?>" )
            {
                selectDestino.innerHTML = "<option value=''>Egreso</option>";
                selectDestino["disabled"] = true;
            }
            else if ( this.value === accionResta )
            {
                selectDestino.setAttribute("required", true);
                selectDestino.removeAttribute("disabled");
            }
            else
            {
                selectDestino.removeAttribute("required");
            }
            if ( operacion )
            {
                this.value = accionResta;
                this["disabled"] = true;
            }
            //$('label[for="accion"]').html(accion);
            //$('label[for="cantidad"]').html("Cantidad a " + accion);
            $('#cantidad').focus();
        };
    }

    document.getElementById('frm-stock').onsubmit = function (e) {
        e.preventDefault();
        let stock_form = this;
        /*jconfirm(function (result) {
            if ( result )
            {*/
        submit_form(stock_form, function () {
            document.getElementById('a-close').click();
            get_rows();
        });
        /*}
    }, accion + " <b>" + $('#cantidad').val() + "</b> un. a '<b><php $data->producto?></b>' en local <b>" + $('#local option:selected').text() + "</b>. ¿Continuar?");*/
    };
</script>