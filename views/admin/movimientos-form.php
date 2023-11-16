<style>
    #cuenta-form-container {
        position: absolute;
        left: 30px;
        z-index: 15;
        right: 30px;
        background: #d5dfe7;
        border: 1px solid #ccc;
        text-align: left;
    }
</style>
<div class="panel panel-default">
    <?php if ( $titulo ) : ?>
        <div class="panel-heading text-center"><?= $titulo ?></div>
    <?php endif; ?>
    <div class="panel-body">
        <?php
        $_concepto = $item->hasConcepto;
        ?>
        <?php if ( !$item && $gestion_cuenta ): ?>
            <div class="form-group text-right">
                <a href="javascript:void(0)" id="collapse-form" data-toggle="collapse" data-target="#cuenta-form-container">Crear cuenta <i class="fa fa-caret-down"></i></a>
                <div id="cuenta-form-container" class="collapse">
                    <?= $form_cuentas ?>
                </div>
            </div>
        <?php endif; ?>
        <form autocomplete="off" id="frm-movimiento" action="!AdminCaja/guardarRegistro">
            <input type="hidden" name="accion" value="<?= $tipo ?>"/>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="id_cuenta">Cuenta Debe: <i class="required"></i></label>
                        <select name="id_cuenta" minlength="0" id="id_cuenta" class="form-control" required>
                            <option value="">Seleccionar</option>
                            <?php foreach ($cuentas_debe as $cuenta) : ?>
                                <option value="<?= $val = $cuenta->id_concepto ?>"><?= $cuenta->concepto_cuenta ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_cuenta_haber">Cuenta Haber <i class="required"></i></label>
                        <select name="id_cuenta_haber" minlength="0" id="id_cuenta_haber" class="form-control" disabled required>
                            <option value="">Seleccionar</option>
                            <?php foreach ($cuentas_haber as $concepto) : ?>
                                <option value="<?= ($id = $concepto->id_concepto) ?>" <?= ($id == $item->id_concepto) ? "selected" : "" ?>><?= $concepto->concepto_cuenta ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="importe">Importe $ <i class="required"></i></label>
                        <input type="tel" name="importe" id="importe" class="nf form-control" value="<?= $item->importe ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha">Fecha <i class="required"></i></label>
                        <input type="text" name="fecha" id="fecha" value="<?= $item->fecha ?: date('d/m/Y') ?>" class="nf form-control">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php
                echo "<label for='id_concepto'>Concepto: <a href='javascript:void(0)' name='1' onclick='opt_concepto(this)'>Nuevo</a></label>";
                $selectConcepto = "<div id='concepto-container'>";
                $selectConcepto .= "<select id='id_concepto' minlength='0' name='id_concepto' class='form-control selectar' disabled>";
                $selectConcepto .= "</select>";
                $selectConcepto .= "</div>";
                echo $selectConcepto;
                ?>
            </div>
            <div class="form-group">
                <?php if ( true ): ?>
                    <label for="nota">Detalle</label>
                    <textarea id="nota" maxlength="250" name="nota" class="form-control"><?= $item->comentario ?></textarea>
                <?php endif; ?>
                <?php if ( $item->id ) : ?>
                    <input type="hidden" id="item-id" name="id" value="<?= $item->id ?>"/>
                <?php elseif ( false ) : ?>
                    <input type="checkbox" id="crear-nuevo"/>&nbsp;<label for="crear-nuevo">No cerrar</label>
                    <br/>
                <?php endif; ?>
                <p style="font-size:12px"><i class="required"></i> Obligatorios</p>
            </div>
            <div class="form-group text-right">
                <button type="submit" id="btn-save" class="btn btn-success">Guardar</button>
                <a href="javascript:void(0)" id="aa-cerrar" class="btn btn-default" data-dismiss="modal">Cerrar</a>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    var id_movimiento = "<?=$item->id?>";
    var es_ingreso = parseInt(<?=$es_ingreso?>);
    //$('#importe').integer();
    $('#fecha').calendario({"yearRange": "-1:+1"});
    $('#tipo-mov [value="<?=$item->accion?>"]').attr("checked", true);
    $('#importe').decimal(".");
    var selectDebe = document.getElementById('id_cuenta');
    var selectHaber = document.getElementById('id_cuenta_haber');
    selectDebe.onchange = function () {
        if ( !this.value )
        {
            //selectHaber.value = "";
            selectHaber.setAttribute("disabled", true);
            $(selectHaber).val("").trigger("change");
            return;
        }
        selectHaber.insertAdjacentHTML("beforebegin", "<i class='fa fa-spin fa-spinner'></i>");
        fetch("!AdminCaja/selectCuentaHaber", {
            "method": "post",
            "body": JSON.stringify({"id_cuenta": this.value, "tipo": es_ingreso})
        }).then(function (res) {
            res.text().then(function ($options) {
                selectHaber.previousElementSibling.remove();
                selectHaber.removeAttribute("disabled");
                selectHaber.innerHTML = $options;
                selectHaber.value = "<?=$item->id_concepto?>";
                selectHaber.onchange();
            });
        });
    };

    selectHaber.onchange = function () {
        if ( !this.value )
        {
            return;
        }
        let selectConceptos = document.getElementById('id_concepto');
        let id_cuenta = es_ingreso ? this.value : selectDebe.value;
        if ( !id_cuenta )
        {
            selectConceptos.value = "";
            selectConceptos.setAttribute("disabled", true);
            return;
        }
        selectConceptos.insertAdjacentHTML("beforebegin", "<i class='fa fa-spin fa-spinner' style='position:absolute'></i>");
        fetch("!AdminCaja/selectConceptos?id_cuenta=" + id_cuenta).then(function ($res) {
            $res.text().then(function ($html) {
                selectConceptos.previousElementSibling.remove();
                selectConceptos.removeAttribute("disabled");
                selectConceptos.innerHTML = $html;
                selectConceptos.value = "<?=$item->id_cuenta?>";
                if ( es_ingreso )
                {
                    selectConceptos.value = "<?=$item->id_concepto?>";
                    selectHaber.value = id_cuenta;
                }
            });
        });
    };

    $('.selectar').selectar();
    if ( id_movimiento )
    {
        selectDebe.value = "<?=$id_cuenta_debe?>";
        selectDebe.onchange();
    }

    function opt_concepto($this)
    {
        const conceptoContainer = document.getElementById('concepto-container');
        if ( $this.name )
        {
            $this.removeAttribute("name");
            $this.innerHTML = "Cancelar";
            conceptoContainer.innerHTML = "<input type='text' name='concepto' id='id_concepto' class='form-control'>";
            return;
        }
        $this.setAttribute("name", 1);
        $this.innerHTML = "Nuevo";
        conceptoContainer.innerHTML = "<?=$selectConcepto?>";
        $('#id_concepto').selectar();
        selectHaber.onchange();
    }

    if ( (frmCuentas = document.getElementById('frm-cuentas')) )
    {
        frmCuentas.onsubmit = function (evt) {
            evt.preventDefault();
            let $form = this;
            submit_form($form, function ($data) {
                if ( $data.id_cuenta )
                {
                    let select_option = "<option value='" + $data.id_cuenta + "' selected>" + $data.nombre + "</option>";
                    if ( $data.accion !== "HABER" )
                    {
                        selectDebe.insertAdjacentHTML("beforeend", select_option);
                        selectDebe.onchange();
                    }
                    else
                    {
                        selectHaber.removeAttribute("disabled");
                        selectHaber.insertAdjacentHTML("beforeend", select_option);
                        selectHaber.onchange();
                    }
                }
                $form.reset();
                document.getElementById('collapse-form').click();
            });
        };
    }

    $('#frm-movimiento').submit(function (e) {
        e.preventDefault();
        let $_form = this;
        submit_form($_form, function () {
            if ( _current_class.search(/caja/gi) >= 0 && typeof get_rows === "function" )
            {
                get_rows();
            }
            if ( !id_movimiento )
            {
                /* $('#importe, #nota').val("");
                 $('#id_cuenta').val("").trigger("change");
                 $('#btn-save').removeAttr("disabled").find(".fa-spin").remove();*/
                $_form.reset();
                selectDebe.onchange();
                opt_concepto(document.createElement('a'));
                return;
            }
            $('#aa-cerrar').trigger('click');
        });
    });
</script>