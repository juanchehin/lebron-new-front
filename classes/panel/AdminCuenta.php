<?php



class AdminCuenta extends AdminMain

{

    public static $_cols = array("#ID", "Nº Cbte.text-center", "Fecha.text-center", "Concepto", "Deuda.text-center", "Pago.text-center", "&nbsp;");



    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuRegistro);

    }



    public function index($id, $reporte = false)

    {

        $MODEL = ucfirst(preg_replace("#.+\/(\w+)\/\d+.+#", "$1", $_SERVER['REQUEST_URI']));

        $this->setItemSeleccionado($MODEL);

        $sumadorSaldo = 0;

        #--

        if ( isset($_GET['dt']) || self::isXhrRequest() )

        {

            $prm['id_cliente'] = floatval($_POST['id_cliente']);

            $prm['fecha_dsd'] = HDate::sqlDate($_POST['desde']);

            //$prm['fecha_hst'] = HDate::sqlDate($_POST['hasta'] ?: date('d/m/Y'));

            $reporte ? ($prm = $this->reporte()) : $this->reporte($prm);

            extract($prm);

            $MODEL = trim($_POST['model']);

            $cuentaProveedor = ($MODEL == Proveedor::class);

            $trows = null;

            $where = "`accion`='{$id_cliente}'";

            if ( $cuentaProveedor )

            {

                $where = "`valor`='{$id_cliente}'";

            }

            if ( $fecha_dsd )

            {

                $where .= " AND `fecha_registro` >= '{$fecha_dsd}'";

            }

            $sql = CuentaCliente::whereRaw($where)->orderByRaw("fecha_registro DESC, fecha_hora DESC");

            $cnt = $sql->count();

            $registros = $reporte ? $sql->get() : $sql->paginate($this->x_page);

            $persona = Persona::find($id_cliente);

            if ( $reporte )

            {

                $trows .= "<tr><td colspan='7' align='center' style='font-weight:600' bgcolor='#f5f5dc'>{$persona->nombre_apellido}</td></tr>";

            }

            //$persona = Persona::find($id_cliente);

            $saldo = floatval($persona->saldo);

            /*if ( !$persona->array_otros_datos['saldo_update'] )

            {

                $_saldo = array_pop(CuentaCliente::calculoSaldos($id_cliente, $cuentaProveedor));

                //if ( $saldo != $_saldo )

                {

                    $persona->saldo = $_saldo;

                    $persona->otros_datos_json = ["saldo_update" => date('Y-m-d')];

                    $persona->save();

                }

            }*/

            foreach ($registros as $index => $reg)

            {

                $monto_haber = "";

                $monto_debe = HFunctions::formatPrice($reg->importe);

                /*if ( !($op_nro = $reg->id_operacion) )

                {

                    $monto_haber = $monto_debe;

                    //$monto_debe = "";

                }*/

                #-- Es pago?

                if ( in_array($reg->id_concepto, [Concepto::cuentaCorriente, Concepto::itemCuentaProveedor]) )

                {

                    $monto_haber = $monto_debe;

                    $monto_debe = "";

                }

                #--

                $trows .= "<tr id='" . ($_id = $reg->id) . "'>";

                $trows .= "<td>{$_id}</td>";

                $trows .= "<td class='text-center'>" . (Venta::find($opNro = $reg->id_operacion)->factura ?: "-") . "</td>";

                $trows .= "<td class='text-center'>{$reg->fecha_aciento}</td>";

                $trows .= "<td>";

                $trows .= Concepto::get($reg->id_cuenta);

                //$trows .= "<p style='margin:0'>{$who->label}</p>";

                $trows .= "</td>";

                $trows .= "<td class='amount'>{$monto_debe}</td>";

                $trows .= "<td class='amount'>{$monto_haber}</td>";

                if($monto_debe && $reg->importe)
                {
                    $sumadorSaldo += $reg->importe;                     
                }
                if($monto_haber && $reg->importe)
                {
                    $sumadorSaldo -= $reg->importe;                     
                }
                //$trows .= "<td class='amount'>" . Facturacion::numberFormat($reg->saldo ?: $saldo[$_id]) . "</td>";

                if ( !$reporte )

                {

                    $trows .= "<td class='text-center'>";

                    if ( !$opNro && $reg->dias_pago < 15 )

                    {

                        $trows .= "<a href='javascript:void(0)' onclick='dt_delete(this)'><i class='fa fa-trash-alt text-danger'></i></a>";

                        if($reg->id_operacion)
                        {
                            $trows .= "<a href='!AdminVenta/modalForm?n={$reg->id_operacion}' target='_blank'><i class='fa fa-file-pdf'></i></a>";
                        }


                    }else
                    {
                        if($reg->id_operacion)
                        {
                            $trows .= "<a href='!AdminVenta/modalForm?n={$reg->id_operacion}' target='_blank'><i class='fa fa-file-pdf'></i></a>";
                        }

                    }

                    $trows .= "</td>";

                }

                $trows .= "</tr>";

            }

            if ( !$reporte )

            {

                $extraBtn = "<a id='aa-reporte' href='!" . self::class . "/exportar?pdf=1' class='pull-right' target='_blank'>Reporte</a>";

                $trows .= "<tr class='not' data-count='{$cnt}'><td colspan='" . count(self::$_cols) . "'>{$this->replaceLinks($registros->links())}</td></tr>";

                $trows .= "<script>";

                $trows .= "document.getElementById('hh-saldo').innerHTML = 'Saldo: $ " . HFunctions::formatPrice($sumadorSaldo) . "';\n";

                $trows .= "aaReporte = (document.getElementById('aa-reporte') || document.createElement('a'));\n";

                $trows .= "aaReporte.remove();\n";

                $trows .= "if ( {$cnt} > 2 ){ \n";

                $trows .= "document.getElementById('action-buttons').insertAdjacentHTML('afterbegin', `{$extraBtn}`);\n";

                $trows .= "}";

                $trows .= "</script>";

                die($trows);

            }

            return $trows;

        }

        $this->setItemSeleccionado(strtolower($MODEL));

        $this->controlPermiso(Permiso::permisoCuentaVer);

        $back = $_SERVER['HTTP_REFERER'] ?: self::sysUrl . "/clientes";

        if ( class_exists($MODEL) && !($reg = $MODEL::find($id)) )

        {

            Router::redirect($back);

        }

        $this->setPageTitle("Cuenta de {$reg->nombre_apellido}");

        $extraBtn = "&nbsp;&nbsp;<a href='{$back}' class='btn btn-default'>Volver</a>";

        $this->setBotonNuevo("Registrar", "javascript:void(0)", $extraBtn);

        $dt = new HDataTable();

        $dt->setColumns(static::$_cols);

        //$dt->setHideDateRange();

        $dt->setHideSearchBox();

        $dt->setKeys('id_cliente', $id);

        $dt->setKeys('model', $MODEL);

        $dt->setDataSource(self::class . "/index");

        $vars['cuenta_table'] = $dt->drawTable();

        $vars['id_cliente'] = $id;

        $vars['persona'] = $reg->nombre_apellido;

        $vars['model'] = $MODEL;

        $this->setParams($vars);

        $this->setBody("registro-index");

    }



    public function modalForm()

    {

        $id_cliente = floatval($_POST['cid']);

        $MODEL = trim(ucfirst($_POST['mdl']));

        if ( class_exists($MODEL) && !($cliente = $MODEL::find($id_cliente)) )

        {

            return;

        }

        $params = array(

            'id_cliente' => $id_cliente,

            'es_ingreso' => 1,

            'model' => $MODEL,//($MODEL == Cliente::class),

            'titulo' => $cliente->nombre_apellido . ", $ <i id='label-saldo'>{$cliente->saldo}</i>",

            'acciones' => array(

                Concepto::itemPagoCliente => "Registrar Pago",

                Concepto::itemCuentaCliente => "Registrar Deuda"

            ),

            'pagoControl' => PagoControl::pagoForm(),

            'itemDeuda' => Concepto::itemDeudaCliente,

            'cuentas' => Concepto::whereRaw("!`borrado` AND `id_concepto` != '" . Concepto::itemDeudaCliente . "' AND `categoria`='disponibilidad'")->get()

        );

        $params['cliente'] = $cliente;

        $params['proveedores'] = Proveedor::where('borrado', 0)->orderBy('nombre')->get();

        $this->setParams($params);

        $modal = $this->loadView("admin/registro-cuenta-form");

        $this->setBlockModal($modal);

    }



    public function registro()

    {

        $id_cliente = floatval($_GET['idc']);

        list($model, $id) = explode("&", $_POST['id']);

        $id_cuenta = floatval($_POST['cuenta']);

        $es_deuda = ($id_cuenta == Concepto::itemDeudaCliente);

        $id_concepto = floatval($_POST['id_concepto']);

        $id_proveedor = floatval($_POST['a_cuenta']);

        $concepto = trim($_POST['concepto']);

        $importe = floatval($_POST['importe']);

        $fecha = HDate::sqlDate($_POST['fecha']);

        $cheEsProveedor = (preg_match("#{$model}#i", Proveedor::class) || $id_proveedor);

        $saldo = floatval($_POST['saldo']);

        $nota = trim($_POST['nota']);

        //HArray::varDump($cheEsProveedor);

        //HArray::varDump($_POST);

        if ( !$id_cuenta )

        {

            HArray::jsonError("Seleccionar cuenta", "cuenta");

        }

        #--

        if ( !$importe )

        {

            HArray::jsonError("Ingresar un importe", "importe");

        }

        #--

        $registro = Movimiento::findOrNew($id);

        $id_cuenta_haber = $cheEsProveedor ? $id_cuenta : Concepto::itemCuentaCliente;

        $modulo = Movimiento::moduloCuenta . "_pago";

        $id_operacion = 0;

        if ( $es_deuda )

        {

            $id_cuenta = Concepto::itemDeudaCliente;

            $id_cuenta_haber = Concepto::itemVenta;

            $modulo = Movimiento::moduloCuenta;

            $id_operacion = 1;

            /*if ( $id_concepto || $concepto )

            {

                $item = Concepto::crear($concepto, "HABER", $id_concepto, $id_cuenta);

                $id_cuenta_haber = $item->id_concepto ?: $id_concepto;

            }*/

        }

        $registro->valor = $id_proveedor;

        $registro->id_operacion = $id_operacion;

        $registro->id_cuenta = $cheEsProveedor ? Concepto::itemCuentaProveedor : $id_cuenta;

        $registro->id_concepto = $id_cuenta_haber;

        //$registro->id_operacion = $id_cliente;

        $registro->accion = $id_cliente;

        $registro->modulo = $modulo;

        $registro->id_sucursal = $this->id_local_ses;

        $registro->fecha_registro = $fecha;

        //$registro->comentario = $saldo;

        $registro->importe = $importe;

        $registro->save();

        #--

        HArray::jsonResponse('ok', true);

    }



    public function selectConceptos()

    {

        $id_cuenta = floatval($_GET['id_cuenta']);

        $conceptos = Concepto::whereRaw("!borrado AND visible AND id_cuenta={$id_cuenta}")->orderBy('concepto')->get();

        $select = "<option value=''></option>";

        foreach ($conceptos as $res)

        {

            $select .= "<option value='{$res->id_concepto}'>{$res->nombre}</option>";

        }

        die($select);

    }



    public function exportar()

    {

        $pdf = trim($_GET['pdf']);

        $idp = floatval($_GET['']);

        $this->exportando(static::$_cols, $this->index($idp, true));

    }



    public function eliminar()

    {

        $id = floatval($_POST['id']);

        if ( $registro = Movimiento::find($id) )

        {

            $add = in_array($registro->id_concepto, [Concepto::itemDeudaCliente, Concepto::itemCuentaProveedor]);   //es un pago

            if ( !($cid = floatval($registro->accion)) )

            {

                $cid = floatval($registro->valor);

            }

            CuentaCliente::actualizarSaldo($cid, $registro->importe, $add);

            //$registro->hasChild()->delete();

            $registro->delete();

        }

    }

}