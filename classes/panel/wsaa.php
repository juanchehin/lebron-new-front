<?php

/**
 * Clase para autenticarse contra AFIP 
 * Hace uso del web-service WSAA que permite obtener el token de conexión
 * 
 * Se encuentra basado en el ejemplo de aplicaciones clientes del WSAA publicado en la web de AFIP
 * http://www.afip.gob.ar/ws/paso4.asp?noalert=1
 *
 * @author NeoComplexx Group S.A.
 */
class Wsaa {

    //************* CONSTANTES *****************************
    const MODO_HOMOLOGACION = 0;
    const MODO_PRODUCCION = 1;
    const WSDL_HOMOLOGACION = "/wsdl/homologacion/wsaa.wsdl"; # WSDL del web service WSAA
    const URL_HOMOLOGACION = "https://wsaahomo.afip.gov.ar/ws/services/LoginCms";
    const CERT_HOMOLOGACION = "/key/homologacion/certificado.pem"; # Certificado X.509 otorgado por AFIP
    const PRIVATEKEY_HOMOLOGACION = "/key/homologacion/privada"; # Clave privada de la PC
    const WSDL_PRODUCCION = "/wsdl/produccion/wsaa.wsdl";
    const URL_PRODUCCION = "https://wsaa.afip.gov.ar/ws/services/LoginCms"; 
    const CERT_PRODUCCION = "/key/produccion/certificado.pem";
    const PRIVATEKEY_PRODUCCION = "/key/produccion/privada";

    //************* VARIABLES *****************************
    private $base_dir = __DIR__;
    private $service = "";
    private $modo = 0;
    private $log_xmls = TRUE; 
    private $cuit = 0;
    private $wsdl = "";
    private $url = "";
    private $cert = "";
    private $privatekey = "";

    public function __construct($service, $modo_afip, $cuit, $logs) {
        $this->log_xmls = $logs;
        $this->modo = $modo_afip;
        $this->cuit = $cuit;
        $this->service = $service;
        ini_set("soap.wsdl_cache_enabled", 0);
        ini_set('soap.wsdl_cache_ttl', 0);

        $parent_dir = realpath(realpath(dirname(__DIR__)));
        chdir($parent_dir);

        $parent_dir = realpath(realpath(dirname($parent_dir)));
        chdir($parent_dir);

        $this->base_dir = $parent_dir . "/conf/30717223469/produccion";
        // $this->base_dir = $parent_dir . "/conf/30717223469/homologacion";

        // $this->url = "https://wsaahomo.afip.gov.ar/ws/services/LoginCms";
        $this->url = "https://wsaa.afip.gov.ar/ws/services/LoginCms";
        
        $filename_tra_cert = $this->base_dir . "/keys/cert";
        $filename_tra_key = $this->base_dir . "/keys/key";

        $this->cert = $filename_tra_cert;
        $this->privatekey = $filename_tra_key;
        $this->wsdl = $this->base_dir . "/wsdl/wsfev1.wsdl"; 
        
        // if ($this->modo === Wsaa::MODO_PRODUCCION) {
        //     $this->wsdl = Wsaa::WSDL_PRODUCCION; 
        //     $this->url = Wsaa::URL_PRODUCCION; 
        
        //     $this->cert = "file://" . $this->base_dir . Wsaa::CERT_PRODUCCION;
        //     $this->privatekey = "file://" . $this->base_dir . Wsaa::PRIVATEKEY_PRODUCCION;
        // } else {
        //     $this->wsdl = Wsaa::WSDL_HOMOLOGACION; 
        //     $this->url = Wsaa::URL_HOMOLOGACION; 
        //     $this->cert = "file://" . $this->base_dir . Wsaa::CERT_HOMOLOGACION;
        //     $this->privatekey = "file://" . $this->base_dir . Wsaa::PRIVATEKEY_HOMOLOGACION;
        // }
    }

    /**
     * Genera un nuevo token de conexión y lo guarda en el archivo ./:CUIT/:WebSerivce/token/TA.xml
     *
     * @author: NeoComplexx Group S.A.
     */
    public function generateToken() {
        $this->validateFileExists($this->cert);
        $this->validateFileExists($this->privatekey);
        $this->validateFileExists($this->wsdl);

        // $filename_ta = __DIR__ . "/xml/TA.xml";
        $filename_ta = $this->base_dir . "\xml\TA.xml";


        $this->createTRA();
        $cms = $this->signTRA();
        $loginResult = $this->callWSAA($cms);
        file_put_contents($filename_ta, $loginResult);
    }

    /**
     * Crea el archivo ./:CUIT/:WebSerivce/token/TRA.xml
     * El archivo es necesario para realizar la firma
     * 
     * @author: NeoComplexx Group S.A.
     */
    private function createTRA() {
        try {
            $filename_tra = $this->base_dir . "/xml/TRA.xml";

            $TRA = new SimpleXMLElement(
                    '<?xml version="1.0" encoding="UTF-8"?>' .
                    '<loginTicketRequest version="1.0">' .
                    '</loginTicketRequest>');
            $TRA->addChild('header');
            $TRA->header->addChild('uniqueId', date('U'));
            $TRA->header->addChild('generationTime', date('c', date('U') - 60));
            $TRA->header->addChild('expirationTime', date('c', date('U') + 60));
            $TRA->addChild('service', $this->service);
            $TRA->asXML($filename_tra);
        } catch (Exception $exc) {
            throw new Exception("Error al crear TRA.xml: " . $exc->getTraceAsString());
        }
    }

    /**
     * Esta funcion realiza la firma PKCS#7 usando como entrada el archivo TRA.xml, el certificado y la clave privada
     * Genera un archivo intermedio ./:CUIT/:WebSerivce/TRA.tmp y finalmente obtiene del encabezado solo lo que se necesita para WSAA
     * 
     * @author: NeoComplexx Group S.A.
     */
    private function signTRA() {
        $infilename = $this->base_dir . '/xml/TRA.xml';
        $outfilename = $this->base_dir . '/tmp/TRA.tmp';
        $filename_tra_cert = $this->base_dir . "/keys/cert";
        $filename_tra_key = $this->base_dir . "/keys/key";

        $headers = array();
        $flags = !PKCS7_DETACHED;

        $status = openssl_pkcs7_sign($infilename ,$outfilename,'file://'. $filename_tra_cert,
			array('file://'. $filename_tra_key, ""),
			array(),
			!PKCS7_DETACHED
		);

        // $status = openssl_pkcs7_sign($infilename, $outfilename, $this->cert, $this->privatekey, $headers, $flags);
        if (!$status) {
            $error = openssl_error_string();
            $this->alta_incidencia('0','problem fact elect','0','1','ERROR al generar la firma PKCS#7 - wsaa.php','0','0','0');

            throw new Exception("ERROR al generar la firma PKCS#7");
        }

        // Cargo el TRA.tmp
        $inf = fopen($outfilename, "r");
        $i = 0;
        $cms = "";
        
        while (!feof($inf)) {
            $buffer = fgets($inf);
            if ($i++ >= 4) {
                $cms.=$buffer;
            }
        }
        fclose($inf);
        #unlink("token/TRA.xml");
        unlink($outfilename);

        return $cms;
    }
    
    /**
     * Esta funcion se conecta al webservice SOAP de AFIP para autenticarse
     * El resultado es la información del token generado
     * 
     * @author: NeoComplexx Group S.A.
     */
    private function callWSAA($cms) {
        $context = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        ));

        $filename_wsdl = $this->base_dir . "/wsdl/wsaa.wsdl";
        // $url = "https://wsaahomo.afip.gov.ar/ws/services/LoginCms"; // homologacion (testing)


        $client = new SoapClient($filename_wsdl, array(
            'soap_version' => SOAP_1_2,
            'location' => $this->url,
            'trace' => 1,
            'exceptions' => 0,
            'stream_context' => $context
        ));

        // Envio a los servers de AFIP el loginCMS
        $results = $client->loginCms(array('in0' => $cms));

        if ($this->log_xmls) {
            // para logueo

            // file_put_contents(__DIR__."/xml/request-loginCms.xml", $this->client->__getLastRequest());  // Envio el request con el CMS
            // file_put_contents(__DIR__."/xml/response-loginCms.xml", $this->client->__getLastResponse());    // Aqui yace el token y el sign

            file_put_contents(__DIR__."/xml/request-loginCms.xml", $client->__getLastRequest());
            file_put_contents(__DIR__."/xml/response-loginCms.xml", $client->__getLastResponse());
        }

        if (is_soap_fault($results)) {
            $this->alta_incidencia('0','problem callWSAA','0','1',$results->faultcode . ' - ' . $results->faultstring,'0','0','0');

            throw new Exception("Error SOAP: " . $results->faultcode . " - " . $results->faultstring);
        }

        return $results->loginCmsReturn;
    }

    /**
     * Verifica la existencia de un archivo y lanza una excepción si este no existe.
     * @param String $filePath
     * @throws Exception
     */
    private function validateFileExists($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("No pudo abrirse el archivo $filePath");
        }
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
