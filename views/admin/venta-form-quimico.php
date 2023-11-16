<div class="row">
    <!--<div class="col-md-2"></div>-->
    <?php
        $es_compra = ($operacion == Venta::tpIngreso);
        $esTraspaso = ($operacion == "traspaso");
    ?>
    <div class="col-md-12" id="dv-venta-form-group">
        <div class="panel panel-info">
            <div class="panel-body">
            <div class="row">
                    <div class="form-group col-md-3">
                        <label for="fecha">Fecha:</label>
                            <input type="text" readonly="readonly" autocomplete="off" class="form-control" name="fecha" id="fecha" value="<?php echo date('d/m/Y'); ?>" />                    
                    </div>
                    <div class="form-group col-md-3">
                        <label for="in-direccion-envio">Direccion envio :</label>
                        <input type="text" id="in-direccion-envio" name="direccion" class="form-control" maxlength="20">                    
                    </div>
                    <div class="form-group col-md-3">
                        <label for="in-telefono">Telefono :</label>
                        <input type="tel" id="in-telefono" name="telefono" class="form-control" maxlength="20">                    
                    </div>
                    <div class="form-group col-md-3">
                        <label for="in-cadete">Cadete :</label>
                        <input type="text" id="in-cadete" name="cadete" class="form-control" maxlength="20">
                    </div>
                </div>
                <div class="row" id="group-articulo" style="position: relative">
                    <div id="stop"></div>
                    <div class="form-group col-md-3">
                        <label for="codigo">
                            C&oacute;digo
                        </label>
                        <input type="tel" id="in-codigo" name="codigo" class="form-control" maxlength="20">
                    </div>
                    <div class="col-md-5 form-group">
                        <label for="id_producto">
                            Producto:&nbsp;&nbsp;<a href="javascript:void(0)" id="aa-buscar">Buscar</a>
                        </label>
                        <?php
                        $aaRegistro = "<a href='javascript:void(0)' class='pull-right' id='aa-registro'>Registro</a>";
                        if ( $es_compra ) :
                            echo $aaRegistro;
                            ?>
                            <script>
                                aaRegistro = '';

                                aaRegistro.onclick = function (aref) {
                                    aref.preventDefault();
                                    get_modal_form({"opr": attrs["tipo_venta"]}, "!AdminArticulo/productoForm?fb");
                                };
                                $('[id^="modal-"]').on('hide.bs.modal', function () {
                                    if ( typeof articuloForm !== "undefined" )
                                    {
                                        frmJson = JSON.parse(articuloForm.getAttribute("rel") || '{}');
                                        if ( (_codigo = frmJson["codigo"]) )
                                        {
                                            inputCodigo.value = _codigo;
                                            selectProducto[0].insertAdjacentHTML("afterbegin", `<option value='${frmJson["ok"]}' selected>${frmJson["label"]}</option>`);
                                            inputCodigo.onkeypress({"which": 13});
                                        }
                                    }
                                });
                                
                            </script>
                        <?php endif; ?>
                        <select class="form-control" name="producto" minlength="0" id="id_producto">
                            <option value=""></option>
                            <?php foreach ($articulos as $articulo): ?>
                                <option value="<?= $articulo->id_producto ?>"><?= $articulo->nombre_producto ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p id="alerta" style="margin:0 0 -20px;font-size:12px;color:#ea8f29">&nbsp;</p>
                    </div>
                    <div class="col-md-4 form-group">
                        <?php if ( false ): ?>
                            <div class="input-group-addon">
                                <label for="iva">IVA</label>
                                <input type="tel" class="form-control" name="iva" id="iva">
                            </div>
                        <?php endif; ?>
                        <div class="input-group-addon">
                            <label for="precio">Precio</label>
                            <input type="tel" class="form-control" name="precio" id="precio" value="0">
                        </div>
                        <!--<div class="form-control" id="subtotal" style="width: 33%"></div>-->
                        <div class="input-group-addon" style="width:.45%">
                            <label for="cantidad">Cantidad</label>
                            <input type="tel" class="form-control" style="text-align:right" id="cantidad" value="1">
                        </div>
                        <div class="input-group-addon" style="width:.02%;padding-top:20px;text-align: right">
                            <a href="javascript:void(0)" id="btn-add" onclick="agregar()"><i class="fa fa-plus-square fa-2x"></i></a>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="modal-body" style="border-bottom:1px solid #eee">
                    <?= $linea_venta ?>
                    <h4 style="font-size:20px" id="h-total" class="text-right amount">Total $ <span id="h-total-span">0</span></h4>
                </div>
                <!--  -->
                <form id="frm-venta" action="!AdminVentaQuimicos/guardarVenta">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <input type="hidden" name="total_venta" id="total_venta" value="" class="form-control">
                            <?php if ( $clientes ): ?>
                                <label for="cliente">
                                    Seleccionar <?= ($es_compra ? "Proveedor" : "Cliente") ?>
                                    &nbsp;&nbsp;<a href="javascript:void(0)" id="aa-registrar">Registrar</a>
                                </label>
                                <select id="persona" name="cliente" minlength="0" class="form-control">
                                    <option value=""></option>
                                    <?php foreach ($clientes as $cliente) : ?>
                                        <option value="<?= $cliente->id ?>"><?= $cliente->label ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ( false ): ?>
                                    <div class="input-group-addon" style="text-align:right;width: .0001%">
                                        <a href="javascript:void(0)" id="aa-registrar"><i class="fa fa-plus-circle fa-2x"></i></a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div id="dv-totales" style="margin:0 0 35px"></div>
                        </div>
                        <div class="form-group col-md-2">
                            <?php if ( $operacion == MenuPanel::menuVentas ): ?>
                                <label for="tipo-venta" style="font-size:13px">Tipo Venta <i class="required"></i></label>
                                <select name="tipo_venta" id="tipo-venta" class="form-control" required>
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($tipos_venta as $tipo => $label) : ?>
                                        <option class="option-tipo-venta" value="<?= strtolower($label) ?>"><?= $label ?></option>
                                        <optgroup></optgroup>
                                    <?php endforeach; ?>
                                </select>
                            <?php else : ?>
                                <h4>&nbsp;</h4>
                            <?php endif; ?>
                        </div>
                        <div class="form-group col-md-4">
                                <label for="id_concepto">Tipo de pago <i class="required"></i></label>
                                    <select class="form-control" name="id_concepto" minlength="0" id="id_concepto">
                                        <option value="1" default>Efectivo</option>
                                        <option value="13">Transferencia</option>
                                        <option value="36">Tarjeta</option>
                                        <option value="5">Cta. Cte.</option>
                                </select>   
                        </div>
                        <input type="hidden" id="operacion" name="operacion" value="<?= $operacion ?: Venta::tpVenta ?>">
                    </div>
                    <div class="form-group text-center">
                        <input type="hidden" name="total" id="in-total">
                        <input type="hidden" id="hdn-id-venta" name="id_venta" value="<?= $venta->id_venta ?>">
                        <?php if ( $esTraspaso ): ?>
                            <div class="pull-left">
                                <a href="!AdminVenta/printList" target="_blank" id="aa-print"><i class="fa fa-file-pdf fa-2x"></i></a>
                            </div>
                        <?php endif; ?>
                        <button type="submit" id="btn-aceptar" class="btn btn-success">Finalizar</button>
                        <a href="<?= $panel_uri ?>/ventas" id="a-close" class="btn btn-default">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>  
</div>

<script type="text/javascript">

    let total = 0, cantidad = 1, stock = 0, es_online = false;
    let inputPrecio = document.getElementById('precio');
    const inputCodigo = document.getElementById('in-codigo');
    let selectProducto = $('#id_producto');
    const selectCliente = $('#persona');
    var direccion_envio;
    var telefono;
    var cadete;
    var id_concepto = 1;

    const fechaBanderaAdmin = document.getElementById('fecha');
    
    // const direccion_envio = document.getElementById("in-direccion-envio").value; // document.getElementById('in-direccion-envio');
    $("#in-direccion-envio").on('change keyup paste', function () {
        direccion_envio = document.getElementById("in-direccion-envio").value;
    });

    $("#in-telefono").on('change keyup paste', function () {
        telefono = document.getElementById("in-telefono").value;
    });

    $("#in-cadete").on('change keyup paste', function () {
        cadete = document.getElementById("in-cadete").value;
    });

    $("#id_concepto").on('change keyup paste', function () {
        id_concepto = document.getElementById("id_concepto").value;
    });
    
    const fechaUser16 = document.getElementById('fecha-user16');

    if(fechaBanderaAdmin !== null)
    {
        inputFecha = $('#fecha');
    }
    else
    {
        if(fechaUser16 !== null)
        {
            inputfechaUser16 = $('#fecha-user16');
        }
        else
        {
            inputFechaHoy = $('#fecha-hoy');
        }
    }

    const check_print = (document.getElementById('imprimir') || document.createElement("input"));
    const selectLocal = 6;
    let attrs = JSON.parse(sessionStorage.getItem("attr_<?=$operacion?>") || '{}');
    if ( (local = parseInt(selectLocal.value)) )
    {
        attrs["id_local"] = local;
    }
    attrs["operacion"] = document.getElementById('operacion').value;
    operacionTraspaso = (attrs["operacion"] === "<?=Venta::tpTraspaso?>");
    operacionCompra = (attrs["operacion"] === "<?=MenuPanel::menuCompra?>");

    if ( typeof dvLineasPago === "undefined" )
    {
        dvLineasPago = document.createElement('div');
    }
    if ( (tipoVentaSelect = document.getElementById('tipo-venta')) )
    {
        attrs["tipo_venta"] = '';
        if ( (tipoVta = "<?=$venta->tipo?>") )
        {
            attrs["tipo_venta"] = tipoVta.replace(/\w+_/g, "");
        }
        tipoVentaSelect.value = (attrs["tipo_venta"] || "");
        tipoVentaSelect.onchange = function () {
            attrs["tipo_venta"] = this.value.trim();
            get_total(true);
            get_total();
        };
    }
    else
    {
        attrs["tipo_venta"] = "<?=Articulo::prcMayorista?>";
        delete attrs["tv"];
    }

    if ( (esVenta = (attrs.operacion === "<?=MenuPanel::menuVentas?>")) )
    {
        //selectLocal.insertAdjacentHTML("beforeend", "<option value='<?=($localML = Local::ventaPagina)?>'><?=Local::nombreLocal($localML)?></option>");
    }
    
    if(fechaBanderaAdmin !== null)
    {
        inputFecha.click(function () {
            document.getElementById('ui-datepicker-div').style["z-index"] = 7;
            }).on("change", function () {
                attrs["fecha"] = this.value;
                historial_articulos();
        }).css("text-align", "right").calendario({"defaultDate": new Date(), "maxDate": new Date() ,"yearRange": "-1:+0"});

        inputFecha[0].value = (attrs["fecha"] || "");
    }
    else
    {

        if(fechaUser16 !== null)
        {
            var dosDiasAtras = new Date();
            dosDiasAtras.setDate(dosDiasAtras.getDate() - 2);

            inputfechaUser16.click(function () {
                document.getElementById('ui-datepicker-div').style["z-index"] = 7;
                }).on("change", function () {
                    attrs["fecha"] = this.value;
                    historial_articulos();
            }).css("text-align", "right").calendario({"defaultDate": new Date(), "maxDate": new Date(),minDate: new Date(dosDiasAtras),"yearRange": "-1:+0"});

            inputfechaUser16[0].value = (attrs["fecha"] || "");
        }
        else
        {
            var now = new Date();
            var day = ("0" + now.getDate()).slice(-2);
            var month = ("0" + (now.getMonth() + 1)).slice(-2);
            var today = (day)+"/"+(month)+"/"+now.getFullYear() ;
            
            inputFechaHoy[0].value = today;
            attrs["fecha"] = today;
            historial_articulos();
        }        
    }

    selectLocal.value = (parseInt(<?=intval($venta->id_sucursal)?>) || attrs["id_local"] || "");

    //----
    selectProducto.on("change select2:change", function () {
        if ( !this.value || inputCodigo.value )
        {
            return;
        }
        get_articulo(this.value);
    }).selectar();

    $(inputPrecio).decimal(".");

    selectCliente.val("<?=$venta->id_cliente?>").selectar();

    (inputRecibido = $('#recibido')).on("input keyup", function () {
        vuelto = parseFloat(this.value) - parseFloat(total);
        document.getElementById('cambio').innerHTML = (!isNaN(vuelto) ? vuelto.toFixed(2) : "0")
    }).decimal('.');

    inputCodigo.onkeypress = function (e) {
        if ( this.value && e.which === 13 )
        {
            get_articulo("", this.value);
        }
    };

    // ========================================
    // ========================================
    function get_articulo(id_articulo, codigo, _callback)
    {
        const labelPre = document.querySelector('label[for="id_producto"]');
        const elmAlerta = document.getElementById('alerta');
        let _post = {
            "id_producto": parseInt(id_articulo || 0),
            "codigo": (codigo || ""),
            "id_local": 6,
            "tp": (attrs["tipo_venta"].toLowerCase() || "")
        };
        if ( _post.id_producto || codigo )
        {
            labelPre.insertAdjacentHTML("beforeend", "<i class='fa fa-spin fa-spinner'></i>");
            //elmAlerta.innerHTML = " ";
            set_notice();
            fetch('!AdminVenta/getProducto', {
                "method": "POST",
                "body": new URLSearchParams(_post)
            }).then(function (res) {
                res.json().then(function (response) {

                    labelPre.lastElementChild.remove();
                    let id_producto = response["id_producto"];
                    stock = (response.stock || 0);
                    let nombre = response.producto;
                    if ( !id_producto )
                    {
                        set_notice("codigo", "No se encontró producto");
                        //$('#group-articulo .small').html("No se encontró producto");
                        return;
                    }
                    //----
                    inputPrecio.setAttribute("rel", linea = (id_producto + " - " + nombre));
                    if ( !(inputPrecio.value = (response.precio || "")) )
                    {
                        inputPrecio.removeAttribute("readonly");
                    }

                    if ( _post.codigo && id_producto )
                    {
                        selectProducto.val(id_producto).trigger("change");
                    }
                    inputCodigo.value = response.codigo;
                    //elmAlerta.innerHTML = response.alerta;
                    if ( typeof _callback === "function" )
                    {
                        _callback();
                        return;
                    }
                    document.getElementById('cantidad').focus();
                });
            });
            return;
        }
        agregar();
    }

    $('#cantidad').attr('maxLength', 4).on('keypress', function (e) {
        if ( this.value && e.which === 13 )
        {
            agregar();
        }
    });


    // ========================================
    // Agrega una linea venta
    // ========================================
    function agregar()
    {
        cantidad = document.getElementById('cantidad').value;
        articulo = inputPrecio.getAttribute("rel");
        
        let _post = {
            "cantidad": cantidad,
            "id_producto": selectProducto.val(),
            "cup": inputCodigo.value,
            "op_nro": "<?=$venta->id_venta?>",
            "producto": articulo,
            "subtotal": cantidad * parseFloat(inputPrecio.value),
            //'tipo_venta': tipo_venta
        };

        if ( _post.id_producto )
        {
            if ( isNaN(_post["subtotal"]) || !_post.subtotal )
            {
                //inputPrecio.value = "";
                jdialog("El producto " + _post.producto + " no tiene un precio asignado.", function () {
                    inputPrecio.focus();
                });
                return;
            }
            if ( (!es_online && !operacionCompra) && (stock < cantidad) )
            {
                jdialog("No hay stock suficiente de " + _post.producto + " (" + stock + " un)");
                inputCodigo.value = "";
                inputPrecio.value = "";
                return;
            }
            //agregar la linea en sesion

            get_linea_venta(_post);
            //setLS('attr', JSON.stringify({"tipo_venta": $('#tipo_venta').val(), "imprimir": check_print.is(':checked')}));
            if ( attrs && !attrs.imprimir )
            {
                check_print.removeAttribute("checked");
            }
            selectProducto.val("").trigger("change");
        }
        inputPrecio.removeAttribute("rel");
        inputPrecio.value = 0;
        stock = 0;
        //inputPrecio.setAttribute("readonly", true);
        document.getElementById('cantidad').value = 1;
        inputCodigo.value = "";
        inputCodigo.focus();
    }

    if(document.querySelector('[rel="table-body"]'))
    {
        tbodyLv = document.querySelector('[rel="table-body"]');
    }else{
        tbodyLv = '';
    }

    // ========================================
    // 
    // ========================================
    function get_linea_venta($params = {})
    {
        //before_send();
        //tableBody = document.querySelector('[rel="table-body"]');
        tbodyLv.innerHTML = "<tr><td class='text-center' colspan='3'><i class='fa fa-spin fa-spinner'></i></td></tr>";
        frm = new FormData();
        $params["operacion"] = attrs["operacion"];
        for (param in $params)
        {
            frm.append(param, $params[param]);
        }
        fetch("!AdminVentaQuimicos/agregarLinea", {
            "method": "POST",
            "body": frm//new URLSearchParams($params || {})
        }).then(function (xhr) {
            xhr.text().then(function (rows) {
                tbodyLv.innerHTML = rows;
                get_total();
            });
        });
    }

    if ( (aaPrint = document.getElementById('aa-print')) )
    {
        /* aaPrint.onclick = function (evt) {
             evt.preventDefault();
             this.href = "javascript:void(0)";
             if ( total > 0 )
             {
                  this.href = "!AdminVenta/printList";
                  this.setAttribute("target", "_blank");

             }
         }*/
    }

    // ========================================
    // 
    // ========================================
    function seleccionar($cup, update = false)
    {
        let split = $cup.split("&");
        let parent = split[0], id = (split[1] || parent);
        style = [
            "width:auto",
            "max-width:80%",
            "padding:10px 10px 20px",
            "position: absolute",
            "z-index: 5",
            "background:#3c3735",
            //"border: 1px solid #fff",
            "border-radius: 5px",
            "top: 35%",
            "left: 0",
            "right: 0",
            "margin-left: auto",
            "margin-right: auto",
        ];
        rel = (trw = document.getElementById(parent)).getAttribute("rel").split("&");
        form = `<tr id='${(dvFrm = `tr-frm-${parent}`)}'>`;
        form += `<td style='${style.join(";")}'>`;
        form += `<div style="padding-bottom:8px;text-align: left">`;
        form += `<button type="button" class="btn btn-danger" id="dv-frm-close"><b>X</b></button>`;
        form += `</div>`;
        form += `<form id="frm-line-opt" autocomplete="off">`;
        //form += `<h4>${trw.cells[0].innerHTML}</h4>`;
        form += "<div class='input-group-addon'>";
        form += `<label for='cnt' style='${lblCss = "color:#fff"}'>Cantidad</label>`;
        form += `<input type='tel' maxlength='3' name="cnt" id='cnt' class='form-control' value='${(rel[1] || "")}' required>`;
        form += "</div>";
        form += "<div class='input-group-addon'>";
        form += `<label for='cnt' style='${lblCss}'>Precio</label>`;
        form += `<input type='tel' id='sbt' name="prc" class='form-control' value='${rel[0]}' required>`;
        form += "</div>";
        form += "<div class='input-group-addon' style='width:.05%;text-align: right;padding-top:15px'>";
        form += "<button id='btn-frm-submit' class='btn btn-success'><i class='fa fa-check'></i></button>";
        form += `<input type='hidden' name='id_linea' value="${id}"/>`;
        form += `<input type='hidden' name='id_local' value="${attrs.id_local}"/>`;
        form += "</div>";
        form += "</form><br/>";
        form += "</td>";
        form += "</tr>";
        document.querySelectorAll('[id^="tr-frm"]').forEach(function (frm) {
            frm.remove();
        });

        setTimeout(function () {
            trw.parentElement.insertAdjacentHTML("afterbegin", form);
            $('#cnt').focus().numeric();
            $('#sbt').decimal(".");
            (dvFrmClose = document.getElementById('dv-frm-close')).onclick = function () {
                document.getElementById(dvFrm).remove();
            };

            document.getElementById("frm-line-opt").onsubmit = function (e) {
                e.preventDefault();
                if ( update )
                {
                    this.action = "!AdminVenta/eliminarLinea";
                    submit_form(this, function (rsp) {
                        dvFrmClose.click();
                        get_linea_venta();
                    });
                    return;
                }
                inputCodigo.value = id;
                inputCodigo.onkeypress({"which": 13});
                setTimeout(function () {
                    document.getElementById('cantidad').value = document.getElementById('cnt').value;
                    document.getElementById('precio').value = document.getElementById('sbt').value;
                    document.getElementById('btn-add').click();
                    //agregar();
                    dvFrmClose.click();
                }, 400);
            };
        }, 200);
    }

    // ========================================
    // Eliminar una linea venta
    // ========================================
    function eliminar(id)
    {
        jconfirm(function (st) {
            if ( st )
            {
                fetch(`!AdminVentaQuimicos/eliminarLinea?id=${id}`);
                document.getElementById(id).remove();
                get_total();
            }
        }, "Eliminar esta linea. ¿Continuar?")
    }

    document.getElementById('records_count').remove();
    $('#column-1, #column-3').addClass("col-md-2 text-center");

    // ========================================
    // 
    // ========================================
    function get_total(stg = false)
    {
        if ( stg )
        {
            sessionStorage.setItem("attr_<?=$operacion?>", JSON.stringify(attrs));
            return;
        }
        //---
        total = 0;

        var disabled = false;
        //---

        if ( !document.getElementById('dt-empty') )
        {
            Array.from(tbodyLv.children).forEach(function (trow) {
                //subtotal = parseFloat($(val).find('td:eq(2)').text());
                if ( typeof (cell = trow.cells[2]) !== "undefined" )
                {
                    subtotal = parseFloat(cell.innerText);
                    total += subtotal;
                }
            });
        }
        //#--
        if ( Math.abs(total) <= 0 )
        {
            tbodyLv.innerHTML = "<tr id='tr-empty'><td colspan='5' id='dt-empty' align='center'>A&uacute;n no se agregaron &iacute;tems</td></tr>";
            disabled = true;
        }
        //-- 26/11/2020
        document.querySelectorAll('[rel="new"]').forEach(function (aa) {
            aa.remove();
        });
        
        if ( (esPresupuesto = (attrs["tipo_venta"] === "<?=strtolower(Venta::presupuesto)?>")) )
        {
            aaNvaLineaHtml = "<a id='aa-nva-linea' rel='new' style='float: left;font-size:24px' href='javascript:void(0)'><i class='fa fa-plus-circle'></i></a>";
            //document.querySelector('[for="id_producto"]').insertAdjacentHTML("afterend", aaNvaLineaHtml);
            document.getElementById('h-total').insertAdjacentHTML("afterbegin", aaNvaLineaHtml);
            if ( (aaNvaLinea = document.getElementById('aa-nva-linea')) )
            {
                aaNvaLinea.onclick = function (evt) {
                    evt.preventDefault();
                    if ( (tr = document.getElementById('tr-empty')) )
                    {
                        tr.remove();
                    }
                    trId = new Date().getTime();
                    newTr = `<tr id="${trId}" rel="new">`;
                    newTr += `<td class="text-center col-md-2">`;
                    newTr += `<input type="tel" name="cnt" id="cnt-${trId}" placeholder="Cantidad" class="form-control">`;
                    newTr += `</td>`;
                    newTr += `<td><input type="text" id="prd-${trId}" placeholder="Artículo" name="prd" class="form-control"></td>`;
                    newTr += `<td class="col-md-2">`;
                    newTr += `<div class="input-group">`;
                    newTr += `<input type="tel" name="sbt" id="sbt-${trId}" placeholder="Precio Un." class="form-control">`;
                    newTr += `<span class="input-group-addon">`;
                    newTr += `<a href="javascript:void(0)" style="" id="btn-${trId}"><i class="fa fa-check-circle"></i></a>`;
                    newTr += `</span>`;
                    newTr += `</div>`;
                    newTr += `</td>`;
                    newTr += `</tr>`;
                    if ( !document.querySelector('tr[rel="new"]') )
                    {
                        tbodyLv.insertAdjacentHTML("beforeend", newTr);
                        $(inputCnt = document.getElementById(`cnt-${trId}`)).focus().numeric();
                        $(inputSbt = document.getElementById(`sbt-${trId}`)).decimal(".");
                        let postValues = {};
                        $(selectPrd = document.getElementById(`prd-${trId}`)).autocomplete({
                            "maxShowItems": 15,
                            "source": function (req, response) {
                                fetch(`!AdminVenta/getProducto?hdn=1&tp=${attrs["tipo_venta"]}`, {
                                    "method": "POST",
                                    "body": new URLSearchParams(req)
                                }).then(function (xhr) {
                                    xhr.json().then(function (result) {
                                        response(result);
                                    });
                                })
                            },
                            "minLength": 3,
                            "select": (afterAutocomplete = function (event, ui) {
                                if ( (data = ui['item']) === null )
                                {
                                    return;
                                }
                                inputSbt.value = data["precio"];
                                postValues["cup"] = data["codigo"];
                                postValues["id_producto"] = data["id"];
                                postValues["producto"] = data["value"];
                            }),
                            "focus": afterAutocomplete,
                            "change": afterAutocomplete
                        }).keyup(function () {
                            if ( !this.value.trim().length )
                            {
                                inputSbt.value = "";
                            }
                        });

                        if ( (btnAddLine = document.getElementById(`btn-${trId}`)) )
                        {
                            btnAddLine.onclick = function (ee) {
                                ee.preventDefault();
                                if ( !(cntd = parseInt(inputCnt.value)) )
                                {
                                    set_notice("cnt", "Ingresar Cantidad");
                                    return;
                                }
                                if ( !postValues["producto"] && (selectPrd.value.trim().length < 8) )
                                {
                                    set_notice("prd", "Buscar o ingresar el nombre del Artículo");
                                    return;
                                }
                                if ( !(precioUn = parseFloat(inputSbt.value)) )
                                {
                                    set_notice("sbt", "Ingresar Precio unitario");
                                    return;
                                }
                                if ( !postValues["id_producto"] )
                                {
                                    postValues["id_producto"] = trId;
                                    postValues["producto"] = selectPrd.value;
                                }
                                postValues["cantidad"] = cntd;
                                postValues["hdn"] = 1;
                                postValues["subtotal"] = (postValues["cantidad"] * precioUn);
                                get_linea_venta(postValues);
                            };
                        }
                    }
                }
            }
        }
        //--
        
        //inputRecibido.attr("disabled", disabled).trigger('input');
        labelTotal = document.getElementById('h-total');
        labelTotal.querySelector('span').innerText = total.toFixed(2);
        
        if ( operacionTraspaso )
        {
            if ( operacionTraspaso &&  fechaBanderaAdmin !== null)
            {
                inputFecha[0].value = "<?=date('d/m/Y')?>";
                inputFecha[0].setAttribute("disabled", true);
                labelTotal.style["display"] = "none";
                //document.getElementById('dv-nro-factura').innerHTML = "";
            }
            else
            {
                inputFechaHoy[0].value = "<?=date('d/m/Y')?>";
                // inputFecha[0].setAttribute("disabled", true);
                labelTotal.style["display"] = "none";
            }
        }
        //--
        document.getElementById('in-total').value = total;
        //dvLineasPago.innerHTML = "";
       
        return total;
    }

    // ========================================
    // 
    // ========================================
    function historial_articulos(pagina)
    {
        if(document.getElementById('tb-historial'))
        {
            tbody.innerHTML = "<tr><td colspan='3' align='center'><i class='fa fa-spin fa-spinner'></i></td></tr>";
            if ( !attrs["tv"] && esVenta )
            {
                attrs["tv"] = "publico";
            }
            get_total(true);
            attrs["p"] = (pagina || 1);
            attrs["exc"] = document.getElementById('hdn-id-venta').value;
            fetch("!AdminVenta/historialArticulos", {
                "method": "POST",
                "body": new URLSearchParams(attrs)
            }).then(function ($res) {
                $res.json().then(function (res) {
                    $rows = res["rows"];

                    tbody.innerHTML = $rows;
                    if ( (ppTotales = document.getElementById('pp-totales')) )
                    {
                        ppTotales.style["display"] = "none";
                        document.getElementById('dv-totales').innerHTML = ppTotales.innerHTML;
                    }
                    if ( (tableOpts = document.getElementById(dvId = 'dv-table-opts')) )
                    {
                        tableOpts.remove();
                    }
                    //------
                    theOptions = "";
                    if ( (numRows = parseInt(document.querySelector('[id^="td-count"]').id.replace(/[^\d+]/gi, ""))) )
                    {
                        theOptions += `<a class="h3 text-info" href='!AdminVenta/exportar?pdf=${attrs['operacion']}' target="_blank"><i class='fa fa-file-pdf'></i></a>&nbsp;&nbsp;`;
                    }
                    //--
                    if ( esVenta )
                    {
                        theOptions += `<input id='in-search' type='text' autocomplete="off" placeholder="Apellido, Nombre o DNI"/>`;
                        theOptions += "<select name='tv' id='select-tv'>";
                        (<?=json_encode($tipos_venta)?>).forEach(function (tv) {
                            radioId = tv.toLowerCase();
                            //theOptions += `<label for="${radioId}"><input id="${radioId}" type="radio" name="tv"> ${tv}</label>&nbsp;&nbsp;`;
                            theOptions += `<option value="${radioId}">${tv}</option>`;
                        });
                        theOptions += "</select>&nbsp;&nbsp;";
                    }
                    theOptions += `<select id='horario' name='horario'>`;
                    for (turno in (horario = {"00&23": "Todo", "00&13": "Mañana", "14&23": "Tarde"}))
                    {
                        theOptions += `<option value="${turno}">${horario[turno]}</option>`;
                    }
                    theOptions += `</select><label for="allUsers">`;
                    theOptions += `<input type="checkbox" id="allUsers"/> Todos los usuarios</label>`;
                    //theOptions += numRows;
                    tableOpts = document.createElement("div");
                    tableOpts.id = dvId;
                    //tableOpts.style["position"] = "";
                    //tableOpts.style["top"] = "-28px";
                    tableOpts.innerHTML = theOptions;
                    document.getElementById('tbl-linea-venta').insertAdjacentElement("beforebegin", tableOpts);
                    if ( (tvSelect = document.getElementById('select-tv')) )
                    {
                        tvSelect.value = attrs["tv"];
                        tvSelect.onchange = function () {
                            attrs["tv"] = this.value.toLowerCase();
                            historial_articulos();
                        };
                    }

                    if ( (inSearch = document.getElementById('in-search')) )
                    {
                        inSearch.value = (attrs["search"] || "");
                        inSearch.onkeyup = function (evt) {
                            txt = this.value.trim();
                            //if ( txt.length > 2 || !txt )
                            if ( (evt.keyCode === 13) || !txt )
                            {
                                attrs["search"] = txt;
                                historial_articulos();
                            }
                        };

                    }

                    if ( (selectHorario = document.getElementById('horario')) )
                    {
                        selectHorario.value = (attrs["horario"] || selectHorario.options[0].value);
                        selectHorario.onchange = function () {
                            attrs["horario"] = this.value;
                            historial_articulos();
                        };
                    }

                    if ( (checkAllUsers = document.getElementById('allUsers')) )
                    {
                        checkAllUsers.checked = attrs["allUsers"];
                        checkAllUsers.onclick = function () {
                            attrs["allUsers"] = Number(this.checked);
                            historial_articulos();
                        };
                    }

                    attrs["block"] = res["blocked"];
                    selectLocal.onchange();
                });
            });
        }
       
    }

    // ========================================
    // 
    // ========================================
    function dt_paginate(ref)
    {
        var page = ref.getAttribute('href').replace(/.*=/g, '');
        ref.setAttribute('href', 'javascript:void(0)');
        historial_articulos(page);
        return false;
    }

    if ( (frmVenta = document.getElementById('frm-venta')) )
    {
        enter_count = 0;

        // ========================================
        // 
        // ========================================
        function finalizar_venta()
        {
            const _form = frmVenta;

            clienteSeleccionado = document.getElementById('select2-persona-container')
            //var msg = !select_cliente.val() ? "No se ha seleccionado un Cliente. ¿Continuar de todas formas?" : "Finalizar y guardar venta. ¿Continuar?";

            if ( Math.abs(total) <= 0 )
            {
                /*jdialog("Para continuar debe agregar &iacute;tems a la venta.", function () {
                    enter_count = 0;
                });*/
                return;
            }
            set_notice();
            // if ( !selectLocal.value )
            // {
            //     set_notice("id_local", "Seleccionar un elemento");
            //     return;
            // }

            inputFactura = (document.getElementById('factura') || document.createElement('input'));
            attrs["factura"] = inputFactura.value;
            // if ( operacionTraspaso && !(attrs["destino"] = document.getElementById('destino').value) )
            // {
            //     set_notice("destino", "Seleccionar un local");
            //     return;
            // }
            //--

            if ( !attrs["fecha"] )
            {
                set_notice("fecha", "Indicar la fecha de operación");
                return;
            }

            //--
            if ( typeof pc_pagos === "undefined" )
            {
                pc_pagos = {};
            }
            //--

            let personaRequerida = (
                (attrs.tipo_venta === "presupuesto") || operacionCompra ||
                Object.keys(pc_pagos).includes("<?=Concepto::itemDeudaCliente?>")
            );
            if ( !operacionTraspaso )
            {
                cteMsg = "<?=$persona?> invalido, seleccionar un <?=$persona?>";
                esCtaCte = false;
                Object.keys(pc_pagos).forEach(function (id_cta) {
                    esCtaCte = (id_cta.replace(/.+&/, "") === "<?=Concepto::cuentaCorriente?>");
                });

                if ( esCtaCte && !parseInt(selectCliente[0].value) || isNumeric(clienteSeleccionado.title))
                {
                    jdialog(cteMsg + " de la lista o registrarlo");
                    return;
                }
                else if ( !selectCliente[0].value.trim() )
                {
                    jdialog(cteMsg);
                    return;
                }
                //--
            }
            //return;
            attrs["cadete"] = cadete;
            attrs["direccion_envio"] = direccion_envio;
            attrs["telefono"] = telefono;
            attrs["id_concepto"] = id_concepto;

            hdnPago = document.createElement("input");
            hdnPago.type = "hidden";
            hdnPago.name = "venta";
            hdnPago.value = JSON.stringify(Object.assign(attrs, {"pagos": pc_pagos}));
            _form.append(hdnPago);

                total_pago = document.getElementById('h-total-span').innerText;
                document.getElementById('h-total-span').value = total_pago;

                submit_form(_form,function (data) {

                    if ( data.ticket && check_print.checked )
                    {
                        $('body').append(data.ticket);
                        imprimir(function () {
                            location.reload();
                        });
                        return;
                    }

                    enter_count = 0;
                    selectCliente.val("").trigger("change");
                    inputFactura.value = "";
                    document.getElementById('hdn-id-venta').value = attrs["exc"] = "";
                    history.pushState({}, document.title, document.URL.replace(/\/\d+$/g, ""));
                    get_linea_venta();

                    historial_articulos();
                    if ( es_online )
                    {
                        control_incidencias();
                    }
                    //location.reload();
                });
                hdnPago.remove();
            }

        frmVenta.onsubmit = function (evt) {
            evt.preventDefault();
            finalizar_venta();
        };
    }

    //---
    //get_total();
    historial_articulos();
    get_total();
    modalDlg = document.querySelector('.modal-dialog');
    document.getElementById('aa-buscar').onclick = function () {
        set_notice();
        if ( typeof tipoVentaSelect !== "undefined" && !attrs["tipo_venta"] )
        {
            set_notice("tipo_venta", "Seleccionar!");
            return;
        }
        modalDlg.classList.add("modal-lg");
        get_modal_form({"mdl": attrs["tipo_venta"].toLowerCase() + "*" + attrs["id_local"]}, '!FrontArticulo/listaPrecios')
    };

    if ( (addLink = document.getElementById('aa-registrar')) )
    {
        addLink.onclick = function (href) {
            href.preventDefault();
            modalDlg.classList.remove("modal-lg");
            get_modal_form({"mdl": "<?=$es_compra ? 'proveedor' : 'cliente'?>*persona"}, "!AdminRegistro/modalForm");
        };
    }

    function isNumeric(value) {
        return value.match(/^[0-9]+$/) != null;
    }
</script>