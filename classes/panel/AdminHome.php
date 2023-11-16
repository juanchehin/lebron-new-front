<?php



class AdminHome extends AdminMain

{

    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuInicio);

    }



    public function index()

    {

        $this->setPageTitle();

        $this->setPageHeader(SITE_NAME);



        //file_put_contents($dolar_file, json_encode($data));

        $this->setBody("inicio-index");

    }



    public function configForm()

    {

        $conf = MainModel::config();

        if ( isset($_GET['s']) && $_POST && self::isXhrRequest() )

        {

            $data = $_POST['conf'];

            if ( !($data['cuit'] = PersonaForm::validarCuit($data['cuit'])) )

            {

                HArray::jsonError("CUIT no válido", "conf[cuit]");

            }

            if ( ($data['tarjeta_un_pago'] > 100) || ($data['tarjeta_tres_pago'] > 100) || ($data['tarjeta_seis_pago'] > 100) )

            {

                HArray::jsonError("Porcentaje de tarjeta no valido", "");

            }

            $json['update'] = (($conf['precio_dolar'] != $data['precio_dolar']) || ($data['dolar_paralelo'] != $conf['dolar_paralelo']));

            $data['upd_lista'] = $json['update'];

            MainModel::setConfig($data);

            $json['ok'] = 1;

            HArray::jsonResponse($json);

        }

        $contacto = $conf['contacto'];

        list($valor, $unidad) = explode("|", $conf['utilidad']);

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading">Configuraci&oacute;n</div>

            <div class="panel-body">

                <form action="!<?= self::class ?>/configForm?s" id="frm-config">

                    <div class="row">

                        <div class="col-md-7 form-group">

                            <label for="entidad">Entidad</label>

                            <h4 style="margin-top:5px"><?= $conf["site_name"] ?></h4>

                        </div>

                        <div class="col-md-5 form-group">

                            <label for="cuit">CUIT</label>

                            <input type="tel" name="conf[cuit]" id="cuit" class="form-control" value="<?= $conf['cuit'] ?>" required>

                        </div>

                        <div class="col-md-7 form-group">

                            <label for="email">E-mail</label>

                            <input type="text" name="conf[contacto][email]" id="email" class="form-control" value="<?= $contacto['email'] ?>" required>

                        </div>

                        <div class="col-md-5 form-group">

                            <label for="telefono">Teléfono</label>

                            <input type="tel" name="conf[contacto][telefono]" id="telefono" class="form-control" value="<?= $contacto['telefono'] ?>" required>

                        </div>

                        <div class="col-md-3 form-group">

                            <label for="precio_dolar">Dólar Oficial</label>

                            <input type="tel" name="conf[precio_dolar]" rel="decimal" id="precio_dolar" class="form-control" value="<?= $conf['precio_dolar'] ?>" required>

                        </div>

                        <div class="col-md-4 form-group">

                            <label for="dolar_paralelo">Dólar Paralelo</label>

                            <input type="tel" name="conf[dolar_paralelo]" rel="decimal" id="dolar_paralelo" class="form-control" value="<?= $conf['dolar_paralelo'] ?>" required>

                        </div>

                        <div class="col-md-5 form-group">

                            <label for="utilidad">Utilidad</label>

                            <div class="input-group-addon">

                                <input type="tel" name="utilidad-0" rel="decimal" id="utilidad" class="form-control" value="<?= $valor ?>" required>

                            </div>

                            <div class="input-group-addon">

                                <select id="unidad" name="utilidad-1" class="form-control" required>

                                    <option value="">Unidad</option>

                                    <?php foreach (Articulo::$_monedas as $moneda): ?>

                                        <option><?= $moneda ?></option>

                                    <?php endforeach; ?>

                                    <option>%</option>

                                </select>

                            </div>

                            <input type="hidden" id="cnf-utilidad" name="conf[utilidad]" value="<?= $conf['utilidad'] ?>">

                        </div>

                        <!-- ============== Tarjetas ================= -->
                        <div class="col-md-3 form-group">

                            <label for="precio_dolar">Tarjeta 1 pago</label>

                            %<input type="tel" name="conf[tarjeta_un_pago]" rel="decimal" id="tarjeta_un_pago" class="form-control" value="<?= $conf['tarjeta_un_pago'] ?>" required>

                        </div>

                        <div class="col-md-4 form-group">

                            <label for="dolar_paralelo">Tarjeta 3 pagos</label>

                            %<input type="tel" name="conf[tarjeta_tres_pago]" rel="decimal" id="tarjeta_tres_pago" class="form-control" value="<?= $conf['tarjeta_tres_pago'] ?>" required>

                        </div>

                        <div class="col-md-4 form-group">

                            <label for="dolar_paralelo">Tarjeta 6 pagos</label>

                            %<input type="tel" name="conf[tarjeta_seis_pago]" rel="decimal" id="tarjeta_seis_pago" class="form-control" value="<?= $conf['tarjeta_seis_pago'] ?>" required>

                        </div>
                        <!-- ============== Fin tarjetas ================= -->
                        <div class="form-btns-group">

                            <button type="submit" class="btn btn-primary">Guardar</button>

                            <button type="button" id="btn-cls-frm-conf" data-dismiss="modal" class="btn btn-default">Cerrar</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <script>

            $('#cuit').mask("00-00000000-0");

            $('[rel="decimal"]').decimal(".");

            selectUnidad = document.getElementById('unidad');

            selectUnidad.value = "<?=$unidad?>";

            utilidad = [];

            utilidad[0] = "<?=$valor?>";

            utilidad[1] = "<?=$unidad?>";

            document.querySelectorAll('[name^="utilidad"]').forEach(function ($ctrl) {

                ["onkeyup", "onchange"].forEach(function (evt) {

                    $ctrl[evt] = function () {

                        index = this.name.replace(/[^\d+]/gi, "");

                        utilidad[index] = this.value;


                        document.getElementById('cnf-utilidad').value = utilidad.join("|");

                    };

                });

            });

            document.getElementById('frm-config').onsubmit = function (evt) {

                evt.preventDefault();

                submit_form(this, function (res) {

                    document.getElementById('btn-cls-frm-conf').click();

                    if ( (typeof get_rows === "function") && /(producto|catalogo)/gi.test(document.URL) && res["update"] )

                    {

                        get_rows();

                    }

                });

            };

        </script>

        <?php

        $this->setBlockModal(ob_get_clean());

    }



    #-- @estadísticas

    public function estadistica($param)

    {

        $this->addScript("static/plugin/loader-chart.js");

        list($tag, $id) = explode("&", $param);

        $from = date('Y-m-01', strtotime("-12 month", time()));

        $dolar = MainModel::getInfoDolar();

        $servername = 'localhost';

        $username = 'root';
        
        $password = '';

        $dbname = 'lebronsu_admin';
    
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        //run the store proc
        $productos_mas_vendidos = mysqli_query($conn, "CALL productos_mas_vendidos()");
    
        $conn->close();

        switch ( $tag )

        {

            case MenuPanel::menuCategorias:

            case Categoria::tipoMarca:

            case MenuPanel::menuVentas:

            case "tag":

                $sql = "!`borrado`";

                $titulo = "Ventas";

                if ( $noVentas = ($tag != MenuPanel::menuVentas) )

                {

                    $item = Categoria::find($id);

                    $tag = $item->tipo;

                    $titulo = ucfirst($tag) . " " . $item->titulo;

                    $sql .= " AND `id_{$tag}`='{$id}'";

                    #--

                    $lineas = LineaVenta::selectRaw("`id_producto`, `id_venta`, DATE_FORMAT(fecha_hora,'%Y-%m') AS 'periodo', SUM(`cantidad`) AS 'cantidad', SUM(`subtotal`) AS 'monto'")->whereHas("hasArticulo", function ($query) use ($sql) {

                        $query->whereRaw($sql);

                    })->whereRaw("`atributo` IN (0, 'Egreso', 'Venta_publico', 'Venta_mayorista')")->whereDate("fecha_hora", ">=", $from)->groupBy("periodo")->get();

                    #--

                    $totales_mes[] = array("Periodo", "Cantidad");

                }

                else

                {

                    $ventas = Venta::selectRaw("`id_venta`, `tipo`, DATE_FORMAT(`fecha_hora`,'%Y-%m') AS 'periodo'")
                    ->whereRaw("`tipo` IN ('venta_publico', 'venta_mayorista') AND DATE(`fecha_hora`) >= '{$from}'")
                    ->orderBy("id_venta")->get();

                    $lineas = $value = array();

                    foreach ($ventas as $venta)

                    {

                        $total = floatval($venta->total);

                        $lineas[$venta->periodo][($venta->tipo == "venta_publico") ? 'publico' : 'mayorista'] += $total;

                        $lineas[$venta->periodo]['periodo'] = $venta->periodo;

                        $lineas[$venta->periodo]['monto'] += $total;

                    }

                    $totales_mes[] = array("Periodo", "Mayorista", "Público");

                }

                #--

                $total_periodo[] = array("Periodo", "Total");

                //HArray::varDump($lineas);

                $totalMayor = $totalPublico = 0;

                foreach ($lineas as $linea)

                {

                    list($anio, $mes) = explode("-", $linea['periodo']);

                    $periodo = substr(HDate::$_MESES[$mes - 1], 0, 3) . " {$anio}";

                    $precioDolar = floatval($dolar[$linea['periodo']] ?: $this->config['dolar_paralelo']);

                    if ( !$noVentas )

                    {

                        $totales_mes[] = array(

                            $periodo,

                            $totalMayor = round(($linea['mayorista'] / $precioDolar), 2),

                            $totalPublico = round(($linea['publico'] / $precioDolar), 2)

                        );

                    }

                    else

                    {

                        $totales_mes[] = array($periodo, floatval($linea['cantidad']));

                    }

                    //$total_periodo[] = array($periodo, round(floatval($linea->monto) / floatval($this->config['dolar_paralelo']), 2));

                    $total_periodo[] = array($periodo, $total = round((floatval($linea['monto']) / $precioDolar), 2));

                    $resumen[$periodo] = array(

                        'cantidad' => $linea['cantidad'],

                        'mayorista' => Facturacion::numberFormat($totalMayor),

                        'publico' => Facturacion::numberFormat($totalPublico),

                        'totalUsd' => Facturacion::numberFormat($total),

                        'totalArs' => Facturacion::numberFormat($linea['monto'])

                    );

                }

                //HArray::varDump($totales_mes);

                /*foreach ($lineas as $linea)

                {

                    //$articulo= $lineas->hasArticulo;

                    $string .= "<div class='col-md-7'>({$linea->periodo}) # {$linea->producto} | {$linea->cantidad} > $ {$linea->total}</div>";

                }*/

                //HArray::varDump($string);

                break;

            default:

                exit;

        }

        $this->setPageTitle("Estadísticas últimos 12 meses");

        ob_start();

        ?>

        <style>

            #reporte [class*="col-md"] {

                background: #eee;

                padding: 6px;

            }



            #reporte h4 {

                margin: 0;

                padding: 5px 0;

                background: #5b8dad;

                color: #f7ffe2;

                text-shadow: 0 2px 3px #eee;

            }



            .chart-container {

                width: 100% !important;

            }

        </style>

        <div id="reporte">

            <h3><?= $titulo ?></h3>

            <div class="row text-center">

            <!-- ================================ -->
                <div class="col-md-6 form-group">

                    <h4>Cantidad x Periodo</h4>

                    <div id="pedidos-mes" class="chart-container"></div>

                    <div class="text-left">

                        <?= $resumen_asistencia ?>

                    </div>

                </div>
            <!-- ================================ -->

                <div class="col-md-6 form-group">

                    <?php ?>

                    <?php ?>

                    <h4>Variaci&oacute;n</h4>

                    <div id="variacion-mes" class="chart-container"></div>

                </div>
            <!-- ================================ -->

                <div class="col-md-6 form-group">

                    <h4 class="text-center">Total por Periodo (USD)</h4>

                    <div id="total-periodo" class="chart-container"></div>

                </div>
            <!-- ================================ -->

                <div class="col-md-6 form-group">

                    <?php if ( $resumen ): ?>

                        <table style="width:100%" class="table-responsive table-bordered table-striped">

                            <thead>

                            <tr>

                                <td>Periodo</td>

                                <?php if ( $noVentas ): ?>

                                    <td>Cantidad</td>

                                <?php else: ?>

                                    <td>Mayorista</td>

                                    <td>Público</td>

                                <?php endif; ?>

                                <td>Total</td>

                            </tr>

                            </thead>

                            <?php foreach ($resumen as $periodo => $data): ?>

                                <tr>

                                    <td><?= $periodo ?></td>

                                    <td class="text-right"><?= $data['cantidad'] ?: ($data['mayorista'] . " USD") ?></td>

                                    <?php if ( !$noVentas ): ?>

                                        <td class="text-right"><?= $data['publico'] . " USD" ?></td>

                                    <?php endif; ?>

                                    <td class="amount">U$D <?= $data['totalUsd'] . " ({$data['totalArs']} ARS)" ?></td>

                                </tr>

                            <?php endforeach; ?>

                        </table>

                    <?php endif; ?>

                </div>
            <!-- ================================ -->

            
            <div class="col-md-6 form-group">
                <h4 class="text-center">Productos mas vendidos del mes (ventas publico)</h4>

                <?php if ( $productos_mas_vendidos ): ?>

                    <table style="width:100%" class="table-responsive table-bordered table-striped">

                        <thead>

                        <tr>

                            <!-- <td>Codigo</td> -->

                            <td>ID Producto</td>

                            <td>Cantidad de ventas</td>

                        </tr>

                        </thead>

                        <?php foreach ($productos_mas_vendidos as $producto): ?>

                            <tr>

                                <td class="text-right"><?= $producto['id_producto'] ?></td>

                                <td class=""> <?= $producto['cantidad_ventas']  ?></td>

                            </tr>

                        <?php endforeach; ?>

                    </table>

                <?php endif; ?>

                </div>
            </div>
            <!-- ================================ -->



        </div>

        <script type="text/javascript">

            let chart_conf = [];

            google.charts.load('current', {

                "callback": function () {

                    <?php if(false) : ?>

                    chart_conf.values = <?=json_encode([])?>;

                    chart_conf.titulo = " ";

                    chart_conf.graficoCircular = document.getElementById('chart_div');

                    drawCharts();

                    <?php endif; ?>

                    <?php if($totales_mes) : ?>

                    chart_conf.values = <?=json_encode($totales_mes)?>;

                    chart_conf.barChart = document.getElementById('pedidos-mes');

                    chart_conf.options = {

                        "hAxis": {

                            "title": " ",

                            "titleTextStyle": {"color": 'red'}

                        },

                        "legend": {"position": "top"}

                    };

                    drawCharts();

                    <?php endif; ?>

                    chart_conf.values = <?=json_encode($totales_mes)?>;

                    chart_conf.lineChart = document.getElementById("variacion-mes");

                    chart_conf.options = {

                        "title": "",

                        "curveType": "none",

                        "legend": {"position": "bottom"}

                    };

                    drawCharts();

                    <?php if(true) : ?>

                    chart_conf.values = <?=json_encode($total_periodo)?>;

                    chart_conf.barChart = document.getElementById('total-periodo');

                    chart_conf.options = {

                        "hAxis": {

                            "title": " ",

                            "titleTextStyle": {"color": 'red'}

                        },

                        "legend": {"position": "top"}

                    };

                    drawCharts();

                    <?php endif; ?>

                    function drawCharts()

                    {

                        if ( !(chartData = chart_conf.values) )

                        {

                            console.log("Arreglo de datos no especificados");

                            return;

                        }

                        let chart;

                        let data = google.visualization.arrayToDataTable(chartData);

                        let options = (chart_conf.options || {'title': chart_conf.titulo});

                        options.width = "100%";

                        options.height = "260";

                        if ( typeof chart_conf.graficoCircular !== "undefined" )

                        {

                            chart = new google.visualization.PieChart(chart_conf.graficoCircular);

                        }

                        if ( typeof chart_conf.barChart !== "undefined" )

                        {

                            chart = new google.visualization.ColumnChart(chart_conf.barChart);

                        }

                        if ( typeof (idAreaChart = chart_conf.areaChart) !== "undefined" )

                        {

                            chart = new google.visualization.AreaChart(idAreaChart);

                        }

                        if ( typeof (idLineChart = chart_conf.lineChart) !== "undefined" )

                        {

                            chart = new google.visualization.LineChart(idLineChart);

                        }

                        chart.draw(data, options);

                        chart_conf = {};

                    }

                },

                "packages": ['corechart', 'bar']

            });

        </script>

        <?php

        $this->setBody(ob_get_clean(), true);

    }



    public function onlineSearch()

    {

        $txt = trim($_GET['q']);

        $result = ExportOpts::yahooImagenOnline($txt, 4);

        //HArray::varDump($result);

        echo $txt . "<br/>";

        foreach ($result as $res)

        {

            echo "<img src='{$res['image_src']}' style='margin-right:8px;display: inline-block;max-width:100%'>";

        }

    }

}