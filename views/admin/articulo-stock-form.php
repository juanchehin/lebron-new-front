<div class="panel panel-warning">

    <div class="panel-heading text-uppercase" style="color:#0f9d58;padding:4px 15px">

        #<?= $data->id_producto . " - " . $data->producto ?>

        <br/><?= $data->detalle ?>

    </div>

    <div class="panel-body">

        <form action="!AdminArticulo/guardar?sf" id="frm-stock" autocomplete="off">

            <div class="row">

                <div class="col-md-4 form-group">

                    <label for="">Cantidades</label>

                    <?= $data->cantidad_string ?>

                    <hr>

                </div>

                <div class="col-md-8 form-group">

                    <div class="input-group-addon" style="<?= $css0 = 'padding:3px 0;border:none;background:none;text-align:left' ?>;width:2.5%">

                        <label for="codigo">C&oacute;digo: <i class="required"></i></label>

                        <input type="tel" name="codigo" id="codigo" class="form-control" value="<?= $data->codigo ?>" required>

                    </div>

                    <div class="input-group-addon" style="<?= $css0 ?>;">

                        <p>&nbsp;</p>

                        <?php if ( false ): ?>

                            <label for="loguear"><input type="checkbox" id="loguear" name="con_log"/> Con Log</label>

                        <?php else:; ?>

                            <input type="hidden" name="con_log" value="1">

                        <?php endif; ?>

                    </div>

                    <?php if ( true ): ?>

                        <p></p>

                        <?= $preciosBlock ?>

                    <?php endif; ?>

                </div>

            </div>

            <div class="row">

                <div class="form-group col-md-4">

                    <?php if ( true ): ?>

                        <div class="input-group-addon" style="<?= $css0 ?>">

                            <label for="accion">&nbsp;</label>

                            <select id="accion" name="accion" class="form-control">

                                <option value="">Operaci&oacute;n</option>

                                <?php foreach (["egreso", Venta::tpTraspaso, "ingreso"] as $accion) : ?>

                                    <option><?= ucwords($accion) ?></option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    <?php endif; ?>

                    <div class="input-group-addon" style="<?= $css0 ?>;width: .05%">

                        <label for="cantidad">Cantidad</label>

                        <input type="tel" id="cantidad" name="stock" class="form-control" disabled>

                    </div>

                </div>

                <div class="col-md-4 form-group">

                    <label for="local">Local</label>

                    <select name="local" id="local" class="form-control" disabled>

                        <option value="">Seleccionar</option>

                        <?php foreach ($locales as $id => $deposito) : ?>

                            <option value="<?= $id ?>"><?= $deposito ?></option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-4 form-group" id="dv-select-destino"></div>

                <div class="col-md-12 form-group" id="dv-nota"></div>

            </div>

            <div class="form-group text-right">

                <br/>

                <input type="hidden" name="id_producto" value="<?= $data->id_producto ?>"/>

                <label for="paralelo" class="pull-left">

                    <input type="checkbox" id="paralelo" name="importado" <?= $data->id_sucursal ? "checked" : "" ?>> D&oacute;lar a $<?= $config['dolar_paralelo'] ?>

                </label>

                <button type="submit" class="btn btn-warning">Aceptar</button>

                <a href="javascript:void(0)" id="a-close" data-dismiss="modal" class="btn btn-default">Cancelar</a>

            </div>

        </form>

    </div>

</div>

<script type="text/javascript">

    var operacion = "",

        locales = [],

        opEgreso = "egreso",

        pto_vta = <?=json_encode(Local::$_puntosVenta ?: [])?>,

        js_motivos =<?=json_encode($motivos ?: [])?>;

    if ( (selectLocal1 = document.getElementById('local')) )

    {

        dvSelectDestino = document.getElementById('dv-select-destino');

        dvNota = document.getElementById('dv-nota');

        inputCantidad = document.getElementById('cantidad');



        inputCantidad.oninput = function () {

            if ( parseInt(this.value) )

            {

                selectLocal1.setAttribute("required", true);

                return;

            }

            selectLocal1.removeAttribute("required");

        };

        $(inputCantidad).numeric();



        if ( (selectAccion = document.getElementById('accion')) )

        {

            selectAccion.onchange = function () {

                locales = <?=json_encode($locales ?: array())?>;

                dvSelectDestino.innerHTML = "";

                dvNota.innerHTML = "";

                motivos = js_motivos[this.value];

                if ( !(operacion = this.value.toLowerCase()) )

                {

                    selectLocal1.value = "";

                    inputCantidad["disabled"] = true;

                    selectLocal1["disabled"] = true;

                    return;

                }

                esVenta = (operacion === opEgreso);

                /*if ( (esVenta = (operacion === opEgreso)) )

                {

                    motivos[0] = "Vencido";

                    motivos[1] = "Defectuoso";

                }*/

                if ( operacion !== "<?=Venta::tpTraspaso?>" )

                {

                    selectMotivo = `<label for="motivo">Motivo</label>`;

                    selectMotivo += `<select name='motivo' id='motivo' class="form-control">`;

                    selectMotivo += `<option value=""></option>`;

                    motivos.forEach(motivo => {

                        selectMotivo += `<option>${motivo}</option>`;

                    });

                    selectMotivo += `<option value='x'>Otro (Ingresar)</option>`;

                    selectMotivo += `</select>`;

                    dvSelectDestino.innerHTML = selectMotivo;

                    //dvNota.innerHTML = `<textarea placeholder="Observación" id="txt-nota" maxlength="400" name="nota" rows="3" class="form-control"></textarea>`;

                    (motivoSelect = document.getElementById('motivo')).onchange = function () {

                        thisValue = this.value.trim();

                        if ( thisValue === "x" )

                        {

                            this.value = "";

                            this.style["display"] = "none";

                            this.removeAttribute("required");

                            spnConvenio = `<span class="" id="spn-convenio">`;

                            spnConvenio += `<a style="position: absolute;right:15px;margin-top:2px" href='javascript:void(0)' onclick="motivoSelect.onchange()">Seleccionar</a>`;

                            spnConvenio += `<span class="input-group-addon">`;

                            spnConvenio += `<input class='form-control' name="${this.name}" id="in-concepto" maxlength="80" required/>`;

                            spnConvenio += `</span>`;

                            //spnConvenio += `<span class="input-group-addon" style="width:0.01%"><button type="button">OK</button></span>`;

                            spnConvenio += `</span>`;

                            this.name = "";

                            this.parentElement.insertAdjacentHTML("beforeend", spnConvenio);

                            document.getElementById('in-concepto').focus();

                            return;

                        }

                        this.removeAttribute("style");

                        this.name = "motivo";

                        this.setAttribute("required", true);

                        if ( (spnConvenio = document.getElementById('spn-convenio')) )

                        {

                            spnConvenio.remove();

                        }

                    };

                    /*document.getElementById('txt-nota').onkeyup = function (evt) {

                        long = parseInt(this.value.trim().length);

                        if ( long >= parseInt(this.getAttribute("maxmaxlength")) )

                        {

                            console.log(long)

                            return false;

                        }

                    };*/

                }

                dvNota.innerHTML = `<textarea placeholder="Observación" id="txt-nota" maxlength="400" name="nota" rows="3" class="form-control"></textarea>`;

                inputCantidad.removeAttribute("disabled");

                selectLocal1.removeAttribute("disabled");
                
                // Si es un ingreso, solo puede ser desde el negocio de la mitre
                if(operacion != 'ingreso'){
                    selectLocalOpts = `<option value=''>Seleccionar</option>`;
                    for (local in locales)
                    {

                        id_local = parseInt(local);

                        if ( esVenta && !pto_vta.includes(id_local) )

                        {

                            continue;

                        }

                        else if ( !esVenta && pto_vta.includes(id_local) )

                        {

                            //continue;

                        }

                        selectLocalOpts += `<option value="${local}">${locales[local]}</option>`;

                    }
                }else{
                    selectLocalOpts = `<option value="6">Negocio Mitre</option>`;

                }


               

                //$('label[for="accion"]').html(accion);

                //$('label[for="cantidad"]').html("Cantidad a " + accion);

                selectLocal1.innerHTML = selectLocalOpts;

                inputCantidad.focus();

            };

        }



        selectLocal1.onchange = function () {

            id_local = parseInt(this.value);

            if ( operacion === "<?=Venta::tpTraspaso?>" )

            {

                delete locales[id_local];

                /*-----*/

                selectDestinoHtml = `<label for="destino">Destino <i class="required"></i></label>`;

                selectDestinoHtml += `<select name="destino" id="destino" class="form-control" required>`;

                selectDestinoHtml += `<option value="">Seleccionar</option>`;

                for (local in locales)

                {

                    if ( !pto_vta.includes(parseInt(local)) )

                    {

                        continue;

                    }

                    selectDestinoHtml += `<option value="${local}">${locales[local]}</option>`;

                }

                selectDestinoHtml += `</select>`;



                dvSelectDestino.innerHTML = selectDestinoHtml;

                //selectAccion.onchange();

            }

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