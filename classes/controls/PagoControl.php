<?php

class PagoControl

{

    private static $_cuentas = array();



    public static function pagoForm($pagos = array(), $exc = array())

    {

        static::cuentas($exc);

        ob_start();

        ?>

        <style>

            .linea-pago {

                margin-bottom: 3px;

            }



            .linea-pago .input-group-addon {

                margin-top: 0;

                padding: 0 !important;

            }



            .linea-pago select, .linea-pago input {

                font-size: 13px;

            }

        </style>

        <div id="dv-pc-pago">

            <label style="margin:0; text-align: right">$ <span id="pc-spn-total"><?= array_sum(array_values($pagos)) ?></span></label>

                <div id="dv-lineas-pago" style="min-height:200px;overflow-y:auto"></div>

            <input type="hidden" id="pc-pago" name="pc_pago"/>

        </div>


        <script>
            dvLineasPago = document.getElementById('dv-lineas-pago');
            labelMontoCompra = document.getElementById('pc-spn-total');

            total_pago = 0;
            var pc_pagos = {}, pc_cuentas = <?=json_encode(static::$_cuentas)?>;

            // =================================================================
            // Pago total - 
            // =================================================================

            function pg_total(eval)
            {

                //get_cuentas();

                if ( eval )
                {
                    total_pago = parseFloat(labelMontoCompra.innerText);
                }
                else
                {
                    labelMontoCompra.innerText = total_pago.toFixed(2);
                }

                let js_pagos = {};

                for (let k in pc_pagos)
                {
                    console.log('k::: ', k);

                    cta_id = k.replace(/.+&/g, "");

                    importe = parseFloat(pc_pagos[k]);

                    if ( typeof js_pagos[cta_id] === "undefined" )
                    {
                        js_pagos[cta_id] = 0;
                    }

                    js_pagos[cta_id] += importe;

                }

                document.getElementById('pc-pago').value = JSON.stringify(js_pagos);

                return dvLineasPago.querySelectorAll('[id^="btn-"]').length;

            }


            // =================================================================
            // Carga un nuevo tipo de pago con su importe
            // =================================================================

            function opcion_pago(id_cuenta, importe)
            {

                id = new Date().getTime();
                //pg_total(true);
                line = `<div class="linea-pago" id='dv-${id}'>`;

                line += `<div class='input-group-addon' id="sc-group-${id}">`;

                //line += `<label for="cuenta-${id}">Cuenta <i class="required"></i></label>`;

                line += `<select id="cuenta-${id}" rel="cuenta" name="c-${id}" class='form-control' required>`;

                line += "<option value=''>Medio</option>";

                for (id_cta in pc_cuentas)
                {

                    switch (id_cta) {

                        case '1':
                            var color = 'red';
                        break;

                        case '8':
                            var color = 'purple';
                        break;

                        case '13':
                            var color = 'maroon';
                        break;

                        case '17':
                            var color = 'silver';
                        break;

                        case '36':
                            var color = 'gray';
                        break;

                        case '37':
                            var color = 'olive';
                        break;

                        case '38':
                            var color = 'lime';
                        break;

                        case '39':
                            var color = 'white';
                        break;

                        default:
                            var color = '';
                        break;

                    }

                    if ( pc_pagos[id_cta] )

                    {

                        //continue;

                    }

                    line += `<option style="background-color:${color}; color:white;" value='${id_cta}' ${(id_cta === id_cuenta) ? "selected" : ""}>${pc_cuentas[id_cta]}</option>`;

                }

                line += `</select>`;

                line += `</div>`;

                line += `<div class='input-group-addon' style="width: .4%;">`;

                //line += `<label for="monto-${id}">Monto <i class="required"></i></label>`;

                line += `<input type="tel" name="m-${id}" value="${importe}" class="form-control" id="monto-${id}" placeholder="Monto" disabled required>`;

                line += `</div>`;

                line += `<div class="input-group-addon" style="padding-top:26px;width:.04%;text-align: right">`;

                line += `<button type="button" id="btn-${id}" class="btn btn-primary" title="Aceptar"><i class="fa fa-check"></i></button>`;

                line += `</div>`;

                line += `</div>`;

                dvLineasPago.insertAdjacentHTML("beforeend", line);

                cuentaSelect = document.getElementById(`cuenta-${id}`);

                montoInput = document.getElementById(`monto-${id}`);

                $(montoInput).numeric(".");

                cuentaSelect.onchange = function () {

                    if ( !this.value )
                    {
                        console.log("pasa linea 227 - pagocontrol")
                        montoInput.setAttribute("disabled", true);
                        return;
                    }

                    montoInput.removeAttribute("disabled");

                    montoInput.focus();

                };

                if ( (btn = document.getElementById(`btn-${id}`)) )
                {


                    btn.onclick = function () {

                        //cuentaSelect.value = id_cuenta

                        set_notice();

                        if ( !(cuenta = cuentaSelect.value) )
                        {
                            set_notice(`c-${id}`, "Seleccionar Cuenta");
                            return;
                        }

                        //--

                        if ( !(monto = parseFloat(montoInput.value)) )
                        {
                            set_notice(`m-${id}`, "Indicar el monto");
                            return;
                        }

                        //--

                        if ( !pc_pagos[id + "&" + cuenta] )
                        {
                            var palabraClave = "/form";

                            if (window.location.href.indexOf(palabraClave) !== -1) {
                                labelMontoCompra = document.getElementById('h-total-span');
                            } 
                            

                            total_pago = parseFloat(labelMontoCompra.innerText);
                            in_monto = parseFloat(monto);


                            if ( in_monto > total_pago )
                            {
                                in_monto = total_pago;
                            }

                            montoInput.value = in_monto;
                            pc_pagos[id + "&" + cuenta] = in_monto;
                            total_pago -= in_monto;


                            pg_total();



                            /*--*/

                            cuentaSelect.setAttribute("disabled", true);

                            console.log("pasa 295 - pagoControl")
                            montoInput.setAttribute("disabled", true);

                            btn.innerHTML = `<i class="fa fa-trash"></i>`;

                            btn.setAttribute("rel", id.toString().replace(/[^\d+]/, ""));
                            btn.setAttribute("onclick", `quitar(${btn.getAttribute("rel")})`);
                            btn.id = "";

                            btn.classList.remove("btn-success");

                            btn.classList.add("btn-danger");


                            if ( !id_cuenta && total_pago > 0 )

                            {

                                opcion_pago();

                            }

                        }

                    };

                    if ( total_pago <= 0 && !id_cuenta )

                    {

                        //quitar(id);

                    }



                    if ( id_cuenta && importe )

                    {

                        btn.click();

                    }

                }

            }

            function quitar($id)
            {

                cuenta = document.getElementById(`cuenta-${$id}`).value;
                if ( (sJson = pc_pagos[$id + "&" + cuenta]) )
                {
                    total_pago += parseFloat(sJson);
                    delete pc_pagos[$id + "&" + cuenta];
                    rtn = pg_total();
                }
                document.getElementById(`dv-${$id}`).remove();
                if ( (total_pago > 0) && !rtn )
                {
                    opcion_pago();
                }
            }

            function refrescar_variables_pago_control()
            {
                console.log('refrescar_variables_pago_control::: ');
                total_pago += parseFloat(sJson);
                delete pc_pagos[$id + "&" + cuenta];
                rtn = pg_total();
            }


            //opcion_pago();
        </script>

        <?php

        $block = ob_get_clean();

        if ( $json )

        {

            //HArray::jsonResponse('body', $block);

        }

        return $block;

    }



    private static function cuentas($excluir = array())

    {

        //$excluir = json_decode($_POST['exc'], true);

        //$excluir[] = 0;

        $cuentas = Concepto::selectRaw("id_concepto AS id, `concepto` AS label")->whereRaw("`categoria`='disponibilidad' AND `visible`='1' AND `id_concepto` NOT IN ('" . implode("','", $excluir) . "')")->get();

        $result = array();

        foreach ($cuentas as $cuenta)

        {

            static::$_cuentas[$cuenta->id] = mb_strtoupper($cuenta->label);

        }

    }

}