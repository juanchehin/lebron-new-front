<?php

include_once (__DIR__ . '/wsaa.php');

ini_set("soap.wsdl_cache_enabled", "0");

use Dompdf\Dompdf;
use Dompdf\Options;

require 'vendor/autoload.php';
use GuzzleHttp\Psr7\Request;

class AdminFacturacionElectronica extends AdminMain
{

    // private $client;
    // private $TA;

    const CERT = "/keys/cert";        	# The X.509 certificate in PEM format. Importante setear variable $path
	const PRIVATEKEY = "/keys/key";  	# The private key correspoding to CERT (PEM). Importante setear variable $path
	const PASSPHRASE = "";         				# The passphrase (if any) to sign
	const PROXY_ENABLE = false;
	//https://wsaahomo.afip.gov.ar/ws/services/LoginCms?WSDL // para obtener WSDL
	const URL = "https://wsaahomo.afip.gov.ar/ws/services/LoginCms"; // homologacion (testing)
	// CONST URL = "https://wsaa.afip.gov.ar/ws/services/LoginCms"; // produccion
    // const WSFEURL = "https://wswhomo.afip.gov.ar/wsfev1/service.asmx"; // homologacion wsfev1 (testing)


    // const TA 	= "/xml/TA.xml";        			# Archivo con el Token y Sign
	const WSDL 	= __DIR__ . "/wsdl/wsfev1.wsdl";      			# The WSDL corresponding to WSAA
    // __DIR__ . "/wsdl/wsfev1.wsdl";
    // const WSDL_PRODUCCION = "/wsdl/produccion/wsfev1.wsdl";
    const URL_WSDL = "https://servicios1.afip.gov.ar/wsfe/service.asmx";
    // const URL_WSDL = "https://wsaahomo.afip.gov.ar/wsfe/service.asmx";
    // protected WSDL_HOMOLOGACION = "/wsdl/homologacion/wsfev1.wsdl";
    // protected URL_HOMOLOGACION = "https://wswhomo.afip.gov.ar/wsfev1/service.asmx";

    //************* CONSTANTES ***************************** 

    const MSG_AFIP_CONNECTION = "No pudimos comunicarnos con AFIP: ";
    const MSG_BAD_RESPONSE = "Respuesta mal formada";
    const MSG_ERROR_RESPONSE = "Respuesta con errores";
    const TA = "/token/TA.xml"; # Ticket de Acceso, from WSAA  
    const PROXY_HOST = ""; # Proxy IP, to reach the Internet
    const PROXY_PORT = ""; # Proxy TCP port 

    //************* VARIABLES *****************************
    protected $log_xmls = TRUE; # Logs de las llamadas
    protected $modo = 0; # Homologacion "0" o produccion "1"
    protected $cuit = 0; # CUIT del emisor de las FC/NC/ND
    protected $client = NULL;
    protected $token = NULL;
    protected $sign = NULL;

    protected $base_dir = "";
    protected $wsdl = "";
    protected $url = "";
    protected $serviceName = "";

    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuFacturacion);
        $this->serviceName = "wsfe";
        $this->cuit = (float) $this->config['cuit_lebron_sas'];

        $parent_dir = realpath(realpath(dirname(__DIR__)));
        chdir($parent_dir);

        $parent_dir = realpath(realpath(dirname($parent_dir)));
        chdir($parent_dir);

        // $this->base_dir = $parent_dir . "/conf/30717223469/homologacion";
        $this->base_dir = $parent_dir . "/conf/30717223469/produccion";

        $this->wsdl = $this->base_dir . "/wsdl/wsfev1.wsdl"; 
        // $this->url = "https://wswhomo.afip.gov.ar/wsfev1/service.asmx";
        $this->url = "https://servicios1.afip.gov.ar/wsfev1/service.asmx";
        
        $this->initializeSoapClient(SOAP_1_2);
    }


    /**
     * Crea el cliente de conexión para el protocolo SOAP.
     *
     * @author: NeoComplexx Group S.A.
     */
    protected function initializeSoapClient($soap_version) {
        try {

            ini_set("soap.wsdl_cache_enabled", 0);
            ini_set('soap.wsdl_cache_ttl', 0);

            $context = stream_context_create(array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            ));

            $this->client = new soapClient( $this->wsdl, array('soap_version' => $soap_version,
                'location' => $this->url,
                #'proxy_host' => PROXY_HOST,
                #'proxy_port' => PROXY_PORT,
                #'verifypeer' => false,
                #'verifyhost' => false,
                'exceptions' => 1,
                'encoding' => 'ISO-8859-1',
                'features' => SOAP_USE_XSI_ARRAY_TYPE + SOAP_SINGLE_ELEMENT_ARRAYS,
                'trace' => 1,
                'stream_context' => $context
            )); # needed by getLastRequestHeaders and others

            // $functions = $this->client->__getFunctions();

            if ($this->log_xmls) {
                file_put_contents($this->base_dir . "/" . $this->cuit . "/" . $this->serviceName ."/tmp/functions.txt", print_r($this->client->__getFunctions(), TRUE));
                file_put_contents($this->base_dir . "/" . $this->cuit . "/" . $this->serviceName ."/tmp/types.txt", print_r($this->client->__getTypes(), TRUE));
            }
        } catch (Exception $exc) {
            throw new Exception("Error: " . $exc);
        }
    }

    /**
     * Verifica la existencia de un archivo y lanza una excepción si este no existe.
     * @param String $filePath
     * @throws Exception
     */
    protected function validateFileExists($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("No pudo abrirse el archivo $filePath");
        }
    }

    public function index()
    {

        $this->setPageTitle("Facturacion electronica");

        $columns[] = "#";

        $columns[] = "Fecha venta";

        $columns[] = "Tipo pago";

        $columns[] = "Nro Doc Cliente";

        $columns[] = "CAE";

        $columns[] = "Cpte";

        $columns[] = "Tipo Cpte";

        $columns[] = "Pto venta";

        $columns[] = "Importe";

        $columns[] = "IVA";

        $columns[] = "Estado";

        $columns[] = "Accion";
        
        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();
        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("facturacion-index");

    }

    // ****************************************  ************************************************
    public function getRows()
    {
        $id_venta = $_POST['search_box'];    // Nro remito

        #--

        if($id_venta)
        {
            $query = Venta::whereRaw(
                "(id_venta = ?)", [ $id_venta ]
            );

            $query = $query->orderBy("venta.id_venta", "DESC")->whereRaw("venta.visible","1");
        }
        else
        {
            $query = Venta::orderBy("venta.id_venta", "DESC")->whereRaw("venta.visible","1");
        }

        $ventas_facturacion = $query->leftjoin("facturacion", "venta.id_venta", '=', 'facturacion.id_venta_facturacion')->whereRaw("venta.tipo != 'venta_presupuesto' AND venta.tipo != 'venta_presupuesto mayorista'");
        $ventas_facturacion = $query->leftjoin("movimiento", "venta.id_venta", '=', 'movimiento.id_operacion')->whereRaw("movimiento.id_cuenta = '13' OR movimiento.id_cuenta = '17' OR movimiento.id_cuenta = '36' OR movimiento.id_cuenta = '36'");
        $ventas_facturacion = $query->leftjoin("concepto", "movimiento.id_cuenta", '=', 'concepto.id_concepto');

        $ventas_facturacion = $query->orderBy("venta.id_venta", "DESC");

        $count = $ventas_facturacion->count();
        $result = $ventas_facturacion->paginate($this->x_page);
        $data = null;

        foreach ($result as $index => $ventas_facturacion)
        {                                  

                $data .= "<tr id='" . ($id = $ventas_facturacion->id_venta) . "'>";

                    $data .= "<td>{$ventas_facturacion->id_venta}</td>";

                    $data .= "<td>{$ventas_facturacion->fecha_hora}</td>";                    

                    $data .= "<td>{$ventas_facturacion->concepto}</td>";

                    $data .= "<td>{$ventas_facturacion->nro_doc_cliente}</td>";
                    
                    $data .= "<td>{$ventas_facturacion->cae}</td>";

                    $data .= "<td>{$ventas_facturacion->nro_comprobante}</td>";

                    $data .= "<td>{$ventas_facturacion->tipo_comprobante}</td>";

                    $data .= "<td>{$ventas_facturacion->punto_de_venta}</td>";

                    if($ventas_facturacion->importe_total <= 0){
                        $data .= "<td>$ 0</td>";
                    }else{
                        $data .= "<td>$ {$ventas_facturacion->importe_total}</td>";
                    }

                    $data .= "<td>{$ventas_facturacion->iva}</td>";

                    if($ventas_facturacion->estado == 'A'){
                        $data .= "<td>Aprobada</td>";
                    }else{
                        $data .= "<td>Pendiente</td>";
                    }

                    if($ventas_facturacion->estado == 'A'){
                        $data .= "<td><a href='!" . self::class . "/crear_factura_pdf?n={$ventas_facturacion}' target='_blank'><i class='fa fa-file-pdf'></i></a></td>";
                    }else{
                        $data .= "<td><a href='javascript:void(0)' onclick='get_modal_facturacion_electronica($ventas_facturacion)'><i class='fa fa-plus-square'></i></a></td>";
                    }

                $data .= "</tr>";
        }

        $data .= "<tr class='not' data-count='{$count}'><td colspan='12'>{$this->replaceLinks($result->links())}</td></tr>";


         #--
         if ( self::isXhrRequest() )
         {
 
             die($data);
 
         }
 
         return $data;
       

    }

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------

    public function crear_factura_pdf()
    {

        $data_json = $_GET['n'];

        // Decodificar el JSON
        $data = json_decode($data_json);

        // $id_venta = $data->id_venta;

        $id_cliente = $data->id_cliente;

        $cliente = Persona::find($id_cliente);

        $domicilio_comercial = "Belgrano 354. Lules (4128) - Tucuman";
        $cuit = $this->config['cuit_lebron_sas'];
        $ingresos_brutos = $this->config['cuit_lebron_sas'];

        $apellido_cliente = $cliente->apellido || '-';
        $nombre_cliente = $cliente->nombre;
        $direccion_cliente = $cliente->direccion;
        $nro_doc_cliente = $data->nro_doc_cliente;
        $cod_comprobante = $data->tipo_comprobante;
        $nro_comprobante = $data->nro_comprobante;
        $punto_de_venta = $data->punto_de_venta;
        $importe_total = $data->importe_total;
        $fecha_vencimiento_cae = $data->fecha_vencimiento_cae;
        $fecha_vencimiento_cae = date("d/m/Y", strtotime($fecha_vencimiento_cae));
        $cae = $data->cae;
        $src_qr = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=https%3A%2F%2F' . 'www.afip.gob.ar/fe/qr/' . $data->qr . '%2F&choe=UTF-8';    // src
        $fecha_emision = $data->fecha_emision;
        $fecha_emision = date("d/m/Y", strtotime($fecha_emision)) ;

        switch ($cod_comprobante) {
            case '001': // factura A
                $tipo_comprobante = 'A';
                break;
            case '002': // Nota debito A
                $tipo_comprobante = 'A';
                break;
            case '003': // Nota de credito A
                $tipo_comprobante = 'A';
                break;
            case '006': // Factura B
                $tipo_comprobante = 'B';
                break;
            case '007': // Nota de debito B
                $tipo_comprobante = 'B';
                break;
            case '008': // Nota de credito B
                $tipo_comprobante = 'B';
                break;
            default:
                $tipo_comprobante = '-';
                break;
        }

        // Armo el PDF
        $dir = realpath(realpath(dirname($this->base_dir)));
        chdir($dir);

        $html_origen = $dir . '/factura_template.html';
        $html_destino = $dir . './tmp/factura.html';

        copy($html_origen, $html_destino);

        // Definir las variables

        // Obtener el contenido del archivo HTML
        $html_content = file_get_contents($html_destino);

        // ************

        //Aquí se crea el objeto a utilizar
        $options = new Options();

        //Y debes activar esta opción "TRUE"
        $options->set('isRemoteEnabled', TRUE);
        $dompdf = new DOMPDF($options);

        // ************


        // Reemplazar los valores
        $html_content = str_replace('[domicilio_comercial]', $domicilio_comercial, $html_content);
        $html_content = str_replace('[cuit]', $cuit, $html_content);
        $html_content = str_replace('[ingresos_brutos]', $ingresos_brutos, $html_content);
        $html_content = str_replace('[src_qr]', $src_qr, $html_content);
        $html_content = str_replace('[nombre]', $nombre_cliente, $html_content);
        $html_content = str_replace('[apellido]', $apellido_cliente, $html_content);
        $html_content = str_replace('[direccion]', $direccion_cliente, $html_content);
        $html_content = str_replace('[nro_doc_cliente]', $nro_doc_cliente, $html_content);
        $html_content = str_replace('[tipo_comprobante]', $tipo_comprobante, $html_content);
        $html_content = str_replace('[cod_comprobante]', $cod_comprobante, $html_content);
        $html_content = str_replace('[nro_comprobante]', $nro_comprobante, $html_content);
        $html_content = str_replace('[punto_de_venta]', $punto_de_venta, $html_content);
        $html_content = str_replace('[importe_total]', $importe_total, $html_content);
        $html_content = str_replace('[fecha_vencimiento_cae]', $fecha_vencimiento_cae, $html_content);
        $html_content = str_replace('[fecha_emision]', $fecha_emision, $html_content);
        $html_content = str_replace('[cae]', $cae, $html_content);

        // Escribir el contenido en el archivo HTML
        file_put_contents($html_destino, $html_content);

        // Cargar el HTML
        $dompdf->loadHtml($html_content);

        // Definir las opciones de configuración
        $dompdf->setPaper('A4');
        // $dompdf->setFont('Helvetica', '', 12);

        // Crear el PDF
        $dompdf->render();
        
        // Descargar el PDF
        $dompdf->stream($cuit."_".$nro_comprobante.".pdf");
    }

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------
    public function modal_facturacion_electronica()
    {
        ob_start();
        
        // Obtengo datos del remito
        
        $id_venta = $_POST['id_venta'];
        
        $form_title = "Datos Factura - AFIP - Rto nro : " . $id_venta;

        $this->setPageTitle($form_title);

        if ( ($venta = Venta::find($id_venta)) )
        {
            $id_cliente = $venta->id_cliente;

            if($id_cliente)
            {
                $cliente = Persona::find($id_cliente);
                $nro_doc_comprador = $cliente->dni;

                $nombre_completo_doc_comprador = $cliente->apellido . " " . $cliente->nombre;
            }
            else{
                $nro_doc_comprador = '';
            }
            
        }else{
            // error al obtener datos de la venta
            return;
        }

        $linea_ventas = Movimiento::where('id_operacion', '=', $id_venta)->get();
        $importe_total = 0;

        foreach ($linea_ventas as $index => $linea_venta)
        {
            $importe_total += $linea_venta->importe;
        }

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">
                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/realizar_factura_electronica" id="factura-electronica-forms" autocomplete="off" method="post">
                    <div class="form-group">
                        <input type="hidden" name="id_venta" value="<?= $id_venta ?>">
                        <!-- **** -->
                        <div class="col-md-12">
                            <label>Cliente : </label>
                            <input id="nombre_completo_doc_comprador" name="nombre_completo_doc_comprador" value="<?= $nombre_completo_doc_comprador ?>" class="form-control" name="nombre_completo_doc_comprador"></input>
                        </div>
                         <!-- **** -->
                         <div class="col-md-12">
                            <label>Tipo de doc. : </label>
                                <select id="tipo_doc" name="tipo_doc" class="form-control">
                                    <option value="80">CUIT</option>
                                    <option value="86">CUIL</option>
                                    <option value="87">CDI</option>
                                    <option value="89">LE</option>
                                    <option value="90">LC</option>
                                    <option value="96">DNI</option>
                                    <option value="99">Sin identificar/venta global diaria</option>
                                </select>
                        </div>
                        <!-- **** -->
                        <!-- **** -->
                        <div class="col-md-12">
                            <label>Nro. Doc. Comprador : </label>
                            <input id="nro_doc_comprador" name="nro_doc_comprador" value="<?= $nro_doc_comprador ?>" class="form-control" name="nro_doc_comprador" required></input>
                        </div>
                        <!-- **** -->
                        <div class="col-md-12">
                            <label for="pto_venta">Pto. de Venta : </label>
                                <select id="pto_venta" name="pto_venta" class="form-control">
                                    <!-- <option value="1">00001</option>
                                    <option value="2">00002</option>
                                    <option value="3">00003</option>
                                    <option value="4">00004</option>
                                    <option value="5">00005</option> -->
                                    <option value="6">00006</option>
                                </select>
                        </div>
                        <!-- **** -->
                        <div class="col-md-12">
                            <label for="tipo_comprobante">Tipo comprobante : </label>
                                <select id="tipo_comprobante" name="tipo_comprobante" class="form-control">
                                    <option value="001" selected="selected">Factura A</option>
                                    <option value="006">Factura B</option>
                                    <option value="011">Factura C</option>
                                    <option value="081">Tique factura A</option>
                                    <option value="081">Tique factura A</option>
                                    <option value="002">Nota debito A</option>
                                    <option value="003">Nota credito A</option>
                                    <option value="049">Consumidor final</option>
                                </select>
                        </div>
                        <!-- **** -->
                        <div class="col-md-12">
                            <label>Iva : </label>
                                <select id="iva" name="iva" class="form-control">
                                    <option value="3">3 - 0,00%</option>
                                    <option value="4">4 - 10,50%</option>
                                    <option value="5" selected="selected">5 - 21,00%</option>
                                    <option value="6">6 - 27,00%</option>
                                    <option value="8">8 - 5,00%</option>
                                    <option value="9">9 - 2,50%</option>
                                </select>
                        </div>
                        <!-- **** -->
                        <div class="col-md-12">
                            <label>Importe total : </label>
                            <input id="importe_total" name="importe_total" value="<?= $importe_total ?>" class="form-control" required></input>
                        </div>
                        <!-- **** -->
                        <br><br><br><br>
                        <div class="col-md-12 text-right">    
                            <button type="submit" class="btn btn-primary">Enviar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </form>
                <!-- ===== Fin form ===== -->
            </div>
        </div>

        <?php

        $this->setBlockModal(ob_get_clean());

    }

    // ----------------------------------------------------------------
    //  
    // ----------------------------------------------------------------
    public function realizar_factura_electronica()
    {
        $pto_venta = $_POST['pto_venta'];
        $tipo_comprobante = $_POST['tipo_comprobante'];
        $nro_doc_comprador = $_POST['nro_doc_comprador'];
        $importe_total = $_POST['importe_total'];
        $id_venta = $_POST['id_venta'];
        $tipo_doc = $_POST['tipo_doc'];
        $iva = $_POST['iva'];
        
        $cuit = $this->config['cuit_lebron_sas'];

        // Chequea variables
        if(($pto_venta <= 0) || ($pto_venta > 999)){
            $this->modal_info('Atencion','Punto de venta invalido');
        }

        if(($tipo_comprobante > 995)){
            $this->modal_info('Atencion','Tipo de comprobante invalido');
        }

        if( ($nro_doc_comprador <= 0) || ($nro_doc_comprador > 99999999999) || (!is_numeric($nro_doc_comprador)) ){
            $this->modal_info('Atencion','Nro  Comprador invalido');
            return;
        }

        if(($importe_total <= 0)){
            $this->modal_info('Atencion','Importe invalido');
        }

        if(($id_venta <= 0)){
            $this->modal_info('Atencion','Venta invalida');
        }

        if(($tipo_doc <= 0) || ($tipo_doc > 99)){
            $this->modal_info('Atencion','Tipo de documento invalido');
        }

        if( ($cuit <= 0) || ($cuit > 99999999999) || (!is_numeric($cuit))  || (strlen($cuit) != 11)){
            $this->modal_info('Atencion','CUIT invalido');
            return;
        }

        if( ($iva <= 0) || ($iva >= 10)){
            $this->modal_info('Atencion','Iva invalido');
            return;
        }

        switch ($iva) {
            case '3': // 
                $porcentaje_iva = '0';
                break;
            case '4': // 
                $porcentaje_iva = '10.50';
                break;
            case '5': // 
                $porcentaje_iva = '21';
                break;
            case '6': // 
                $porcentaje_iva = '27';
                break;
            case '8': // 
                $porcentaje_iva = '5';
                break;
            case '9': //
                $porcentaje_iva = '2.5';
                break;
            default:
                $porcentaje_iva = '21';
                break;
        }


        // ** Testear servers AFIP **
        if(!$this->get_status_server()){
            $this->modal_info('Atencion','Problemas con server AFIP');
            $this->alta_incidencia($this->admin_user->id_usuario,'problem fact elect','0','1','Problemas con server AFIP','0','0','0');

            exit;
        }

        // ** Obtener ultimo comprobante **
        $nro_ultimo_cbte = $this->get_last_comprobante($pto_venta,$tipo_comprobante);

        // ** Obtener CAE **
        $resp_cae = $this->solicitar_cae($pto_venta,$id_venta,$nro_ultimo_cbte,$importe_total,$nro_doc_comprador,$tipo_comprobante,$tipo_doc,$iva,$porcentaje_iva);


        if(($resp_cae['cae'] != "") ){

            // Obtengo el QR
            $qr = $this->generar_qr($pto_venta,$id_venta,$nro_ultimo_cbte,$importe_total,$cuit,$resp_cae['cae']);
            // Almaceno en la BD
            $facturacion = new FacturacionElectronica;

            $facturacion->id_venta_facturacion = $id_venta;
            $facturacion->cae = $resp_cae['cae'];        
            $facturacion->fecha_vencimiento_cae = $resp_cae['fecha_vencimiento'];

            $facturacion->nro_doc_cliente = $nro_doc_comprador;
            $facturacion->tipo_doc = $tipo_doc;        
            $facturacion->voucher_number = 1;
            $facturacion->iva = $iva;

            $facturacion->tipo_comprobante = $tipo_comprobante;
            $facturacion->nro_comprobante = $nro_ultimo_cbte;        
            $facturacion->punto_de_venta = $pto_venta;

            $facturacion->importe_total = $importe_total;
            $facturacion->qr = $qr;        
            $facturacion->estado = 'A';
            
            $facturacion->save();
            // Genero el PDF

            $url = '/ls-admin/facturacion';

            header('Location: '.$url);

        }else{
            $this->modal_info('Atencion','Problema al obtener CAE');
            $this->alta_incidencia($this->admin_user->id_usuario,'problem fact elect','1',$id_venta,'Problema al obtener CAE 2','0','0','0');
        }

    }

    /**
     * Construye un objeto con los parametros basicos requeridos por todos los metodos del servcio wsfev1.
     */
    private function buildBaseParams() {
        $this->checkToken();
        $params = new stdClass();
        $params->Auth = new stdClass();

        $params->Auth->Token = $this->token;
        $params->Auth->Sign = $this->sign;
        $params->Auth->Cuit = $this->cuit;
        return $params;
    }


    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------

    public function get_last_comprobante($pto_venta,$tipo_comprobante)
    {
        $params = $this->buildBaseParams();
        $params->PtoVta = $pto_venta;
        $params->CbteTipo = $tipo_comprobante;

        try {
            $results = $this->client->FECompUltimoAutorizado($params);
        } catch (Exception $e) {
            $this->alta_incidencia($this->admin_user->id_usuario,'problem get_last_com','1','0',$e->getMessage(),'0','0','0');
            throw new Exception( $e->getMessage());
        }

        $this->logClientActivity('FECompUltimoAutorizado');
        // $this->checkErrors($results, 'FECompUltimoAutorizado');

        return $results->FECompUltimoAutorizadoResult->CbteNro;

    }

     /**
     * Verifica la existencia y validez del token actual y solicita uno nuevo si corresponde.
     *
     * @author: NeoComplexx Group S.A.
     */
    protected function checkToken() {

        $ta = $this->base_dir . "\xml\TA.xml";

        // ¿Genero el token?
        if (!file_exists($ta)) {
            $generateToken = TRUE;
        } else {
            $TA = simplexml_load_file($ta);
            $expirationTime = date('c', strtotime($TA->header->expirationTime));
            $actualTime = date('c', date('U'));
            $generateToken = $actualTime >= $expirationTime;
        }

        if ($generateToken) {
            //renovamos el token
            $wsaa_client = new Wsaa($this->serviceName, $this->modo, $this->cuit, $this->log_xmls);
            $wsaa_client->generateToken();
            //Recargamos con el nuevo token
            $TA = simplexml_load_file($ta);
        }

        $this->token = $TA->credentials->token;
        $this->sign = $TA->credentials->sign;
    }

    /**
     * Si el loggueo de errores esta habilitado graba en archivos xml y txt las solicitudes y respuestas
     *
     * @param: $method - String: Metodo consultado
     * @author: NeoComplexx Group S.A.
     */
    protected function logClientActivity($method) {
        if ($this->log_xmls) {
            file_put_contents($this->base_dir . "/tmp/request-" . $method . ".xml", $this->client->__getLastRequest());
            file_put_contents($this->base_dir . "/tmp/hdr-request-" . $method . ".txt", $this->client->
                __getLastRequestHeaders());
            file_put_contents($this->base_dir . "/tmp/response-" . $method . ".xml", $this->client->__getLastResponse());
            file_put_contents($this->base_dir . "/tmp/hdr-response-" . $method . ".txt", $this->client->
                __getLastResponseHeaders());
        }
    }

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------

    public function generar_qr($pto_venta,$tipo_comp,$nro_cbte,$importe,$nro_doc_receptor,$cod_aut)
    {
        $json_datos = json_encode([
            "ver" => 1,
            "fecha" => date('Ymd'),
            "cuit" => $this->cuit,
            "ptoVta" => $pto_venta,
            "tipoCmp" => $tipo_comp,
            "nroCmp" => $nro_cbte,
            "importe" => $importe,
            "moneda" => 'PES',
            "ctz" => 1,
            "tipoDocRec" => 80,
            "nroDocRec" => $nro_doc_receptor,
            "tipoCodAut" => 'E',
            "codAut" => $cod_aut
          ]);

        // Codifica el JSON en una cadena
        $encoded_json = json_encode($json_datos);

        // Codifica la cadena en base64
        $DATOS_CMP_BASE_64 = base64_encode($encoded_json);

        // "https://www.afip.gob.ar/fe/qr/"
        return $DATOS_CMP_BASE_64;

        // print_r($url_end_point);

    }

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------

    public function modal_info($titulo,$mensaje)
    {
        
        ?>        

            <script>
                var mensaje = <?php echo json_encode($mensaje); ?>;
                var confirmacion = confirm(mensaje);

                if (confirmacion) {
                    console.log('El usuario aceptó el mensaje.');
                } else {
                    console.log('El usuario rechazó el mensaje.');
                }

                ;

                window.location.href = "/ls-admin/facturacion";

            </script>;
        <?php

        // $url = '/ls-admin/facturacion';

        // header('Location: '.$url);        
        

    }

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------

    public function get_status_server()
    {
        // Define el nombre del archivo XML
        $filename = $this->base_dir . "/xml/FEDummy.xml";

        if (file_exists($filename)) {
            $xml = file_get_contents($filename);
        } else {
            exit('Failed to open FEDummy.xml.');
        }

        $client = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ) ));
        $headers = [
          'Content-Type' => 'application/xml'
        ];

        $body = $xml;
        
        $request = new Request('POST', self::URL_WSDL . '/FEDummy', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $resp  = $res->getBody()->getContents();

        $xml = simplexml_load_string($resp);

        $appserver = (string) $xml->appserver[0];
        $dbserver = (string) $xml->dbserver[0];
        $authserver = (string) $xml->authserver[0];

        if(($appserver == 'OK') && ($dbserver == 'OK') && ($authserver == 'OK'))
        {
            return true;
        }else{
            return false;
        }

    }

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------

    public function generar_ta()
    {
        $TRA = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<loginTicketRequest version="1.0">'.
            '</loginTicketRequest>');
            
            $TRA->addChild('header');
            $TRA->header->addChild('uniqueId', date('U'));
            $TRA->header->addChild('generationTime', date('c',date('U')-60));
            $TRA->header->addChild('expirationTime', date('c',date('U')+60));
            $TRA->addChild('service', 'wsfe');

        $TRA->asXML(__DIR__ . '/xml/TRA.xml');

        // Obtengo el CMS
        $cms = $this->sign_TRA();

        // $this->TA = $this->get_token_sign($cms);

        $a = 0;

        return true;


        // $this->TA = $this->xml2Array($TA);

    }

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------
    private function sign_TRA()
	{
        $filename_tra_input = $this->base_dir . "/xml/TRA.xml";
        $filename_tra_output = $this->base_dir . "/xml/TRA.tmp";
        $filename_tra_cert = $this->base_dir . "/keys/cert";
        $filename_tra_key = $this->base_dir . "/keys/key";

		$STATUS = openssl_pkcs7_sign($filename_tra_input ,$filename_tra_output,'file://'. $filename_tra_cert,
			array('file://'. $filename_tra_key, self::PASSPHRASE),
			array(),
			!PKCS7_DETACHED
		);
		
		if (!$STATUS)
            return;
			// throw new Exception("ERROR generating PKCS#7 signature");
		
		$inf = fopen( __DIR__."/xml/TRA.tmp", "r");

        if(!$inf){
            throw new Exception("ERROR opening TRA.tmp");
            return;
        }

		$i = 0;
		$CMS = "";
		while (!feof($inf)) { 
			$buffer = fgets($inf);
			if ( $i++ >= 4 ) $CMS .= $buffer;
		}
		
		fclose($inf);
		unlink(__DIR__."/xml/TRA.tmp");
		
		return $CMS;
	}

    /*
	* Convertir un XML a Array
	*/
	private function xml2array($xml) 
	{    
		$json = json_encode( simplexml_load_string($xml));
		return json_decode($json, TRUE);
	}

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------

    public function solicitar_cae($pto_venta,$id_venta,$nro_ultimo_cbte,$imp_total,$doc_cliente,$tipocbte,$tipo_doc,$iva,$porcentaje_iva)
    {
        // Carga el archivo TA.xml
        // $wsfev1->openTA();

        $regfe['CbteTipo']=$tipocbte;
        $regfe['Concepto']=1;
        $regfe['DocTipo']=$tipo_doc; //80=CUIL
        $regfe['DocNro']= $doc_cliente;  // Doc cliente
        //$regfe['CbteDesde']=$cbte; 	// nro de comprobante desde (para cuando es lote)
        //$regfe['CbteHasta']=$cbte;	// nro de comprobante hasta (para cuando es lote)
        $regfe['CbteFch']=date('Ymd'); 	// fecha emision de factura
        $regfe['ImpNeto']=$imp_total;			// neto gravado
        $regfe['ImpTotConc']=0;			// no gravado
        $regfe['ImpIVA']=$imp_total * (21 / 100);			// IVA liquidado
        $regfe['ImpTrib']=0;			// otros tributos
        $regfe['ImpOpEx']=0;			// operacion exentas    //ImpTotConc + ImpNeto + ImpOpEx + ImpTrib + ImpIVA
        $regfe['ImpTotal']=$imp_total + $regfe['ImpIVA'];			// total de la factura. ImpNeto + ImpTotConc + ImpIVA + ImpTrib + ImpOpEx
        $regfe['FchServDesde']=null;	// solo concepto 2 o 3
        $regfe['FchServHasta']=null;	// solo concepto 2 o 3
        $regfe['FchVtoPago']=null;		// solo concepto 2 o 3
        $regfe['MonId']='PES'; 			// Id de moneda 'PES'
        $regfe['MonCotiz']=1;			// Cotizacion moneda. Solo exportacion

        // Comprobantes asociados (solo notas de crédito y débito):
        //$regfeasoc['Tipo'] = 91; //91; //tipo 91|5			
        //$regfeasoc['PtoVta'] = 1;
        //$regfeasoc['Nro'] = 1;

        // Detalle de otros tributos
        $regfetrib['Id'] = 1; 			
        $regfetrib['Desc'] = 'impuesto';
        $regfetrib['BaseImp'] = 0;
        $regfetrib['Alic'] = 0; 
        $regfetrib['Importe'] = 0;
        
        // Detalle de iva
        $regfeiva['Id']=$iva; 
        $regfeiva['BaseImp']=$imp_total; 
        $regfeiva['Importe']=$imp_total * ($porcentaje_iva / 100);

        $params = array( 
            'Auth' => 
            array( 'Token' => $this->token,
                    'Sign' => $this->sign,
                    'Cuit' => $this->cuit ), 
            'FeCAEReq' => 
            array( 'FeCabReq' => 
                array( 'CantReg' => 1,
                        'PtoVta' => $pto_venta,
                        'CbteTipo' => $regfe['CbteTipo'] ),
            'FeDetReq' => 
            array( 'FECAEDetRequest' => 
                array( 'Concepto' => $regfe['Concepto'],
                        'DocTipo' => $regfe['DocTipo'],
                        'DocNro' => $regfe['DocNro'],
                        'CbteDesde' => $nro_ultimo_cbte + 1,
                        'CbteHasta' => $nro_ultimo_cbte + 1,
                        'CbteFch' => $regfe['CbteFch'],
                        'ImpNeto' => $regfe['ImpNeto'],
                        'ImpTotConc' => $regfe['ImpTotConc'], 
                        'ImpIVA' => $regfe['ImpIVA'],
                        'ImpTrib' => $regfe['ImpTrib'],
                        'ImpOpEx' => $regfe['ImpOpEx'],
                        'ImpTotal' => $regfe['ImpTotal'],   // "El campo  'Importe Total' ImpTotal, debe ser igual  a la  suma de ImpTotConc + ImpNeto + ImpOpEx + ImpTrib + ImpIVA."
                        'FchServDesde' => $regfe['FchServDesde'], //null
                        'FchServHasta' => $regfe['FchServHasta'], //null
                        'FchVtoPago' => $regfe['FchVtoPago'], //null
                        'MonId' => $regfe['MonId'], //PES 
                        'MonCotiz' => $regfe['MonCotiz'], //1 
                        'Tributos' => 
                            array( 'Tributo' => 
                                array ( 'Id' =>  $regfetrib['Id'], 
                                        'Desc' => $regfetrib['Desc'],
                                        'BaseImp' => $regfetrib['BaseImp'], 
                                        'Alic' => $regfetrib['Alic'], 
                                        'Importe' => $regfetrib['Importe'] ),
                                ), 
                        'Iva' => 
                            array ( 'AlicIva' => 
                                array ( 'Id' => $regfeiva['Id'], 
                                        'BaseImp' => $regfeiva['BaseImp'], 
                                        'Importe' => $regfeiva['Importe'] ),
                                ), 
                        ), 
                ), 
            ), 
        );
        

        $results = $this->client->FECAESolicitar($params);

        $FeDetRespMsg = $results->FECAESolicitarResult->FeDetResp->FECAEDetResponse[0]->Observaciones->Obs[0]->Msg;
        $FeDetRespCode = $results->FECAESolicitarResult->FeDetResp->FECAEDetResponse[0]->Observaciones->Obs[0]->Code;

        if($FeDetRespMsg){
            $this->modal_info('Atencion',$FeDetRespMsg);
            $this->alta_incidencia($this->admin_user->id_usuario,'problem fact elect','0',$id_venta,"Code : " . $FeDetRespCode . " - " . $FeDetRespMsg,'0','0','0');
        }

        $resp_cae = $results->FECAESolicitarResult->FeDetResp->FECAEDetResponse[0]->CAE;
        $resp_caefvto = $results->FECAESolicitarResult->FeDetResp->FECAEDetResponse[0]->CAEFchVto;

        if(($results->FECAESolicitarResult->Errors->Err[0]->Msg) ){
            $this->modal_info('Atencion',$results->FECAESolicitarResult->Errors->Err[0]->Msg);
            $this->alta_incidencia($this->admin_user->id_usuario,'problem fact elect',$results->FECAESolicitarResult->Errors->Err[0]->Code,$id_venta,$results->FECAESolicitarResult->Errors->Err[0]->Msg,'0','0','0');  
            return;
        } 
        
        return Array( 'cae' => $resp_cae, 'fecha_vencimiento' => $resp_caefvto );
    }

    /*
	* Conecta con el web service y obtiene el token y sign
	*/
	private function get_token_sign($cms)
	{   

        $filename_wsdl = __DIR__ . "/wsdl/wsaa.wsdl";

        $this->client = new SoapClient( $filename_wsdl , array(
            'soap_version'   => SOAP_1_2,
            'location'       => self::URL,
            'trace'          => 1,
            'exceptions'     => 0
            )
        );


		$results = $this->client->loginCms(array('in0' => $cms));

		// para logueo
		file_put_contents(__DIR__."/xml/request-loginCms.xml", $this->client->__getLastRequest());  // Envio el request con el CMS
		file_put_contents(__DIR__."/xml/response-loginCms.xml", $this->client->__getLastResponse());    // Aqui yace el token y el sign

		if (is_soap_fault($results)) 
			throw new Exception("SOAP Fault: ".$results->faultcode.': '.$results->faultstring);

		return $results->loginCmsReturn;
	}

    // ----------------------------------------------------------------
    //   
    // ----------------------------------------------------------------
    public function alta_incidencia($p_id_usuario,$p_operacion,$valor,$p_id_operacion,$p_detalle,$p_importe,$p_cobrado,$p_estado)
    {

        $incidencia = new Incidencia;        
        
        $incidencia->id_usuario = $p_id_usuario;                
        $incidencia->operacion = $p_operacion;
        $incidencia->valor = $valor;
        $incidencia->id_operacion = $p_id_operacion;

        $incidencia->detalle = $p_detalle;                
        $incidencia->importe = $p_importe;
        $incidencia->cobrado = $p_cobrado;
        $incidencia->estado = $p_estado;
        
        $incidencia->fecha_hora = date("Y-m-d H:i:s");

        try {
            // Page code
            $incidencia->save();
        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}