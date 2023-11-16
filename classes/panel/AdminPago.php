<?php



//ini_set("display_errors", "On");

class AdminPago extends AdminMain

{

    static $_columnas = array(

        "#ID",

        "Concepto",

        "Fecha",

        "Medio",

        "Monto",

        "Sucursal",

        ""

    );



    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuContable);

        $this->setPageTitle("Administración");

    }



    public function index($adm = false)

    {

        $export = new ExportOpts();

        $export->setExcelUrl($src = "!" . self::class . "/exportacion");

        $export->setPdfUrl("{$src}?pdf=1");

        #--

        $this->setBotonesAdministracion("Registrar", "javascript:void(0)", $export->drawControl());

        // $this->setBotonPostulantes();

        #--

        $tabla = new HDataTable();

        $tabla->setDateRangeConf(true, false);

        $tabla->setClass(self::class);

        $tabla->setDataSource(self::class . "/dataSource");

        $tabla->setColumns(static::$_columnas);

        $tabla->setHtmlControl("<label for='hoy'><input type='checkbox' id='hoy' /> DESDE/HASTA</label>&nbsp;" . $this->_selectLocal());

        //$tabla->setHtmlControl("aaaaa");

        $tabla->setHideSearchBox();

        $tabla->setKeys('adm', 1);

        $this->setParams('dataTable', $tabla->drawTable());

        $this->setBody("adminpago-index");

    }



    public function gastos()

    {

        $this->setPageTitle("Cuentas");

        $this->setItemSeleccionado(MenuPanel::menuContable);

        $this->index(1);

    }



    public function dataSource($reporte = false)

    {

        //$periodo = (trim($_REQUEST['anio']) ?: date('Y')) . "-" . (trim($_REQUEST['mes']) ?: date('m'));

        $fecha = HDate::sqlDate($_REQUEST['desde'] ?: date('d/m/Y'));

        $local = floatval($_REQUEST['local']);

        $adm = intval($_POST['adm']);

        #--

        $sql = array();

        $sql[] = "!`id_relacion`";

        $query = "='{$fecha}'";

        if ( $month = intval($_REQUEST['month']) )

        {

            list($y, $m) = explode("-", $fecha);

            $query = "BETWEEN '{$fecha}' AND '{$y}-{$m}-" . HDate::daysInMonth($m, $y) . "'";

        }

        $sql[] = "DATE(`fecha_registro`) {$query}";

        if ( $local )

        {

            $sql[] = "`id_sucursal`='{$local}'";

        }

        //$sql[] = "`modulo` NOT IN ('" . Movimiento::tablaInscripcion . "','" . MenuPanel::menuSuscripcion . "')";        #--

        for ($x = 0; $x < 2; $x++)

        {

            if ( $x > 0 )

            {

                unset($sql[0]);

            }

            $request[$x] = Movimiento::whereRaw(implode(" AND ", $sql))->orderBy('id_relacion')->orderBy("fecha_registro", "DESC")->orderBy('id', "DESC");

        }

        $count = $request[0]->count();

        $response = $reporte ? $request[0]->get() : $request[0]->paginate($this->x_page);

        $totales = array();

        foreach ($request[1]->get() as $allRow)

        {

            if ( !$allRow->id_cuenta )

            {

                continue;

            }

            $totales[$allRow->hasCuenta->nombre] += floatval($allRow->importe);

        }

        $totales[""] = "";

        $rows = "<tr class='tr-totales' id='{$month}'>";

        $rows .= "<td colspan='" . (sizeof(static::$_columnas) - 2) . "' align='right'>";

        $rows .= implode(" $<br/>", array_keys($totales));

        $rows .= "</td>";

        $rows .= "<td align='right'>";

        foreach (array_values($totales) as $value)

        {

            $rows .= "<p style='margin:0'>" . HFunctions::formatPrice($value) . "</p>";

        }

        $rows .= "</td>";

        if ( !$reporte )

        {

            $rows .= "<td></td>";

        }

        $rows .= "</tr>";

        foreach ($response as $item)

        {

            $movimientos = $item->hasChild->push($item)->sortBy('id_relacion');

            //if ( !$id_inscripto && !$adm )

            {

                $colspan = count(static::$_columnas);

                $bkg = "b2f5cf";

                if ( $item->saldo_item )

                {

                    $bkg = "fde8e8";

                    if ( $item->total_pagado )

                    {

                        $bkg = "f9f2bb";//pagó solo una parte

                    }

                }

                if ( $reporte )

                {

                    $colspan -= 1;

                }

                if ( $persona = $item->hasPersona )

                {

                    //$rows .= "<tr><td style='background:#{$bkg};font-weight:600' colspan='{$colspan}'>" . ($persona->label ?: "-") . "</td></tr>";

                }

            }

            foreach ($movimientos as $movimiento)

            {

                $parent = !$movimiento->id_relacion;

                //HArray::varDump($movimiento->monto_pago, false);

                $rows .= "<tr id='" . ($id = $movimiento->id) . "'>";

                if ( $parent )

                {

                    $rows .= "<td>{$id}</td>";

                    $rows .= "<td>";

                    $rows .= $movimiento->hasConcepto->nombre;

                    if ( ($opNro = $movimiento->id_operacion) )

                    {

                        $rows .= " #{$opNro}";

                    }

                    $rows .= "</td>";

                    $rows .= "<td>{$movimiento->fecha_aciento}" . substr($movimiento->fecha_hora, 10) . "</td>";

                }

                else

                {

                    $rows .= "<td colspan='3' class='text-center'>|______________________________</td>";

                }

                //$rows .= "<td>" . (!$parent ? $movimiento->hasCuenta->nombre : "-") . "</td>";

                $rows .= "<td>{$movimiento->hasCuenta->nombre}</td>";

                $rows .= "<td class='text-right'>" . HFunctions::formatPrice($monto = $movimiento->importe) . "</td>";

                $rows .= "<td class=''>" . Local::nombreLocal($movimiento->id_sucursal) . "</td>";

                #--

                if ( !$reporte )

                {

                    $rows .= "<td class='text-center'>";

                    if ( $parent && $movimiento->dias_pago < 7 )

                    {

                        $modulo = $movimiento->modulo;

                        //if ( ($modulo != Movimiento::moduloCuenta) && $this->controlPermiso(Permiso::permisoCrear, false) )

                        if ( $this->controlPermiso(Permiso::permisoCrear, false) )

                        {

                            $rows .= "<button class='btn btn-primary' onclick='get_modal_form({\"id\":\"{$id}\",\"monto\":\"{$monto}\", \"adm\":$adm}, \"!" . self::class . "/form\")'><i class='fa fa-ticket-alt'></i></button>";

                        }

                        #--

                        if ( ($modulo == MenuPanel::menuContable) && $this->controlPermiso(Permiso::permisoBorrar, false) )

                        {

                            $rows .= "<button type='button' onclick='dt_delete(\"{$id}\")' class='btn btn-danger'><i class='fa fa-trash'></i></button>";

                        }

                        #--

                        /*if ( !$movimiento->pendiente && $movimiento->dias_pago < 7 && $this->controlPermiso(Permiso::permisoBorrar, false) )

                        {



                        }*/

                    }

                    $rows .= "</td>";

                }

                $rows .= "</tr>";

            }

        }

        #--

        if ( !$reporte )

        {

            $rows .= "<tr class='not'><td colspan='" . count(static::$_columnas) . "' data-count='{$count}'>{$this->replaceLinks($response->links())}</td></tr>";

            unset($_REQUEST['p']);

            $rows .= "<script>document.getElementById('ec-a-pdf').href='!" . self::class . "/exportacion?pdf=1&" . http_build_query($_REQUEST) . "';</script>";

            die($rows);

        }

        return $rows;

    }



    public function form()

    {

        $abono = floatval($_POST['monto']);

        $id = floatval($_POST['id']);

        $cid = floatval($_POST['cid']);

        $contable = intval($_POST['adm']);

        $titulo = "Registrar operación";

        //$importe = floatval($_POST['monto']);

        if ( ($item = Movimiento::find($id)) )

        {

            $titulo = "Operación #{$id}";

            //$abono = $item->saldo_item;

            #--

            if ( $contable )

            {

                $abono += $item->saldo;

            }

            //HArray::varDump($item->monto_pago);

            //$importe = floatval($item->{$abono ? "monto_pago" : "importe"});

        }

        #--

        //if ( ($persona = Persona::find($cid)) )

        if ( $persona = trim($_POST['persona']) )

        {

            $titulo .= " [{$persona}]";

        }

        $this->setBlockModal($this->_formPago($id, $titulo, $abono, $cid));

    }



    public function exportacion()

    {

        $this->exportando(static::$_columnas, $this->dataSource(true));

    }



    public function eliminar()

    {

        $id = floatval($_POST['id']);

        if ( ($item = Movimiento::find($id)) )

        {

            if ( $item->dias_pago < 8 )

            {

                $item->hasChild()->delete();

                HArray::jsonResponse('ok', $item->delete());

            }

        }

        HArray::jsonError("No se pudo eliminar.");

    }



    protected function _formPago($id, $titulo, $importe, $op_type = null, $frm_action = null)

    {

        $pagos = array();

        $entidad = trim($_POST['mdl']);

        if ( $id )

        {

            $importe = 0;

            $res_pagos = Movimiento::whereRaw("(id='{$id}' OR id_relacion='{$id}')")->select('id_operacion', 'id_cuenta', 'fecha_registro', 'id_concepto', 'valor', 'accion', 'importe', 'id_sucursal')->get();

            foreach ($res_pagos as $index => $res_pago)

            {

                $pagos[$res_pago->id_cuenta] = $res_pago->importe;

                $importe += $res_pago->importe;

                if ( !$res_pago->id_relacion )

                {

                    $item = $res_pago;

                    $id_concepto = $res_pago->id_concepto;

                    $id_operacion = $res_pago->id_operacion;

                    $id_sucursal = $res_pago->id_sucursal;

                }

            }

        }

        $exc = array(8);

        if ( (!$op_type && !$item->id_persona) || $item->pago_persona )

        {

            $exc[] = Concepto::cuentaCorriente;

        }

        ob_start();

        ?>

        <div class="panel panel-primary">

            <div class="panel-body">

                <form action="!<?= $frm_action ?: self::class . "/procesar" ?>" id="frm-operacion">

                    <div class="row">

                        <input type="hidden" name="id_local" value="<?= $id_sucursal ?>"/>

                        <h4 class="col-md-12" style="text-decoration: underline"><?= $titulo ?><br/></h4>

                        <?php if ( true ): ?>

                            <div class="col-md-5 form-group">

                                <label for="local">Sucursal <i class="required"></i></label>

                                <?= $this->_selectLocal() ?>

                            </div>

                            <div class="col-md-7 form-group"></div>

                            <div class="clearfix"></div>

                        <?php endif; ?>

                        <div class="col-md-5 form-group">

                            <label for="monto">Importe <i class="required"></i></label>

                            <input type="tel" id="monto" class="form-control" value="<?= $importe ?>" name="importe" required/>

                            <?php

                            if ( $importe && false )

                            {

                                echo "<a href='javascript:void(0)' style='float:right'>Otro Importe</a>";

                            }

                            ?>

                        </div>

                        <div class="col-md-7 form-group">

                            <?php if ( !$op_type && !$id_operacion ): ?>

                                <label for="id_concepto">Concepto <i class="required"></i></label>

                                <select name="id_concepto" id="id_concepto" class="form-control" required>

                                    <option value="">Seleccionar</option>

                                    <?php foreach (Concepto::whereIn('tipo', ["", "pasivo"])->select('id_concepto', 'concepto')->get() as $item): ?>

                                        <option value="<?= $item->id_concepto ?>"><?= $item->nombre ?></option>

                                    <?php endforeach; ?>

                                    <option value="nuevo">Otro (Registrar)</option>

                                </select>

                                <input type="hidden" name="trn" value="1"/>

                            <?php else : ?>

                                <?php if ( $item ): ?>

                                    <p>&nbsp;</p>

                                    <h4><?= $item->hasConcepto->nombre . " #{$id_operacion}" ?></h4>

                                <?php endif; ?>

                                <?php

                                if ( !$id_concepto )

                                {

                                    $id_concepto = ($entidad == Proveedor::class) ? Concepto::itemCuentaProveedor : Concepto::cuentaCorriente;

                                }

                                ?>

                                <input type="hidden" name="id_concepto" value="<?= $id_concepto ?>">

                                <input type="hidden" name="cid" value="<?= $op_type ?: $item->id_persona ?>"/>

                            <?php endif; ?>

                        </div>

                        <div class="clearfix"></div>

                        <div class="col-md-5 form-group">

                            <label for="fecha-operacion">Fecha <i class="required"></i></label>

                            <input type="text" class="form-control" name="fecha" id="fecha-operacion" required/>

                        </div>

                        <!--<div class="col-md-3 form-group" style="padding-top:10px;text-align: center"></div>-->

                        <div class="col-md-7 form-group" style="float:right">

                            <?php echo PagoControl::pagoForm($pagos, $exc) ?>

                        </div>

                        <!--<div class="col-md-6"></div>-->

                        <div class="col-md-12 text-right">

                            <input type="hidden" name="id" value="<?= $id ?>"/>

                            <button type="submit" class="btn btn-success">Aceptar</button>

                            <button type="button" data-dismiss="modal" class="btn btn-default" id="btn-cerrar">Cancelar</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <script>

            $('#fecha-operacion').val("<?=$item->fecha_aciento ?: date('d/m/Y')?>").calendario({'yearRange': '-4:+0', 'defaultDate': new Date(), 'maxDate': new Date()});

            if ( (selectSucursal = document.getElementById('local')) )

            {

                selectSucursal.setAttribute("required", true);

                selectSucursal.value = "<?=$id_sucursal?>";

            }

            var in_value = parseFloat(<?=floatval($importe)?>);

            if ( (inputMonto = document.getElementById('monto')) )

            {

                inputMonto.focus();

                $(inputMonto).decimal(".");

                inputMonto.onkeyup = function () {

                    in_value = "";

                    if ( parseFloat(this.value) > 0 )

                    {

                        in_value = this.value;

                    }

                    document.getElementById('pc-spn-total').innerHTML = in_value;

                };



                inputMonto.onfocusout = function () {

                    this.value = in_value;

                };

            }

            if ( (selectConcepto = document.getElementById('id_concepto')) )

            {

                selectConcepto.value = "<?=$id_concepto?>";

                selectConcepto.onchange = function () {

                    thisValue = this.value.trim();



                    if ( thisValue === "nuevo" )

                    {

                        this.value = "";

                        this.style["display"] = "none";

                        this.removeAttribute("required");

                        spnConvenio = `<span class="" id="spn-convenio">`;

                        spnConvenio += `<a style="position: absolute;right:15px;margin-top:5px" href='javascript:void(0)' onclick="selectConcepto.onchange()">Seleccionar</a>`;

                        spnConvenio += `<span class="input-group-addon">`;

                        spnConvenio += `<input class='form-control' name="concepto" id="in-concepto" maxlength="80" required/>`;

                        spnConvenio += `</span>`;

                        //spnConvenio += `<span class="input-group-addon" style="width:0.01%"><button type="button">OK</button></span>`;

                        spnConvenio += `</span>`;

                        this.name = "";

                        this.parentElement.insertAdjacentHTML("beforeend", spnConvenio);

                        document.getElementById('in-concepto').focus();

                        return;

                    }

                    this.removeAttribute("style");

                    this.name = "id_concepto";

                    this.setAttribute("required", true);

                    if ( (spnConvenio = document.getElementById('spn-convenio')) )

                    {

                        spnConvenio.remove();

                    }

                };

            }

            document.getElementById('frm-operacion').onsubmit = function (evt) {

                evt.preventDefault();

                if ( in_value <= 0 )

                {

                    inputMonto.focus();

                    return;

                }

                if ( parseFloat(labelMontoCompra.innerText) > 0 )

                {

                    set_notice("dv-pc-pago", "Seleccionar cuenta, indicar monto y dar click en &#10004");

                    return;

                }

                submit_form(this, function (res) {

                    document.getElementById('btn-cerrar').click();

                    if ( !res.nok && typeof get_rows === "function" )

                    {

                        get_rows();

                    }

                });

            }

        </script>

        <?php

        return ob_get_clean();

    }



    public function procesar()

    {

        $suscripcion = !isset($_POST['trn']);

        $monto = floatval($_POST['importe']);

        $id_concepto = floatval($_POST['id_concepto']);

        $concepto = trim($_POST['concepto']);

        $id_local = floatval($_POST['id_local']);

        $oid = floatval($_POST['id']);

        $cid = floatval($_POST['cid']);

        $fecha = HDate::sqlDate($_POST['fecha']);

        $cuentas = json_decode($_POST['pc_pago'], true);

        if ( $cid )

        {

            $modulo = Movimiento::moduloCuenta;

        }

        #--

        if ( !$id_local )

        {

            HArray::jsonError("Seleccionar Sucursal", "id_local");

        }

        #--

        if ( !$monto )

        {

            HArray::jsonError("Ingresar un valor", "monto");

        }

        #--

        if ( !$suscripcion && (!$id_concepto && strlen($concepto) > 3) )

        {

            if ( Concepto::where('concepto', "LIKE", "{$concepto}%")->first() )

            {

                HArray::jsonError("Ya existe un ítem con ese nombre. Ingresar otro o Seleccionarlo", "concepto");

            }

            $crear = new Concepto();

            $crear->concepto = mb_strtolower($concepto);

            $crear->categoria = "";

            $crear->tipo = Concepto::tipoCuentaPasivo;

            $crear->accion = "debe";

            $crear->save();

            $id_concepto = $crear->id_concepto;

        }

        #--

        if ( !$cid && key_exists(Concepto::itemCuentaCliente, $cuentas) )

        {

            //HArray::jsonError("Item incorrecto");

        }

        #--

        $saldo = $i = 0;

        $pagos = collect([]);

        if ( $oid )

        {

            $pagos = Movimiento::whereRaw("id='{$oid}' OR (id_relacion AND id_relacion='{$oid}')")->get();

        }

        $pagoCliente = ($id_concepto == Concepto::itemDeudaCliente);

        //HArray::varDump(intval($pagoCliente));

        foreach ($cuentas as $cuenta => $importe)

        {

            $operacion = $pagos[$i] ?: new Movimiento();

            $es_deuda = ($cuenta == Concepto::cuentaCorriente);

            if ( $operacion->id && ($operacion->id == $oid) )

            {

                $modulo = $operacion->modulo;

                if ( !$es_deuda && !$operacion->pago_persona && $operacion->id_persona )

                {

                    $modulo = Movimiento::moduloStock;

                    $operacion->valor = 0;

                    $operacion->accion = $cid = 0;

                }

                $id_operacion = $operacion->id_operacion;

                $id_local = $operacion->id_sucursal;

            }

            #--

            $operacion->id_relacion = $i ? $oid : 0;

            $operacion->id_sucursal = $id_local ?: $this->admin_user->id_local;

            //$operacion->valor = $item->valor;

            $operacion->modulo = $modulo ?: MenuPanel::menuContable;

            $operacion->id_concepto = $es_deuda ? Concepto::itemVarios : $id_concepto;

            $operacion->fecha_registro = $fecha;

            $operacion->id_operacion = $id_operacion;

            $operacion->id_cuenta = $cuenta;

            $operacion->importe = $importe;

            if ( !$operacion->id )

            {

                $operacion->{($pagoCliente ? "accion" : "valor")} = $cid;

                $operacion->saldo = intval(!$pagoCliente);

            }

            $operacion->save();

            if ( !$i && !$oid )

            {

                $oid = $operacion->id;

            }

            #--

            if ( $es_deuda )

            {

                $saldo += $importe;

            }

            else

            {

                $saldo -= $importe;

            }

            $arr[$i] = $operacion->id;

            $operacion = null;

            $pagos->forget($i);

            $i++;

        }

        #--

        CuentaCliente::actualizarSaldo($cid, $saldo);

        #--

        foreach ($pagos as $pago)

        {

            $pago->delete();

            //Movimiento::whereRaw("id <> '{$oid}' AND id_relacion='{$oid}'")->whereNotIn('id_cuenta', array_keys($cuentas))->delete();

        }

        $res["ok"] = $oid;

        HArray::jsonResponse($res);

    }

}