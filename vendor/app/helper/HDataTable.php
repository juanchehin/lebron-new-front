<?php



class HDataTable

{

    private $columns;

    private $rows;

    private $search_box = true;

    private $search_box_cc = false;

    private $search_producto_vencido = false;

    private $search_dni_apellido_nombre = false;

    private $rango_precios = false;    

    private $clean_filter = false;

    private $filtro_sucursal = false;

    private $filtro_fecha = false;

    private $filtro_fecha_quimico = false;

    private $filtro_fecha_incidencia = false;

    private $filtro_fecha_pedido = false;

    private $filtro_fecha_cuentas = false;    

    private $filtro_fecha_mayorista = false;

    private $filtro_zona = false;

    private $filtro_ventas_pendientes = false;

    private $filtro_ventas_pendientes_quimicos = false;

    private $filtro_ventas_pendientes_pedidos = false;

    private $filtro_cuentas_pendientes = false;

    private $buscador = true;

    private $date_range = array(true, true);

    private $select_periodo = array();

    private $html_control;

    private $keys = array();

    private $funciones = true;

    private $responsive_show_label = true;

    private $fixed_head = null;

    private $data_source;

    private $data_class;

    private $empty_table_text = "No se encontraron registros";

    public static $_meses = array(

        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",

        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"

    );



    public function __construct()

    {

        $this->setFunciones();

        $this->setClass(CURRENT_CLASS);

    }



    public function setHideSearchBox()

    {

        $this->search_box = false;

        return $this;

    }

    public function setSearchBoxCC()

    {

        $this->search_box_cc = true;

        return $this;

    }

    public function setSearchBoxProdVencido()

    {

        $this->search_producto_vencido = true;

        return $this;

    }

    public function clean_filter()

    {

        $this->clean_filter = true;

        return $this;

    }

    public function setHideBuscador()

    {

        $this->buscador = false;

        return $this;

    }

    public function setMostrarBuscador()

    {

        $this->buscador = true;

        return $this;

    }

    public function setRangoPrecios()

    {

        $this->rango_precios = true;

        return;

    }

    public function setFiltroSucursales()

    {

        $this->filtro_sucursal = true;

        return;

    }

    public function setFiltroFecha()

    {

        $this->filtro_fecha = true;

        return;

    }

    public function setFiltroFechaQuimico()

    {

        $this->filtro_fecha_quimico = true;

        return;

    }

    public function setFiltroFechaIncidencia()

    {

        $this->filtro_fecha_incidencia = true;

        return;

    }

    public function setFiltroFechaPedido()

    {

        $this->filtro_fecha_pedido = true;

        return;

    }

    public function setFiltroFechaCuentas()

    {

        $this->filtro_fecha_cuentas = true;

        return;

    }

    public function setFiltroVentasPendientesQuimico()

    {

        $this->filtro_ventas_pendientes_quimicos = true;

        return;

    }

    public function setFiltroVentasPendientesPedidos()

    {

        $this->filtro_ventas_pendientes_pedidos = true;

        return;

    }

    public function setFiltroCuentasPendientes()

    {

        $this->filtro_cuentas_pendientes = true;

        return;

    }

    public function setFiltroBusquedaClienteDniApellidoNombre()

    {

        $this->search_dni_apellido_nombre = true;

        return;

    }

    public function setFiltroFechaMayorista()

    {

        $this->filtro_fecha_mayorista = true;

        return;

    }

    public function setClass($class)

    {

        $this->data_class = $class;

        return;

    }



    public function setDataSource($url, $params = array())

    {

        $params['dt'] = 1;

        $this->data_source = $url . "?" . http_build_query($params);

        return;

    }



    /**

     * @param array $fk

     */

    public function setKeys($key, $value = null)

    {

        if ( is_array($key) )

        {

            foreach ($key as $k => $v)

            {

                $this->keys[$key] = $v;

            }

            return;

        }

        $this->keys[$key] = $value;

        return;

    }



    /**

     * @param string $empty_table_text

     */

    public function setEmptyTableText($empty_table_text)

    {

        $this->empty_table_text = $empty_table_text;

        return;

    }



    public function setHtmlControl($value)

    {

        $this->html_control = $value;

        return;

    }



    public function setFunciones($value = true)

    {

        $this->funciones = $value;

        return;

    }

    



    public function setDisableFunciones()

    {

        $this->funciones = false;

        return;

    }



    public function setHideDateRange()

    {

        $this->date_range = array();

        return;

    }

    public function setFiltroZona()

    {

        $this->filtro_zona = true;

        return;

    }

    public function setFiltroVentasPendientes()

    {

        $this->filtro_ventas_pendientes = true;

        return;

    }



    public function setDateRangeConf($inputs = true, $criterios = true)

    {

        $this->date_range[0] = $inputs;

        $this->date_range[1] = $criterios;

        return;

    }



    public function setSelectPeriodo($todo_anio = true, $desde_anio = null)

    {

        $this->select_periodo[0] = $desde_anio ?: date('Y') - 1;

        $this->select_periodo[1] = $todo_anio;

        return;

    }



    public function setColumns($value)

    {

        $this->columns = $value;

        return;

    }



    public function setRows($value)

    {

        $this->rows = $value;

        return;

    }



    public function noResponsiveShowLabel()

    {

        $this->responsive_show_label = false;

        return;

    }



    /**

     * @param bool $fixed_head

     */

    public function setFixedHead()

    {

        $this->fixed_head = "table-fixed";

        return;

    }



    private function _inicioFin($periodo = null)

    {

        $fechas = array();

        switch ( $periodo )

        {

            case  'semana' :

                //$hoy = date('d/m/Y');

                $dia_semana = date('w');

                $fechas[] = date('d/m/Y', strtotime("-{$dia_semana} day"));

                $fechas[] = date('d/m/Y', strtotime("+" . (6 - $dia_semana) . ' day'));

                break;

            case 'anio' :

                $fechas = array(

                    '01/01/' . date('Y'),

                    '31/12/' . date('Y')

                );

                break;

            default :

                $fecha = new DateTime();

                $fechas = array(

                    '01/' . date('m/Y'),

                    $fecha->modify('last day of this month')->format('d/m/Y')

                );

                break;

        }



        return $fechas;

    }



    public function drawTable()

    {


        if ( !$this->data_source && !$this->data_class )

        {

            return "Definir correctamente la clase Actual";

        }



        $data_source = $this->data_source ?: $this->data_class . "/getRows?dt=1";

        // dd($data_source);



        ob_start();

        ?>

        <style type="text/css">

            .data-table {

                color: #444;

                font-size: 13px;

                width: 100%;

            }



            .data-table thead th {

                background: #aaa;

                font-weight: 600;

                text-transform: uppercase;

                color: #000;

            }



            .data-table .btn {

                padding: 2px 4px;

                margin-top: 0;

                margin-bottom: 0;

                margin-left: 4px;

                font-size: 12px;

            }



            .data-table tr td, .data-table tr th {

                padding: 4px 6px !important;

                border: 1px solid #ddd;

            }



            .data-table .fa {

                font-size: 17px;

            }



            .data-table tbody tr:not(.not):nth-child(odd) {

                background: #eee;

            }



            .data-table tbody tr.not td {

                background: #fff;

                border: none;

            }



            .data-table tbody tr:not(.not):hover {

                background: #d9edf7;

            }



            .data-table tr td a {

                display: inline-block;

                margin-right: 8px;

            }



            .data-table tr td a:last-child {

                margin-right: 0;

            }



            .data-table tr.dt-empty {

                width: 100%;

            }



            #filter-group {

                margin-bottom: 5px;

            }



            #filter-group .fecha-criterios {

                list-style: none;

                margin-bottom: 3px;

                padding: 0;

            }



            #filter-group .fecha-criterios li {

                display: inline-block;

            }



            #filter-group .fa {

                font-size: 18px;

            }



            #filter-group input, #filter-group select, #filter-group button, #filter-group a {

                display: inline-block;

                width: auto;

                font-size: 13px;

                padding-left: 5px;

                margin-bottom: 3px;

            }



            #records_count {

                font-style: italic;

                color: #1e7ae3;

                font-size: 15px;

                line-height: 2px;

                width: auto;

            }



            .table-fixed tbody {

                display: block;

                height: 188px;

                overflow: auto;

            }



            .table-fixed thead th {

                border: none;

            }



            .table-fixed thead, .table-fixed tbody tr {

                display: table;

                width: 100%;

                table-layout: fixed;

            }



            .pagination {

                margin: 0;

                display: block;

            }



            .hlabel {

                display: none;

                font-size: 11px;

                margin: 5px auto;

                text-transform: uppercase;

                line-height: 0;

                color: #2b2b2b;

                font-weight: 600;

            }



            @media screen and (max-width: 995px) {

                .hlabel {

                    display: block;

                }



                .data-table thead {

                    display: none;

                }



                .data-table tr td, .data-table tr {

                    display: block;

                    width: 100%;

                    text-align: center;

                }

            }

        </style>

        <div class="">

            <div class="" id="filter-group">

                <!-- ======== Buscador por codigo o producto =========== -->
                <?php if ( $this->search_box ) : ?>

                    <input type="text" id="search_box" name="dt_search" class="form-control" placeholder="B&uacute;squeda"/>

                <?php endif; ?>                
                <!-- ======== Fin Buscador por codigo o producto =========== -->

                 <!-- ======== Buscador por codigo o producto =========== -->
                 <?php if ( $this->search_box_cc ) : ?>
                    <input type="text" id="search_box_cc" name="search_box_cc" class="form-control" placeholder="B&uacute;squeda por cliente"/>
                <?php endif; ?>                
                <!-- ======== Fin Buscador por codigo o producto =========== -->

                <!-- ======== Buscador por producto vencido =========== -->
                <?php if ( $this->search_producto_vencido ) : ?>
                    <input type="text" id="search_producto_vencido" name="search_producto_vencido" class="form-control" placeholder="B&uacute;squeda"/>
                    <a href="javascript:void(0)" id="btn-search" title="Buscar"><i class="fa fa-search"></i></a>&nbsp;
                <?php endif; ?>                
                <!-- ======== Fin Buscador por producto vencido =========== -->

                <!-- Rango de fechas -->
                <?php if ( $conf = $this->date_range ) : ?>

                    <?php if ( $conf[1] ) : ?>

                        <ul class="fecha-criterios">

                            <li><a href="javascript:void(0)" rel="dia" onclick="_criterio(this)">En el d&iacute;a</a></li>

                            <li><a href="javascript:void(0)" rel="semana" onclick="_criterio(this)">Semana</a></li>

                            <li><a href="javascript:void(0)" rel="mes" onclick="_criterio(this)">Mes</a></li>

                            <li><a href="javascript:void(0)" rel="anio" onclick="_criterio(this)">A&ntilde;o</a></li>

                        </ul>

                    <?php endif; ?>

                    <input placeholder="Desde" id="desde" type="<?= ($conf[0] ? 'text' : 'hidden') ?>" class="form-control"/>

                    <input placeholder="Hasta" id="hasta" type="<?= ($conf[0] ? 'text' : 'hidden') ?>" class="form-control"/>

                <?php endif; ?>
                <!-- Fin Rango de fechas -->

                
                <!-- ============ Filtro ventas pendientes quimicos ========== -->
                <?php if ( $this->filtro_ventas_pendientes_quimicos ) : ?>                    
                <select id='ventas_pendientes_quimicos' name='ventas_pendientes_quimicos' class='form-control' required>
                        <option value='T' selected="selected">Todos</option>
                        <option value='P'>Pendientes</option>
                    </select>
                <?php endif; ?>
                <!-- ============ Fin Filtro ventas pendientes quimicos ========== -->

                <!-- ============ Filtro ventas pendientes ========== -->
                <?php if ( $this->filtro_ventas_pendientes_pedidos ) : ?>                    
                <select id='ventas_pendientes_pedidos' name='ventas_pendientes_pedidos' class='form-control' required>
                        <option value='T' selected="selected">Todos</option>
                        <option value='P'>Pendientes</option>
                        <option value='C'>Confirmados</option>
                    </select>
                <?php endif; ?>
                <!-- ============ Fin Filtro ventas pendientes quimicos ========== -->

                <!-- ============ Filtro ventas pendientes quimicos ========== -->
                <?php if ( $this->filtro_cuentas_pendientes ) : ?>                    
                    <select id='cuentas_pendientes' name='cuentas_pendientes' class='form-control' required>
                        <option value='T' selected="selected">Todos</option>
                        <option value='P'>Pendientes</option>
                    </select>
                <?php endif; ?>
                <!-- ============ Fin Filtro ventas pendientes quimicos ========== -->

                <!-- ======== Buscador por dni/apellido nombre =========== -->
                <?php if ( $this->search_dni_apellido_nombre ) : ?>
                    <input type="text" id="btn-search-dni-cliente" name="btn-search-dni-cliente" class="form-control" placeholder="B&uacute;squeda por dni"/>
                    <input type="text" id="btn-search-apellido-nombre-cliente" name="btn-search-apellido-nombre-cliente" class="form-control" placeholder="B&uacute;squeda apellido/nombre"/>
                    <a href="javascript:void(0)" id="btn-search-cliente" title="Buscar"><i class="fa fa-search"></i></a>&nbsp;
                <?php endif; ?>                
                <!-- ======== Fin Buscador por dni/apellido nombre =========== -->

                <!-- ======== Periodo =========== -->
                <?php if ( $conf = $this->select_periodo ) : ?>

                    <select id="mes" class="form-control">

                        <?php

                        for ($m = 0; $m < 12; $m++)

                        {

                            if ( !$m && $conf[1] )

                            {

                                echo "<option value=''>Todo el A&ntilde;o</option>";

                            }

                            $mes = str_pad($m + 1, 2, 0, 0);

                            echo "<option value='{$mes}'>" . static::$_meses[$mes - 1] . "</option>";

                        }

                        ?>

                    </select>

                    <select id="anio" class="form-control">

                        <?php

                        for ($anio = date('Y') + 1; $anio >= $conf[0]; $anio--)

                        {

                            echo "<option>{$anio}</option>";

                        }

                        ?>

                    </select>

                <?php endif; ?>
                <!-- ========= Fin Periodo ================== -->
                <?= $this->html_control ?>
                <?php if ( $this->buscador ) : ?>
                    <span class="a-dt-actions">
                        <a href="javascript:void(0)" id="btn-search" title="Buscar"><i class="fa fa-search"></i></a>&nbsp;
                        <a href="javascript:void(0)" id="a-reset" title="Restablecer"><i class="fa fa-stop-circle"></i></a>
                    </span>
                <?php endif; ?> 
                <!-- ============ Rango de precios ========== -->
                <?php if ( $this->rango_precios ) : ?>


                    <input placeholder="Precio Desde" id="precio_desde" type="text" class="form-control"/>

                    <input placeholder="Precio Hasta" id="precio_hasta" type="text" class="form-control"/>

                    <span class="a-dt-actions">

                        <a href="javascript:void(0)" id="btn-search-precios" title="Buscar por Precio"><i class="fa fa-search"></i></a>&nbsp;

                        <!-- <a href="javascript:void(0)" id="a-reset" title="Restablecer"><i class="fa fa-stop-circle"></i></a> -->

                    </span>

                <?php endif; ?>           
                <!--============ Fin Rango de precios ============-->

                 <!-- ============ Filtro precio ========== -->
                <?php if ( $this->filtro_sucursal ) : ?>

                    <input placeholder="Precio Desde" id="precio_desde" type="text" class="form-control"/>

                    <input placeholder="Precio Hasta" id="precio_hasta" type="text" class="form-control"/>

                    <span class="a-dt-actions">

                        <a href="javascript:void(0)" id="btn-search-precios" title="Buscar por Precio"><i class="fa fa-search"></i></a>&nbsp;

                        <!-- <a href="javascript:void(0)" id="a-reset" title="Restablecer"><i class="fa fa-stop-circle"></i></a> -->

                    </span>

                <?php endif; ?>           
                <!--============ Fin Filtro precio ============-->

                 <!-- ============ Filtro sucursal ========== -->
                 <?php if ( $this->filtro_sucursal ) : ?>

                    <input placeholder="Precio Desde" id="precio_desde" type="text" class="form-control"/>

                    <input placeholder="Precio Hasta" id="precio_hasta" type="text" class="form-control"/>

                    <span class="a-dt-actions">

                        <a href="javascript:void(0)" id="btn-search-precios" title="Buscar por Precio"><i class="fa fa-search"></i></a>&nbsp;

                        <!-- <a href="javascript:void(0)" id="a-reset" title="Restablecer"><i class="fa fa-stop-circle"></i></a> -->

                    </span>

                    <?php endif; ?>           
                    <!--============ Fin Filtro sucursal ============-->

                    <!-- ============ Filtro fecha gasto ========== -->
                    <?php if ( $this->filtro_fecha ) : ?>
                        <span class="a-dt-actions">
                            <input placeholder="fechaGasto" id="fecha-gasto" type="date" class="form-control"/>
                            <a href="javascript:void(0)" id="btn-filtro-fecha-gasto" title="Buscar"><i class="fa fa-search"></i></a>&nbsp;
                        </span>
                    <?php endif; ?>

                    <!-- ============ Filtro fecha incidencia ========== -->
                    <?php if ( $this->filtro_fecha_incidencia ) : ?>
                        <span class="a-dt-actions">
                            <input placeholder="fechaIncidencia" id="fecha-incidencia" type="date" class="form-control"/>
                            <a href="javascript:void(0)" id="btn-filtro-fecha-incidencia" title="Buscar"><i class="fa fa-search"></i></a>&nbsp;
                        </span>
                    <?php endif; ?>

                    <!-- ============ Filtro fecha venta quimico ========== -->
                    <?php if ( $this->filtro_fecha_quimico ) : ?>
                        <span class="a-dt-actions">
                            <input placeholder="fechaVentaQuimico" id="fecha-quimico" type="date" class="form-control"/>
                            <a href="javascript:void(0)" id="btn-filtro-quimico" title="Buscar"><i class="fa fa-search"></i></a>&nbsp;
                        </span>
                    <?php endif; ?>

                    <!-- ============ Filtro fecha venta quimico ========== -->
                    <?php if ( $this->filtro_fecha_pedido ) : ?>
                        <span class="a-dt-actions">
                            <input placeholder="fechaVentaPedido" id="fecha-pedido" type="date" class="form-control"/>
                            <a href="javascript:void(0)" id="btn-filtro-pedido" title="Buscar"><i class="fa fa-search"></i></a>&nbsp;
                        </span>
                    <?php endif; ?>

                     <!-- ============ Filtro fecha cuentas corrientes ========== -->
                     <?php if ( $this->filtro_fecha_cuentas ) : ?>
                        <span class="a-dt-actions">
                            <input placeholder="fechaVentaQuimico" id="fecha-cuenta" type="date" class="form-control"/>
                            <a href="javascript:void(0)" id="btn-filtro-cuentas-corrientes" title="Buscar"><i class="fa fa-search"></i></a>&nbsp;
                        </span>
                    <?php endif; ?>
                
                    <!-- ============ Filtro fecha venta mayorista ========== -->
                    <?php if ( $this->filtro_fecha_mayorista ) : ?>
                        <span class="a-dt-actions">
                            <input placeholder="fechaVentaMayorista" id="fecha-mayorista" type="date" class="form-control"/>
                            <a href="javascript:void(0)" id="btn-filtro-fecha-mayorista" title="Buscar"><i class="fa fa-search"></i></a>&nbsp;
                        </span>
                    <?php endif; ?>

                    <!-- ============ Filtro zona ========== -->
                    <?php if ( $this->filtro_zona ) : ?>
                        <select id='zona' name='zona' class='form-control' required>";

                            <option value='N' selected='selected'>Seleccione una Zona</option>

                            <option value='S'>Santiago</option>

                            <option value='T'>Tucuman Sur</option>

                            <option value='G'>Gimnasio y centro</option>

                        </select>
                    <?php endif; ?>
                    <!-- ============ Fin Filtro zona ========== -->

                      <!-- ============ Filtro ventas pendientes ========== -->
                      <?php if ( $this->filtro_ventas_pendientes ) : ?>
                        <select id='zona' name='zona' class='form-control' required>";

                            <option value='N' selected='selected'>Seleccione una Zona</option>

                            <option value='S'>Santiago</option>

                            <option value='T'>Tucuman Sur</option>

                            <option value='G'>Gimnasio y centro</option>

                        </select>
                    <?php endif; ?>
                    <!-- ============ Fin Filtro ventas pendientes ========== -->
                    
                <!-- ============ Limpiador de filtros ========== -->
                <?php if ( $this->clean_filter ) : ?>
                     <span class="a-dt-actions">
                         <a href="javascript:void(0)" id="a-reset" title="Restablecer"><i class="fa fa-stop-circle"></i></a>
                     </span>
                 <?php endif; ?> 
                 <!-- ============ Fin Limpiador de filtros ========== -->
                 
           

                </div>
            <div class="table-container">

                <span id="records_count">&nbsp;</span>

                <table cellspacing="0" class="data-table <?= $this->fixed_head ?>" data-url="<?= $data_source ?>">

                    <?php if ( $this->columns ) : ?>

                        <thead class="hidden-xs">

                        <tr>

                            <?php

                            if ( is_array($this->columns) )

                            {

                                foreach ($this->columns as $index => $column)

                                {

                                    $tag_id = $index + 1;

                                    $class = explode('.', $column);

                                    unset($class[0]);

                                    echo "<th class='" . implode(" ", $class) . "' id='column-{$tag_id}'>" . preg_replace("#\..*#", "", $column) . "</th>";

                                }

                            }

                            else

                            {

                                echo $this->columns;

                            }

                            ?>

                        </tr>

                        </thead>

                    <?php endif; ?>

                    <tbody rel="table-body" id="table-body-<?= ($tb = uniqid()) ?>">

                        <?= $this->rows ?>

                    </tbody>

                </table>

            </div>

        </div>

        <!-- =============== Empiza JS ================== -->

        <script type="text/javascript">

            if ( typeof $ === "undefined" )
            {
                throw new Error('No JQuery');
            }

            var table_body = $('#table-body-<?=$tb?>');

            if ( !$('#filter-group').find('input, select').length )
            {
                $('.a-dt-actions').html("");
            }

            <?php if ( $this->funciones ) : echo "\n"; ?>

            const table = 'table_<?=$this->data_class?>';

            var values = {}, dt_buscar = false;

            var row_count = 0;

            var storage = getLS(table) ? JSON.parse(getLS(table)) : {};

            //----

            $('#desde, #hasta').calendario({'yearRange': '-4:+0', 'defaultDate': new Date(), 'maxDate': new Date()});



            function _criterio(option)
            {

                var criterio = option.getAttribute("rel");

                option.setAttribute("href", "javascript:void(0)");

                var fechas = [];

                switch ( criterio )
                {

                    case 'semana' :

                        fechas = <?=json_encode($this->_inicioFin('semana'))?>;

                        break;

                    case 'mes' :

                        fechas = <?=json_encode($this->_inicioFin())?>;

                        break;

                    case 'anio' :

                        fechas = <?=json_encode($this->_inicioFin('anio'))?>;

                        break;

                    default :

                        for (var i = 0; i < 2; i++)

                        {

                            fechas[i] = '<?=date('d/m/Y')?>'

                        }

                        break;

                }

                $('#desde').val(fechas[0]);

                $('#hasta').val(fechas[1]);

                set_filter();

            }



            $('#filter-group').find('input[type="text"], select').attr("autocomplete", "off").on("keyup input", function () {

                var txt = this.value;

                if ( this.localName === "select" || (!txt || txt.length > 2) )

                {

                    $('#btn-search').trigger('click');

                    return;

                }

            });



            //----
            // Egresos - Registros
            function get_rows()
            {                
				table_body.html("<tr><td colspan='<?=sizeof($this->columns)?>' style='font-weight:600;text-align:center'>Espere...</td></tr>");
                
                // values contiene los nombres de los id de los filtros
                $.post('!' + $('table.data-table').data('url'), values, function (result) {

                    table_body.html(result);

                    row_count = parseInt(table_body.find('[data-count]').data("count"));

                    if ( !isNaN(row_count) )
                    {
                        document.getElementById('records_count').innerHTML = "N&deg; de Registros: <b>" + row_count + "</b>";
                    }

                    //before_send();

                    if ( row_count < 1 )
                    {
                        table_body.html("<tr class='dt-empty'><td colspan='15' id='dt-empty' align='center'><?=$this->empty_table_text?></td></tr>");

                        return;
                    }

                    //----

                    <?php if($this->responsive_show_label): echo "\n"; ?>

                    var header_count = $('table.data-table th').length;

                    if ( header_count > 0 )
                    {
                        table_body.find('tr:not(.not) td').each(function (k, v) {

                            if ( (k + 1) % header_count )
                            {
                                //$(v).prepend("<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");

                                v.insertAdjacentHTML('afterbegin', "<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");
                            }
                        });
                    }

                    <?php endif; echo "\n";?>

                });

                setLS(table, JSON.stringify(values));

            }

            //----
            function get_rows_gastos_fecha()
            {

                //before_send();

				table_body.html("<tr><td colspan='<?=sizeof($this->columns)?>' style='font-weight:600;text-align:center'>Espere...</td></tr>");

                var fechaGasto = document.getElementById("fecha-gasto").value;

                // values contiene los nombres de los id de los filtros
                $.post('!' + 'AdminGastos/getRows?fechaGasto=' + fechaGasto, values, function (result) {

                    table_body.html(result);

                    row_count = parseInt(table_body.find('[data-count]').data("count"));

                    if ( !isNaN(row_count) )
                    {
                        document.getElementById('records_count').innerHTML = "N&deg; de Registros: <b>" + row_count + "</b>";
                    }

                    //before_send();

                    if ( row_count < 1 )
                    {
                        table_body.html("<tr class='dt-empty'><td colspan='15' id='dt-empty' align='center'><?=$this->empty_table_text?></td></tr>");

                        return;
                    }

                    //----

                    <?php if($this->responsive_show_label): echo "\n"; ?>

                    var header_count = $('table.data-table th').length;

                    if ( header_count > 0 )
                    {
                        table_body.find('tr:not(.not) td').each(function (k, v) {

                            if ( (k + 1) % header_count )
                            {
                                //$(v).prepend("<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");

                                v.insertAdjacentHTML('afterbegin', "<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");
                            }
                        });
                    }

                    <?php endif; echo "\n";?>

                });

                setLS(table, JSON.stringify(values));

            }

            //----

            function get_rows_ventas_quimicos()
            {

                //before_send();

				table_body.html("<tr><td colspan='<?=sizeof($this->columns)?>' style='font-weight:600;text-align:center'>Espere...</td></tr>");

                var fechaVentaQuimico = document.getElementById("fecha-quimico").value;
                var venta_quimico_pendiente = document.getElementById("ventas_pendientes_quimicos").value;


                // values contiene los nombres de los id de los filtros
                $.post('!' + 'AdminVentaQuimicos/getRows?fechaVentaQuimico=' + fechaVentaQuimico + '&venta_quimico_pendiente=' + venta_quimico_pendiente, values, function (result) {

                    table_body.html(result);

                    row_count = parseInt(table_body.find('[data-count]').data("count"));

                    if ( !isNaN(row_count) )
                    {
                        document.getElementById('records_count').innerHTML = "N&deg; de Registros: <b>" + row_count + "</b>";
                    }

                    //before_send();

                    if ( row_count < 1 )
                    {
                        table_body.html("<tr class='dt-empty'><td colspan='15' id='dt-empty' align='center'><?=$this->empty_table_text?></td></tr>");

                        return;
                    }

                    //----

                    <?php if($this->responsive_show_label): echo "\n"; ?>

                    var header_count = $('table.data-table th').length;

                    if ( header_count > 0 )
                    {
                        table_body.find('tr:not(.not) td').each(function (k, v) {

                            if ( (k + 1) % header_count )
                            {
                                //$(v).prepend("<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");

                                v.insertAdjacentHTML('afterbegin', "<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");
                            }
                        });
                    }

                    <?php endif; echo "\n";?>

                });

                setLS(table, JSON.stringify(values));

            }

            //----
            function get_rows_clientes()
            {

				table_body.html("<tr><td colspan='<?=sizeof($this->columns)?>' style='font-weight:600;text-align:center'>Espere...</td></tr>");

                var search_cliente_dni = document.getElementById("btn-search-dni-cliente").value;
                var search_cliente_apellido_nombre = document.getElementById("btn-search-apellido-nombre-cliente").value;

                // values contiene los nombres de los id de los filtros
                $.post('!' + 'AdminCustomers/getRows?search_cliente_dni=' + search_cliente_dni + '&search_cliente_apellido_nombre=' + search_cliente_apellido_nombre, values, function (result) {

                    table_body.html(result);

                    row_count = parseInt(table_body.find('[data-count]').data("count"));

                    if ( !isNaN(row_count) )
                    {
                        document.getElementById('records_count').innerHTML = "N&deg; de Registros: <b>" + row_count + "</b>";
                    }

                    //before_send();

                    if ( row_count < 1 )
                    {
                        table_body.html("<tr class='dt-empty'><td colspan='15' id='dt-empty' align='center'><?=$this->empty_table_text?></td></tr>");

                        return;
                    }

                    //----

                    <?php if($this->responsive_show_label): echo "\n"; ?>

                    var header_count = $('table.data-table th').length;

                    if ( header_count > 0 )
                    {
                        table_body.find('tr:not(.not) td').each(function (k, v) {

                            if ( (k + 1) % header_count )
                            {
                                //$(v).prepend("<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");

                                v.insertAdjacentHTML('afterbegin', "<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");
                            }
                        });
                    }

                    <?php endif; echo "\n";?>

                });

                setLS(table, JSON.stringify(values));

            }

             //----
             function get_rows_incidencias()
            {

                //before_send();

				table_body.html("<tr><td colspan='<?=sizeof($this->columns)?>' style='font-weight:600;text-align:center'>Espere...</td></tr>");

                var fechaIncidencia = document.getElementById("fecha-incidencia").value;

                // values contiene los nombres de los id de los filtros
                $.post('!' + 'AdminIncidencias/getRows?fechaIncidencia=' + fechaIncidencia, values, function (result) {

                    table_body.html(result);

                    row_count = parseInt(table_body.find('[data-count]').data("count"));

                    if ( !isNaN(row_count) )
                    {
                        document.getElementById('records_count').innerHTML = "N&deg; de Registros: <b>" + row_count + "</b>";
                    }

                    //before_send();

                    if ( row_count < 1 )
                    {
                        table_body.html("<tr class='dt-empty'><td colspan='15' id='dt-empty' align='center'><?=$this->empty_table_text?></td></tr>");

                        return;
                    }

                    //----

                    <?php if($this->responsive_show_label): echo "\n"; ?>

                    var header_count = $('table.data-table th').length;

                    if ( header_count > 0 )
                    {
                        table_body.find('tr:not(.not) td').each(function (k, v) {

                            if ( (k + 1) % header_count )
                            {
                                //$(v).prepend("<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");

                                v.insertAdjacentHTML('afterbegin', "<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");
                            }
                        });
                    }

                    <?php endif; echo "\n";?>

                });

                setLS(table, JSON.stringify(values));

            }

            //----

            function get_rows_ventas_pedidos()
            {

                //before_send();

				table_body.html("<tr><td colspan='<?=sizeof($this->columns)?>' style='font-weight:600;text-align:center'>Espere...</td></tr>");

                var fechaVentaPedido = document.getElementById("fecha-pedido").value;
                var ventas_pendientes_pedidos = document.getElementById("ventas_pendientes_pedidos").value;

                // values contiene los nombres de los id de los filtros
                $.post('!' + 'AdminPedidos/getRows?fechaVentaPedido=' + fechaVentaPedido + '&venta_pedido_pendiente=' + ventas_pendientes_pedidos, values, function (result) {

                    table_body.html(result);

                    row_count = parseInt(table_body.find('[data-count]').data("count"));

                    if ( !isNaN(row_count) )
                    {
                        document.getElementById('records_count').innerHTML = "N&deg; de Registros: <b>" + row_count + "</b>";
                    }

                    //before_send();

                    if ( row_count < 1 )
                    {
                        table_body.html("<tr class='dt-empty'><td colspan='15' id='dt-empty' align='center'><?=$this->empty_table_text?></td></tr>");

                        return;
                    }

                    //----

                    <?php if($this->responsive_show_label): echo "\n"; ?>

                    var header_count = $('table.data-table th').length;

                    if ( header_count > 0 )
                    {
                        table_body.find('tr:not(.not) td').each(function (k, v) {

                            if ( (k + 1) % header_count )
                            {
                                //$(v).prepend("<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");

                                v.insertAdjacentHTML('afterbegin', "<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");
                            }
                        });
                    }

                    <?php endif; echo "\n";?>

                });

                setLS(table, JSON.stringify(values));

            }

            function get_rows_cuentas_corrientes()
            {

                //before_send();

				table_body.html("<tr><td colspan='<?=sizeof($this->columns)?>' style='font-weight:600;text-align:center'>Espere...</td></tr>");

                var fechaCuentaCorriente = document.getElementById("fecha-cuenta").value;
                var cuentas_pendientes = document.getElementById("cuentas_pendientes").value;
                var search_cliente_cc = document.getElementById("search_box_cc").value;

                // values contiene los nombres de los id de los filtros
                $.post('!' + 'AdminCuentasCorrientes/getRows?fechaCuentaCorriente=' + fechaCuentaCorriente + '&cuentas_pendientes=' + cuentas_pendientes + '&search_cliente_cc='  + search_cliente_cc, values, function (result) {

                    table_body.html(result);

                    row_count = parseInt(table_body.find('[data-count]').data("count"));

                    if ( !isNaN(row_count) )
                    {
                        document.getElementById('records_count').innerHTML = "N&deg; de Registros: <b>" + row_count + "</b>";
                    }

                    //before_send();

                    if ( row_count < 1 )
                    {
                        table_body.html("<tr class='dt-empty'><td colspan='15' id='dt-empty' align='center'><?=$this->empty_table_text?></td></tr>");

                        return;
                    }

                    //----

                    <?php if($this->responsive_show_label): echo "\n"; ?>

                    var header_count = $('table.data-table th').length;

                    if ( header_count > 0 )
                    {
                        table_body.find('tr:not(.not) td').each(function (k, v) {

                            if ( (k + 1) % header_count )
                            {
                                //$(v).prepend("<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");

                                v.insertAdjacentHTML('afterbegin', "<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");
                            }
                        });
                    }

                    <?php endif; echo "\n";?>

                });

                setLS(table, JSON.stringify(values));

            }

            // --
            function get_rows_cuentas_corrientes()
            {

                //before_send();

				table_body.html("<tr><td colspan='<?=sizeof($this->columns)?>' style='font-weight:600;text-align:center'>Espere...</td></tr>");

                var fechaCuentaCorriente = document.getElementById("fecha-cuenta").value;
                var cuentas_pendientes = document.getElementById("cuentas_pendientes").value;
                var search_cliente_cc = document.getElementById("search_box_cc").value;

                // values contiene los nombres de los id de los filtros
                $.post('!' + 'AdminCuentasCorrientes/getRows?fechaCuentaCorriente=' + fechaCuentaCorriente + '&cuentas_pendientes=' + cuentas_pendientes + '&search_cliente_cc='  + search_cliente_cc, values, function (result) {

                    table_body.html(result);

                    row_count = parseInt(table_body.find('[data-count]').data("count"));

                    if ( !isNaN(row_count) )
                    {
                        document.getElementById('records_count').innerHTML = "N&deg; de Registros: <b>" + row_count + "</b>";
                    }

                    //before_send();

                    if ( row_count < 1 )
                    {
                        table_body.html("<tr class='dt-empty'><td colspan='15' id='dt-empty' align='center'><?=$this->empty_table_text?></td></tr>");

                        return;
                    }

                    //----

                    <?php if($this->responsive_show_label): echo "\n"; ?>

                    var header_count = $('table.data-table th').length;

                    if ( header_count > 0 )
                    {
                        table_body.find('tr:not(.not) td').each(function (k, v) {

                            if ( (k + 1) % header_count )
                            {
                                //$(v).prepend("<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");

                                v.insertAdjacentHTML('afterbegin', "<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");
                            }
                        });
                    }

                    <?php endif; echo "\n";?>

                });

                setLS(table, JSON.stringify(values));

            }

            //----

            function get_rows_ventas_mayoristas_fecha()
            {

                //before_send();

				table_body.html("<tr><td colspan='<?=sizeof($this->columns)?>' style='font-weight:600;text-align:center'>Espere...</td></tr>");

                var fechaVentaMayorista = document.getElementById("fecha-mayorista").value;                

                // values contiene los nombres de los id de los filtros
                $.post('!' + 'AdminVentaMayoristas/getRows?fechaVentaMayorista=' + fechaVentaMayorista, values, function (result) {

                    table_body.html(result);

                    row_count = parseInt(table_body.find('[data-count]').data("count"));

                    if ( !isNaN(row_count) )
                    {
                        document.getElementById('records_count').innerHTML = "N&deg; de Registros: <b>" + row_count + "</b>";
                    }

                    //before_send();

                    if ( row_count < 1 )
                    {
                        table_body.html("<tr class='dt-empty'><td colspan='15' id='dt-empty' align='center'><?=$this->empty_table_text?></td></tr>");

                        return;
                    }

                    //----

                    <?php if($this->responsive_show_label): echo "\n"; ?>

                    var header_count = $('table.data-table th').length;

                    if ( header_count > 0 )
                    {
                        table_body.find('tr:not(.not) td').each(function (k, v) {

                            if ( (k + 1) % header_count )
                            {
                                //$(v).prepend("<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");

                                v.insertAdjacentHTML('afterbegin', "<h5 class='hlabel'>" + $('#column-' + (v.cellIndex + 1)).text() + "</h5>");
                            }
                        });
                    }

                    <?php endif; echo "\n";?>

                });

                setLS(table, JSON.stringify(values));

            }


            function set_filter(store)
            {
                <?php foreach ($this->keys as $k => $v ) : echo "\n"; ?>

                    // Agrego los valores en array values[]
                    // 'values' contiene los nombres de los id de los filtros
                    values['<?=$k?>'] = '<?=$v?>';
                <?php endforeach; echo "\n"; ?>

                $('#filter-group').find('select, input[type="text"],input[type="hidden"]').each(function (x, elem) {
                    // _value contiene los valores ingresados en el textbox
                    var filtro = elem.getAttribute('id'), _value = elem.value;  

                    // 'filtro' contiene los id de los textbox
                    // 'elem.value' contiene los valores de los textbox

                    // console.log("elem.value es : ",elem.value)
                    // console.log("storage[filtro] es : ",storage[filtro])

                    // storage[filtro] contiene los valores de los textboxs
                    if ( store && storage && storage[filtro] )
                    {
                        _value = storage[filtro];
                    }

                    if ( _value && _value !== "undefined" )
                    {
                        dt_buscar = true;
                        elem.value = _value;
                    }

                    values[filtro] = _value;

                    // console.log("values[filtro] es : ",values[filtro])

                });

                values.p = (dt_buscar || !store) ? 1 : storage.p;

                get_rows();

            }



            set_filter(true);



            $('#btn-search').click(function () {

                set_filter();

            });

            $('#btn-search-precios').click(function () {
                set_filter();

            });

            $('#btn-filtro-fecha-gasto').click(function () {
                get_rows_gastos_fecha();

            });

            $('#btn-filtro-fecha-incidencia').click(function () {
                get_rows_incidencias();

            });

            $('#btn-filtro-quimico').click(function () {
                get_rows_ventas_quimicos();

            });

            $('#btn-filtro-pedido').click(function () {
                get_rows_ventas_pedidos();
            });

            // Clientes
            $('#btn-search-cliente').click(function () {
                get_rows_clientes();
            });
            // Fin Clientes


            $('#btn-filtro-cuentas-corrientes').click(function () {
                get_rows_cuentas_corrientes();

            });

            
            $('#btn-filtro-fecha-mayorista').click(function () {
                get_rows_ventas_mayoristas_fecha();

            });

            $('#a-reset').on('click', function (e) {

                e.preventDefault();

                $('#filter-group').find('input[type="text"],select').val("");

                $('#zona').val("N");

                $('#search-cliente').val("");

                $('#ventas_pendientes').val("realizada");

                $('#cuentas_pendientes').val("T");

                
                set_filter();

            });



            function get_row_id(row)
            {
                return row.closest('tr, .dt_row').getAttribute('id');
            }



            function set_estado($this)
            {

                var params = {

                    'id': get_row_id($this),

                    'estado': $this.checked ? 1 : 0,

                    'attr': $this.getAttribute('name')

                };



                $.post("!<?=$this->data_class?>/setEstado", params);

            }



            function dt_delete(opt)
            {
                jconfirm(function (res) {

                    if ( res )

                    {

                        $.post("!<?=$this->data_class?>/eliminar", {'id': get_row_id(opt)}, function () {

                            if ( table_body.find('tr:not(.not)').length < 2 )

                            {

                                values.p--;

                            }

                            get_rows();

                        });

                    }

                });

            }

            // =================================================================
            // updateVendidos
            function updateVendido(opt)
            {

                jconfirm(function (res) {

                    if ( res )
                    {

                        $.post("!<?=$this->data_class?>/updateVencidos", {'id': get_row_id(opt)}, function () {

                            if ( table_body.find('tr:not(.not)').length < 2 )
                            {
                                values.p--;
                            }

                            get_rows();

                        });

                    }

                });

            }

            // =================================================================

            function dt_paginate(ref)
            {

                var page = ref.getAttribute('href').replace(/.*=/g, '');

                ref.setAttribute('href', 'javascript:void(0)');

                values.p = page;

                get_rows();

                return false;

            }

            // *********** Confirmar pago - Venta de quimico ********
            function confirmar_venta_quimico(opt)
            {
                jconfirm_compra_quimico(function (res) {

                    if ( res )

                    {

                        $.post("!AdminVentaQuimicos/confirmar_venta_quimico", {'id': get_row_id(opt)}, function () {

                            if ( table_body.find('tr:not(.not)').length < 2 )

                            {

                                values.p--;

                            }

                            get_rows();

                        });

                    }

                });

            }

            // *********** Confirmar pago - cuenta corriente ********
            function confirmar_cuenta_corriente(opt)
            {
                jconfirm_compra_quimico(function (res) {

                    if ( res )

                    {

                        $.post("!AdminCuentasCorrientes/confirmar_cuenta_corriente", {'id': get_row_id(opt)}, function () {

                            if ( table_body.find('tr:not(.not)').length < 2 )

                            {

                                values.p--;

                            }

                            get_rows();

                        });

                    }

                });

            }
            // ***********
            function dt_delete_venta_quimico(opt)
            {
                jconfirm(function (res) {

                    if ( res )

                    {

                        $.post("!<?=$this->data_class?>/eliminarVentaQuimico", {'id': get_row_id(opt)}, function () {

                            if ( table_body.find('tr:not(.not)').length < 2 )

                            {

                                values.p--;

                            }

                            get_rows();

                        });

                    }

                });

            }

            // ***********
            function dt_delete_inversor(opt)
            {
                jconfirm(function (res) {

                    if ( res )

                    {

                        $.post("!<?=$this->data_class?>/eliminarInversor", {'id': get_row_id(opt)}, function () {

                            if ( table_body.find('tr:not(.not)').length < 2 )

                            {

                                values.p--;

                            }

                            get_rows();

                        });

                    }

                });

            }

            // ***********
            function dt_delete_inversion(p_idmovimientos_inversores)
            {
                jconfirm(function (res) {

                    if ( res )

                    {

                        $.post("!AdminInversoresHistorico/eliminarInversion", {'idmovimientos_inversores': p_idmovimientos_inversores}, function () {

                            if ( table_body.find('tr:not(.not)').length < 2 )

                            {

                                values.p--;

                            }

                            get_rows();

                        });

                    }

                });

            }

            // ***********
            function dt_delete_cuenta_corriente(opt)
            {
                jconfirm(function (res) {

                    if ( res )

                    {

                        $.post("!<?=$this->data_class?>/eliminarFilaCuenta", {'id': get_row_id(opt)}, function () {

                            if ( table_body.find('tr:not(.not)').length < 2 )

                            {

                                values.p--;

                            }

                            get_rows();

                        });

                    }

                });

            }

            <?php endif; ?>

        </script>

        <?php

        return ob_get_clean();

    }

}