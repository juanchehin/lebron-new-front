<div class="panel panel-default">
    <div class="panel-heading text-center"><?= $titulo ?></div>
    <div class="panel-body">
        <form action="!AdminCuenta/registro?idc=<?= $id_cliente ?>" id="frm-cuenta">
            <div class="row">
                <div class="col-md-5 form-group">
                    <label for="cuenta">Medio de Pago <i class="required"></i></label>
                    <select name="cuenta" id="cuenta" class="form-control" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($cuentas as $cuenta): ?>
                            <option value="<?= $cuenta->id_concepto ?>"><?= $cuenta->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 form-group" id="dv-extra-ctrl">&nbsp;</div>
                <div class="clearfix"></div>
                <div class="col-md-5 form-group"></div>
                <div class="col-md-3 form-group">
                    <label for="importe">Importe <i class="required"></i></label>
                    <input type="tel" name="importe" id="importe" class="form-control" required disabled>
                </div>
                <div class="col-md-4 form-group">
                    <label for="fecha">Fecha <i class="required"></i></label>
                    <input type="text" name="fecha" id="fecha" class="form-control" value="<?= date('d/m/Y') ?>" required>
                </div>
                <?php if ( false ): ?>
                    <div class="col-md-12 form-group">
                        <?php
                        echo "<label for='id_concepto'>Concepto: <a href='javascript:void(0)' name='1' onclick='opt_concepto(this)'>Nuevo</a></label>";
                        $selectConcepto = "<div id='concepto-container'>";
                        $selectConcepto .= "<select id='id_concepto' minlength='0' name='id_concepto' class='form-control selectar' disabled>";
                        $selectConcepto .= "</select>";
                        $selectConcepto .= "</div>";
                        echo $selectConcepto;
                        ?>
                    </div>
                    <div class="col-md-12 form-group">
                        <label for="nota">Nota:</label>
                        <textarea name="nota" id="nota" rows="4" class="form-control"></textarea>
                        <p class="small"><i class="required"></i> Datos requeridos</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group text-right">
                <input type="hidden" name="id" value="<?= $model . "&" . $item->id ?>">
                <button type="submit" class="btn btn-primary">Aceptar</button>
                <a class="btn btn-default" id="aa-cierre" data-dismiss="modal">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script>
    var es_ingreso = true, theSaldo = parseFloat(<?=$cliente->saldo?>);
    var labelSaldo = document.getElementById('label-saldo');
    (inputImporte = document.getElementById('importe')).oninput = function () {
        //if ( parseFloat(selectHaber.value) === 2 )
        {
            monto = theSaldo - parseFloat(this.value || 0);
            labelSaldo.innerHTML = monto;
        }
    };
    $(inputImporte).decimal(".");
    $('#fecha').calendario({"yearRange": "-1:+1"});
    document.getElementById('frm-cuenta').onsubmit = function (evt) {
        evt.preventDefault();
        theForm = this;
        hdnSaldo = document.createElement('input');
        hdnSaldo.type = "hidden";
        hdnSaldo.readOnly = true;
        hdnSaldo.name = "saldo";
        hdnSaldo.value = labelSaldo.innerText;
        theForm.appendChild(hdnSaldo);
        submit_form(theForm, function () {
            get_rows();
            //document.getElementById('label-saldo').innerHTML = $res.saldo;
            document.getElementById('aa-cierre').click();
            //theForm.reset();
        });
        hdnSaldo.remove();
    };
    if ( (selectHaber = document.getElementById('cuenta')) )
    {
        selectHaber.insertAdjacentHTML("beforeend", "<option value='<?=$itemDeuda?>' style='background:red;color: #fff;'>REGISTRO DE DEUDA</option>");
        selectHaber.onchange = function () {
            (dvExtraCtrl = document.getElementById('dv-extra-ctrl')).innerHTML = "";
            if ( !this.value || (parseInt(this.value) === 2) )
            {
                inputImporte.setAttribute("disabled", true);
                return;
            }
            if ( this.value === "<?=Concepto::cuentaBanco?>" )
            {
                selectProveedor = "<label for='a-cuenta'>A cuenta de <i class='required'></i></label>";
                selectProveedor += `<select name='a_cuenta' id="a-cuenta" class="form-control">`;
                selectProveedor += `<option value="">Seleccionar</option>`;
                selectProveedor += `<option value="s">Cuenta Propia</option>`;
                <?php foreach ($proveedores as $provedor): ?>
                selectProveedor += `<option value="<?=$provedor->id?>"><?=$provedor->label?></option>`;
                <?php endforeach; ?>
                selectProveedor += `</select>`;
                dvExtraCtrl.innerHTML = selectProveedor;
            }
            inputImporte.removeAttribute("disabled");
            inputImporte.focus();
            /*let selectConceptos = document.getElementById('id_concepto');
            let id_cuenta = es_ingreso ? this.value : selectDebe.value;
            if ( !id_cuenta )
            {
                selectConceptos.value = "";
                selectConceptos.setAttribute("disabled", true);
                return;
            }
            selectConceptos.insertAdjacentHTML("beforebegin", "<i class='fa fa-spin fa-spinner' style='position:absolute'></i>");
            fetch(`!AdminCuenta/selectConceptos?id_cuenta=${id_cuenta}`).then(function ($res) {
                $res.text().then(function ($html) {
                    selectConceptos.previousElementSibling.remove();
                    selectConceptos.removeAttribute("disabled");
                    selectConceptos.innerHTML = $html;
                    selectConceptos.value = "< ?=$item->id_cuenta?>";
                    if ( es_ingreso )
                    {
                        selectConceptos.value = "< ?=$item->id_concepto?>";
                        selectHaber.value = id_cuenta;
                    }
                });
            });*/
        };
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
</script>