<?php

//ini_set("display_errors", "On");

class FrontCart extends FrontMain

{

    const sesCart = "tmpCart";

    const emptyCartMsg = "Aún no se agregaron artículos a la compra";



    public function __construct()

    {

        parent::__construct();

        $this->setParams('modalBlock', $this->modalBlock());

    }



    public function index()

    {

        $this->setPageTitle("Detalle de compra");

        $lista = new HDataTable();

        $lista->setDisableFunciones();

        $lista->setColumns(["Nombre", "Precio Un", "Cantidad.text-center", "Subtotal", "&nbsp;"]);

        $lista->setHideDateRange();

        $lista->setHideSearchBox();

        $lista->setDataSource(self::class . "/getItemsCart");

        $lista->setRows($items = $this->getItemsCart(true));

        $params = array(

            //'cart_table' => $lista->drawTable(),

            'emptyCartMsg' => self::emptyCartMsg,

            'itemsCart' => $this->getItemsCart(true)

        );

        $this->setParams($params);

        $this->setBody("front-cart-index");

    }



    protected function setSessionCart($item = array(), $overwrite = false)

    {

        $items = array_merge($item, $this->getSessionCart());

        if ( $overwrite )

        {

            $items = $item;

        }

        //HArray::varDump($items);

        HSession::setSession(self::sesCart, $items);

    }



    protected function getSessionCart()

    {

        return (array)HSession::getSession(self::sesCart);

    }



    public function setItemCart()

    {

        $id_producto = trim($_POST['cup']);

        $cantidad = intval($_POST['cantidad']);

        $item = array();

        #--

        if ( $_POST['directa'] )

        {

            $this->setSessionCart(array(), true);

        }

        if ( $id_producto )

        {

            $lineas = $this->getSessionCart();

            $existe = key_exists($id_producto, $lineas);

            $lineas[$id_producto] += $cantidad;

            #--

            $this->setSessionCart($lineas, true);

            HArray::jsonSuccess();

        }

        HArray::jsonError("Ha ocurrido un error desconocido.");

    }



    public function getItemsCart($return = false)

    {

        $total = $monto = $i = 0;

        $rows = $no_envio = null;

        $dim = array();

        //$this->setSessionCart(array(), true);

        $items = $this->getSessionCart();

        krsort($items);



        foreach ($items as $key => $cantidad)

        {

            $last_item = (($i + 1) >= count($items));

            #-- 02/06/2021

            $ids_articulo = explode("_", $key);

            $id_producto = $ids_articulo[0];

            unset($ids_articulo[0]);

            #--

            $articulo = Articulo::find($id_producto);

            $dimension = $articulo->dimension_array;

            if ( $dimension['alto'] && $dimension['ancho'] )

            {

                if ( $dimension['alto'] > $dim[0] )

                {

                    $dim[0] = $dimension['alto'];

                }

                $dim[1] += ($dimension['ancho'] * $cantidad);

                $dim[2] += ($dimension['largo'] * $cantidad);

                $dim[3] += ($dimension['peso'] * $cantidad);

                $dimensiones = "{$dim[0]}x{$dim[1]}x{$dim[2]},{$dim[3]}";

            }

            #--

            if ( $last_item && !$dimensiones )

            {

                $dimensiones = "28x25x25,1200";

            }

            #--

            $total += $cantidad ?: 1;            

            $monto += ($subtotal = floatval($pu = $articulo->ecommerce_precio) * $cantidad);

            if ( $id_producto == 4669 )
            {
                $monto = '19900';
                $subtotal = '19900';
            }

            if ( $id_producto == 4678 )
            {
                $monto = '9900';
                $subtotal = '9900';
            }

            if ( $id_producto == Articulo::shippingCost )

            {

                $cantidad = 1;

                if ( ($monto - $subtotal) >= self::montoEnvioGratis )

                {

                    $monto -= $subtotal;

                    $pu = $subtotal = $cantidad = 0;

                }

            }

            if ( $id_producto == 4669 )
            {
                $pu = '19900';
            }

            if ( $id_producto == 4678 )
            {
                $pu = '9900';
            }

            $values[$key] = $cantidad;

            //$rows .= "<div class='cart-row' id='" . ($id = time()) . "' " . ($last_item ? " rel='tr-{$monto}&{$dimensiones}'" : '') . ">";

            $rows .= "<div class='cart-row' id='tr-{$key}'" . ($last_item ? " rel='" . json_encode(['monto' => $monto, 'dimension' => $dimensiones]) . "'" : '') . ">";

            $rows .= "<div class='col-md-8'>";

            $rows .= $articulo->nombre_producto;

            if ( ($itemsPromo = Articulo::whereRaw("id_producto IN ('" . implode("','", $ids_articulo) . "')")->get()) )

            {

                foreach ($itemsPromo as $itemPromo)

                {

                    $rows .= "<p style='margin:0 0 0 10px;font-size:13px; font-style:italic'>- {$itemPromo->nombre_producto} x 1 Un</p>";

                }

            }

            $rows .= "<h4>$ " . HFunctions::formatPrice($pu) . " x " . ($cantidad ?: 1) . " Un</h4>";

            $rows .= "</div>";

            $rows .= "<div class='col-md-2 text-right'>$ <span class='label-subtotal'>" . HFunctions::formatPrice($subtotal) . "</span></div>";

            $rows .= "<div class='col-md-2 text-right'>";

            if ( $id_producto != Articulo::shippingCost )

            {

                $rows .= "<a href='javascript:void(0)' onclick='borrar(\"{$key}\")' title='Quitar'><i class='fa text-danger fa-trash-alt'></i></a>";

            }

            $rows .= "</div>";

            $rows .= "</div>";

            $i++;

        }

        $this->setSessionCart($values, true);

        //$this->setSessionCart([], true);

        #--

        if ( isset($_GET['q']) )

        {

            echo $total;

            die;

        }

        #--

        if ( !$return )

        {

            die($rows);

        }

        return $rows;

    }



    public function quitar()

    {

        $id = trim($_GET['id']);

        $lineas = $this->getSessionCart();

        unset($lineas[$id]);

        $this->setSessionCart($lineas, true);

    }



    public function authForm($conEnvio = false)

    {

        //$conEnvio = intval($_POST['envio']);

        $user = $this->logged_user;

        ob_start();

        $masDatos = "<div class='col-md-6 form-group'>";

        $masDatos .= "<label for='nombre'>Nombre <i class='required'></i></label>";

        $masDatos .= "<input type='text' id='nombre' name='nombre' maxlength='60' class='form-control' value='{$user->nombre}' required>";

        $masDatos .= "</div>";

        $masDatos .= "<div class='col-md-6 form-group'>";

        $masDatos .= "<label for='apellido'>Apellido <i class='required'></i></label>";

        $masDatos .= "<input type='text' id='apellido' name='apellido' maxlength='40' class='form-control' value='{$user->apellido}' required>";

        $masDatos .= "</div>";

        if ( $conEnvio )

        {

            $addrData = "<div class='col-md-6'>";

            $addrData .= "<label for='select-addrs'>Direcciones</label>";

            $addrData .= "<select id='select-addrs' class='form-control'></select>";

            $addrData .= "</div>";

            $addrData .= "<div class='col-md-6'>";

            //$addrData .= "<div class='input-group-addon'>";

            $addrData .= "<label for='check-es-sucursal' style='position:absolute;right:15px;'>";

            $addrData .= "<input type='checkbox' id='check-es-sucursal' name='es_sucursal' value='1' disabled/> Es Sucursal de Correo";

            $addrData .= "</label>";

            $addrData .= "<label for='cp'>C&oacute;digo Postal <i class='required'></i></label>";

            $addrData .= "<input type='tel' id='cp' name='cp' maxlength='4' class='form-control' value='{$cp}' required>";

            //$addrData .= "</div>";

            //$addrData .= "<div class='input-group-addon' style='padding-top:20px;text-align: right'>";



            //$addrData .= "<select class='form-control' id='sucursal-correo' disabled></select>";

            //$addrData .= "</div>";

            $addrData .= "</div>";

            #--

            $addrData .= "<div class='col-md-6'>";

            //$addrData .= "<div class='input-group-addon'>";

            $addrData .= "<label for='provincia'>Provincia <i class='required'></i></label>";

            $addrData .= "<select name='json[provincia]' id='provincia' class='form-control' required disabled>";

            $addrData .= "<option value=''>Seleccionar</option>";

            $addrData .= "</select>";

            $addrData .= "</div>";

            $addrData .= "<div class='col-md-6'>";

            $addrData .= "<label for='localidad'>Localidad <i class='required'></i></label>";

            $addrData .= "<span id='localidad-group'>";

            $addrData .= "<select id='localidad' name='localidad' class='form-control' required disabled></select>";

            $addrData .= "</span>";

            //$addrData .= "</div>";

            $addrData .= "</div>";

            #--

            $addrData .= "<div class='col-md-12'>";

            $addrData .= "<div class='input-group-addon'>";

            $addrData .= "<label for='direccion'>Calle <i class='required'></i></label>";

            $addrData .= "<input type='text' id='direccion' name='json[calle]' placeholder='Nombre de Calle' class='form-control' required>";

            $addrData .= "</div>";

            $addrData .= "<div class='input-group-addon' style='width:.4%'>";

            $addrData .= "<label for='check-snro' style='position:absolute;right:15px;'>";

            $addrData .= "<input type='checkbox' id='check-snro' name='snro' value='1'/> Sin Número";

            $addrData .= "</label>";

            $addrData .= "<label for='altura'>Número <i class='required'></i></label>";

            $addrData .= "<input type='tel' id='numero' name='json[numero]' placeholder='Número' class='form-control' required>";

            $addrData .= "</div>";

            $addrData .= "</div>";

            #--

            $addrData .= "<div class='col-md-12'>";

            $addrData .= "<div class='input-group-addon'>";

            $addrData .= "<label for='piso'>Piso/Departamento</label>";

            $addrData .= "<input type='text' id='piso-depto' maxlength='10' name='json[piso_depto]' class='form-control'>";

            $addrData .= "</div>";

            $addrData .= "<div class='input-group-addon'>";

            $addrData .= "<label for='telefono'>N&deg; Tel&eacute;fono <i class='required'></i></label>";

            $addrData .= "<input type='tel' name='json[telefono]' maxlength='12' id='telefono' class='form-control' required>";

            $addrData .= "</div>";

            $addrData .= "</div>";

            $addrData .= "<div class='col-md-12'>";

            $addrData .= "<label for='referencia'>Referencia</label>";

            $addrData .= "<textarea rows='3' name='json[referencia]' style='resize: none' class='form-control' id='referencia' placeholder='Calles, Fachada'></textarea>";

            $addrData .= "<input type='hidden' name='addrId' id='addrId'>";

            $addrData .= "</div>";

        }

        $userID = floatval($user->id);

        ?>

        <style>

            #frm-completar-compra .input-group-addon {

                background: none;

                padding: 0;

                border: none;

                text-align: left;

            }



            #frm-completar-compra label {

                margin: 8px 0 2px 0;

                font-size: 15px;

            }

        </style>

        <div class="panel panel-success">

            <div class="panel-body" id="frm-completar-compra">

                <form action="!FrontUsuario/<?= (!$userID ? "continuar?shp={$conEnvio}" : "setAddress") ?>" autocomplete="off" id="frm-auth">

                    <div class="row" id="auth-user-container">

                        <?php if ( !$userID && false ): ?>

                            <div class="col-md-12 text-center" id="fbk-group">

                                <a href="javascript:void(0)" style="display: block" class="btn btn-primary" onclick="facebook_login(status_callback)">Acceder con Facebook</a>

                                <br/>

                            </div>

                        <?php endif; ?>

                        <?php if ( !$user->id ): ?>

                            <div class="col-md-6 form-group" id="email-group">

                                <label for="email">E-mail <i class="required"></i></label>

                                <?php if ( $correo = $user->email ): ?>

                                    <h4><?= $correo ?></h4>

                                <?php else : ?>

                                    <input type="email" id="email" name="email" maxlength="80" class="form-control" required>

                                <?php endif; ?>

                            </div>

                            <div class="col-md-6 form-group" id="dni-group">

                                <label for="dni">DNI <i class="required"></i></label>

                                <?php if ( $dni = $user->dni ): ?>

                                    <h4><?= $dni ?></h4>

                                <?php else: ?>

                                    <input type="tel" id="dni" name="dni" maxlength="8" class="form-control" required>

                                <?php endif; ?>

                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="row" id='extra-data-container'>

                        <?php

                        if ( $userID )

                        {



                            echo $addrData;

                        }

                        ?>

                    </div>

                    <div class="form-group" style="text-align:right">

                        <p style="margin:0;font-size:12px;text-align: left"><i class="required"></i>&nbsp;Datos requeridos</p>

                        <button type="submit" name="sve" class="btn btn-default">Continuar</button>

                    </div>

                </form>

            </div>

        </div>

        <script type="text/javascript">

            var addrs = JSON.parse('<?=json_encode(FrontUsuario::customerAddrs($userID))?>');

            var conEnvio = parseInt(<?=$conEnvio?>);

            (theForm = document.getElementById('frm-auth')).onsubmit = function (evt) {

                evt.preventDefault();

                let tform = this;

                submit_form(tform, function (res) {

                    if ( typeof res !== "object" )

                    {

                        return;

                    }

                    if ( !res["logged"] )

                    {

                        if ( res.dni && (inputDni = document.getElementById('dni')) )

                        {

                            //inputDni.remove();

                            inputDni.name = "";

                            inputDni.setAttribute("disabled", true);

                            document.getElementById('dni-group').insertAdjacentHTML("beforeend", `<input name="dni" type="hidden" value="${res["dni"]}"/>`);

                        }

                        if ( res.correo && (inputCorreo = document.getElementById('email')) )

                        {

                            //inputCorreo.remove();

                            inputCorreo.name = "";

                            inputCorreo.setAttribute("disabled", true);

                            //document.getElementById('email-group').insertAdjacentHTML("beforeend", `<h4>${res["correo"]}</h4>`);

                            document.getElementById('email-group').insertAdjacentHTML("beforeend", `<input name="email" type="hidden" value="${res["correo"]}"/><input name="userId" id="userId" type="hidden"/>`);

                            //document.getElementById('fbk-group').remove();

                        }

                        document.getElementById("extra-data-container").innerHTML = `<?=addslashes($masDatos)?>`;

                        document.getElementById('nombre').focus();



                        delete res["correo"];

                        delete res["dni"];

                        for (let i in res)

                        {

                            if ( (inText = document.getElementById(i)) )

                            {

                                inText.value = res[i];

                            }

                        }

                        return;

                    }

                    addrs = res["direcciones"];

                    delete res["direcciones"];

                    sessionStorage.setItem(sesAuth, JSON.stringify(res));

                    get_count_cart(true);

                    if ( conEnvio && !res["snd"] )

                    {



                        tform.action = tform.action.replace(/(!\w+)\/.+/, "$1/setAddress");

                        document.getElementById("auth-user-container").innerHTML = `<?=addslashes($addrData)?>`;

                        document.getElementById('extra-data-container').innerHTML = "";

                        showAddrs();

                        return;

                    }

                    //if ( res["snd"] )

                    {

                        location.href = `<?=self::siteUrl?>/pay?snd=${res["snd"]}`;

                    }

                });

            };



            function showAddrs()

            {

                if ( (selectAddrs = document.getElementById('select-addrs')) )

                {

                    selectPcia = document.getElementById('provincia');


                    selectLocalidad = document.getElementById('localidad');

                    inputAltura = document.getElementById('numero');

                    checkSucursal = document.getElementById('check-es-sucursal');



                    selectLocalidad.onchange = function () {

                        spanLocalidadGroup = document.getElementById('localidad-group');

                        this.name = "localidad";

                        this.removeAttribute("style");

                        if ( this.value === "Otra" )

                        {

                            this.value = "";

                            this.style["display"] = "none";

                            spnLocalidad = `<span id="spn-localidad">`;

                            spnLocalidad += `<a style="position: absolute;right:15px;top:10px" href='javascript:void(0)' onclick="selectLocalidad.onchange()">Seleccionar</a>`;

                            spnLocalidad += `<input class='form-control' id="in-localidad" name="${this.name}" required/>`;

                            spnLocalidad += `</span>`;

                            this.name = "";

                            //spanLocalidadGroup.insertAdjacentElement("beforeend", inputLocalidad);

                            spanLocalidadGroup.insertAdjacentHTML("beforeend", spnLocalidad);

                            document.getElementById('in-localidad').focus();

                            return;

                        }

                        if ( (node = document.getElementById('spn-localidad')) )

                        {

                            node.remove();

                        }

                    };

                    (inputCP = document.getElementById('cp')).onkeyup = function () {

                        cp = this.value.trim();

                        selectLocalidad.innerHTML = "";

                        selectLocalidad.setAttribute("disabled", true);

                        checkSucursal.setAttribute("disabled", true);

                        if ( cp.length > 3 )
                        {

                            checkSucursal.removeAttribute("disabled");

                            // ==================
                            // Para provincias
                            // ==================
                            fetch(`!FrontUsuario/consultaLocalidadCp?cp=${cp}`).then(function (ntwRes) {

            
                                ntwRes.json().then(function (jsRes) {

                                    selectPcia.onchange = function () {

                                       
                                        cargaLocalidades();

                                        

                                    };

                                    selectPciaOpts = "";

                                    if ( Object.keys(jsRes).length > 1 )

                                    {

                                        selectPciaOpts = "<option value=''>Seleccionar</option>";

                                    }

                                    for (provincia in jsRes)

                                    {

                                        selectPciaOpts += `<option>${provincia}</option>`;

                                        selectPcia.innerHTML = selectPciaOpts;

                                        selectPcia.removeAttribute("disabled");

                                    }

                                    selectPcia.onchange();

                                });
                            });

                            // ==================
                            // Para localidades
                            // ==================
                        function cargaLocalidades(){
                        fetch(`!FrontUsuario/consultaLocalidadCpLocalidades?cp=${cp}`).then(function (ntwRes) {

                                selectLocalidad.removeAttribute("disabled");


                                ntwRes.json().then(function (jsRes) {


                                    selectLocalidadesOpts = "";

                                    if ( Object.keys(jsRes).length > 1 )

                                    {

                                        selectLocalidadesOpts = "<option value=''>Seleccionar</option>";

                                    }

                                    for (nombre in jsRes)

                                    {

                                        selectLocalidadesOpts += `<option>${nombre}</option>`;

                                        selectLocalidad.innerHTML = selectLocalidadesOpts;

                                        // selectLocalidad.removeAttribute("disabled");

                                    }

                                    selectLocalidad.onchange();

                                });
                            

                            

                            });
                            }
                            

                        }

                    };



                    if ( (checkSinNumero = document.getElementById('check-snro')) )

                    {

                        checkSinNumero.onclick = function () {

                            altura = "";

                            inputAltura.setAttribute("required", true);

                            inputAltura.removeAttribute("readonly");

                            if ( this.checked )

                            {

                                altura = "S/N";

                                inputAltura.setAttribute("readonly", true);

                                inputAltura.removeAttribute("required");

                            }

                            inputAltura.value = altura;

                        };

                    }

                    /*selectSucursalCorreo = document.getElementById('sucursal-correo');

                    checkSucursal.onclick = function () {

                        if ( this.checked )

                        {



                        }

                    };*/



                    selectAddrs.onchange = function () {

                        data = key => {

                            jsn = (addrs[this.value] || {});



                            if ( typeof ($value = jsn[key]) === "undefined" )

                            {

                                $value = "";

                            }

                            return $value;

                        };



                        inputCP.value = data("cp");

                        checkSucursal.checked = data("sucursal");

                        selectPcia.innerHTML = `<option>${(pcia = data("provincia"))}</option>`;

                        if ( pcia )

                        {

                            selectPcia.removeAttribute("disabled");

                        }

                        selectLocalidad.innerHTML = `<option>${(localidad = data("localidad"))}</option>`;

                        if ( localidad )

                        {

                            selectLocalidad.removeAttribute("disabled");

                        }

                        document.getElementById('direccion').value = data("calle");

                        inputAltura.value = data("numero");

                        document.getElementById('piso-depto').value = data("piso_depto");

                        document.getElementById('telefono').value = data("telefono");

                        document.getElementById('referencia').value = data("referencia");

                        document.getElementById('addrId').value = parseInt(this.value);

                    };

                    selectAddrsOptn = "";

                    last = String(Object.keys(addrs)[0] || 0);

                    for (const addr in addrs)

                    {

                        optLabel = addrs[addr];

                        selectAddrsOptn += `<option value="${addr}">${optLabel.calle} ${optLabel.numero}. ${optLabel.localidad} (${optLabel.cp})</option>`;

                    }

                    selectAddrsOptn += "<option value='0'>Agregar Direcci&oacute;n</option>";

                    selectAddrs.innerHTML = selectAddrsOptn;

                    selectAddrs.value = last;

                    selectAddrs.onchange();

                }

            }



            showAddrs();

            //input_direccion("< ?=$direccion?>");

        </script>

        <?php

        $this->modalBlock(ob_get_clean());

    }

}