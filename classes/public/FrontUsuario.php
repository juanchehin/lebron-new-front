<?php

class FrontUsuario extends FrontCart

{

    public function __construct()

    {

        parent::__construct();

        if ( !isset($_POST['fb_token']) && (!$this->logged_user && !$this->getSessionCart()) )

        {

            Router::redirect(self::siteUrl);

        }

    }



    public function auth()

    {

        $this->setPageTitle("Registrarse");

        $this->setBody("auth-form");

    }



    public static function customerAddrs($userId)

    {

        $addrs = array();

        if ( $userId )

        {

            $result = Direccion::where('owner', $userId)->orderBy("id", "DESC")->get();

            foreach ($result as $addr)

            {

                $arr_body = $addr->array_body;

                $arr_body['cp'] = $addr->valor;

                $arr_body['localidad'] = $addr->nombre;

                $arr_body['sucursal'] = $addr->estatico;

                $addrs[$addr->id] = $arr_body;

            }

        }

        return $addrs;

    }



    public function consultaLocalidadCp()

    {
        $cp = intval($_GET['cp']);

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "lebronsu_admin";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 


          $sql = "SELECT provincia FROM provincias2";
          $result = $conn->query($sql);
  
          if ($result->num_rows > 0) {
              // output data of each row
              $res1 = array();
  
              while($row = $result->fetch_assoc()) {
              //   echo "nombre: " . $row["nombre"]. "<br>";
                $res1[] = $row["provincia"];
              }
            } else {
              echo "0 results";
            }
            // print_r($res1);

          $conn->close();

          $result = array_values($res1);

          $res = array();
  
           foreach ($result as $item)
           {
               $res[$item] = [];
           }

        HArray::jsonResponse($res);

    }


    // ===================
    // Lista las localidades
    // ======================
    public function consultaLocalidadCpLocalidades()

    {
        $cp = intval($_GET['cp']);

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "lebronsu_admin";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 

        $sql = "SELECT nombre FROM provincias_localidades where cp = $cp";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            $res1 = array();

            while($row = $result->fetch_assoc()) {
            //   echo "nombre: " . $row["nombre"]. "<br>";
              $res1[] = $row["nombre"];
            }
          } else {
            echo "0 results";
          }

          $conn->close();

          $result = array_values($res1);

          $res = array();
  
           foreach ($result as $item)
           {
               $res[$item] = [];
           }

        //    dd($res);
        HArray::jsonResponse($res);

    }

    public function continuar()
    {

        $conEnvio = ($_GET['shp']);

        $masDatos = isset($_POST['userId']);

        $userId = floatval($_POST['userId']);

        $correo = trim($_POST['email']);

        $dni = floatval($_POST['dni']);

        $apellido = trim($_POST['apellido'] ?: $_POST['last_name']);

        $nombre = trim($_POST['nombre'] ?: $_POST['first_name']);

        #--

        if ( !($user_id = $this->logged_user->id) )

        {

            $search = Cliente::whereRaw("!`borrado` AND (`email`='{$correo}' OR `dni`='{$dni}')")->first();

            if ( !$masDatos )

            {

                if ( !filter_var($correo, FILTER_VALIDATE_EMAIL) )

                {

                    HArray::jsonError("Ingresar un correo electrónico válido", "email");

                }

                #--

                if ( $dni < 1000000 )

                {

                    HArray::jsonError("Ingresar Número de DNI correcto", "dni");

                }

                #--

                if ( $search )

                {

                    if ( $search->dni && ($search->dni != $dni) )

                    {

                        HArray::jsonError("Correo electrónico ya existe", "email");

                    }

                    #--

                    if ( $search->email && ($search->email != $correo) )

                    {

                        HArray::jsonError("DNI ya se encuentra registrado con otro e-mail", "dni");

                    }

                    $res['userId'] = $search->id;

                }

            }

            else

            {

                if ( strlen($nombre) < 3 )

                {

                    HArray::jsonError("Ingresa tu Nombre", "nombre");

                }

                #--

                if ( strlen($apellido) < 3 )

                {

                    HArray::jsonError("Ingresa tu Apellido", "apellido");

                }

                #--

                if ( !$search )

                {

                    $search = new Cliente();

                }

                $search->nombre = mb_strtolower($nombre);

                $search->apellido = mb_strtolower($apellido);

                if ( $dni )

                {

                    $search->dni = $dni;

                }

                #--

                if ( $correo )

                {

                    $search->email = mb_strtolower($correo);

                }

                $search->save();

                $userId = $search->id;

            }

            #--

            $res['ok'] = 1;

            $res['correo'] = $correo;

            $res['dni'] = $dni;

            $res['nombre'] = ucfirst($search->nombre);

            $res['apellido'] = ucfirst($search->apellido);

            #--

            //if ( $res['logged'] = $search->id ) // continuar sin mostrar form de datos extras

            if ( $res['logged'] = $userId )

            {

                $res['direcciones'] = static::customerAddrs($userId);

                if ( !$conEnvio )

                {

                    unset($res['ok']);

                }

                $this->setLoginSession($search);

            }

            HArray::jsonResponse($res);

        }

    }



    public function setAddress()

    {

        $addrId = floatval($_POST['addrId']);

        $cp = floatval($_POST['cp']);

        $addr = (array)$_POST['json'];

        $sin_nro = intval($_POST['snro']);

        $localidad = trim($_POST['localidad']);

        $esSucursal = intval($_POST['es_sucursal']);

        #--

        if ( !$cp )

        {

            HArray::jsonError("Ingresar Código Postal", "cp");

        }

        #--

        if ( !$localidad )

        {

            HArray::jsonError("Indicar la Localidad", "localidad");

        }

        #--

        if ( !$addr['provincia'] )

        {

            HArray::jsonError("Indicar la Provincia", "json[provincia]");

        }

        #--

        if ( !$addr['calle'] )

        {

            HArray::jsonError("Ingresar Nombre de la calle", "json[calle]");

        }

        #--

        if ( !$sin_nro && !intval($addr['numero']) )

        {

            HArray::jsonError("Ingresar número de Inmueble", "json[numero]");

        }

        #--

        if ( !floatval($addr['telefono']) )

        {

            HArray::jsonError("Ingresar número de Telefono", "json[telefono]");

        }

        #--

        $direccion = Direccion::findOrNew($addrId);

        $direccion->owner = $this->logged_user->id;

        $direccion->tipo = "direccion";

        $direccion->nombre = mb_strtoupper($localidad);

        $direccion->valor = $cp;

        $direccion->estatico = $esSucursal;

        $direccion->json_body = $addr;

        $direccion->save();

        #--

        HArray::jsonResponse(['snd' => $direccion->id, 'logged'=> $direccion->owner]);

    }

    public function finalizarCompraMayorista()
    {

        
        $correo = trim($_POST['email']);

        $dni = floatval($_POST['dni']);
        
        $apellidos = trim($_POST['apellidos']);
        
        $nombres = trim($_POST['nombres']);

        $telefono = trim($_POST['telefono']);
                
        #--

        $provincia = trim($_POST['provincia']);
        
        $localidad = trim($_POST['localidad']);

        $direccion = trim($_POST['direccion']);
        
        #--

        $conEnvio = ($_POST['envio']);

        $articulos = (array)HSession::getSession(self::sesCart);

        #--

        $file_type = $_FILES["comprobante"]["type"];
            
        $targetfolder = $_SERVER["DOCUMENT_ROOT"] . "/media/uploads/mayoristas/";

        switch ($file_type) {
            case "application/pdf":
                $ext = '.pdf';
                break;
            case "image/png":
                $ext = '.png';
                break;
            case "image/jpg":
                $ext = '.jpg';
                break;
            case "image/jpeg":
                $ext = '.jpeg';
                break;
            default:
                HArray::jsonError("Ingresar un comprobante válido", "comprobante");
                return;
        }

        $namedFile = rand() . $ext;

        $targetfolder = $targetfolder . $namedFile;

        $moved = move_uploaded_file($_FILES['comprobante']['tmp_name'], $targetfolder);

        $sizeFile = $_FILES['comprobante']['size'];

        if ( (!$moved) || ($sizeFile > 500000)) {
            HArray::jsonError("Ingresar un comprobante válido", "comprobante");
            return;
        }

        # --

        if ( !filter_var($correo, FILTER_VALIDATE_EMAIL) )
        {
            HArray::jsonError("Ingresar un correo electrónico válido", "email");
        }
        #--
        if ( $dni < 1000000 )
        {
            HArray::jsonError("Ingresar Número de DNI correcto", "dni");
        }

        if ( strlen($nombres) < 3 )
        {
            HArray::jsonError("Ingresa tu Nombre", "nombre");
        }
        #--
        if ( strlen($apellidos) < 3 )
        {
            HArray::jsonError("Ingresa tu Apellido", "apellido");
        }

        // *** creo cliente

        $persona = new Persona;

        $persona->apellido =  $apellidos;

        $persona->nombre = $nombres;

        $persona->telefono = $telefono;

        $persona->dni = $dni;

        $persona->email = $correo;

        $persona->rol = 'mayorista';

        $persona->save();

        // *** creo venta mayorista

        $venta_mayorista = new VentasMayorista;

        $venta_mayorista->id_cliente =  $persona->id;

        $venta_mayorista->monto = $nombres;

        if($conEnvio == 'envio')
        {
            $venta_mayorista->tipo_envio = 'E';
            $venta_mayorista->direccion_mayorista = $direccion . ' - ' . $localidad . ' - ' . $provincia;

        }else{
            $venta_mayorista->tipo_envio = 'L';
            $venta_mayorista->direccion_mayorista = $localidad . ' - ' . $provincia;

        }


        $venta_mayorista->comprobante = $namedFile;

        $venta_mayorista->save();

        // *** cargo las linea_venta

        foreach ($articulos as $key => $value)
        {

            $linea_venta_mayorista = new LineaVentaMayoristas();

            $linea_venta_mayorista->id_venta_mayorista =  $venta_mayorista->idventa_mayorista;

            $linea_venta_mayorista->id_producto = $key;

            $search_articulo = Articulo::find($key);

            $linea_venta_mayorista->subtotal = $search_articulo->precio_compra; 

            $linea_venta_mayorista->cantidad = $value; 

            $linea_venta_mayorista->save();
        }

        $lineas = $this->getSessionCart();

        $lineas = array();

        $this->setSessionCart($lineas, true);

        echo '<script type="text/javascript">
            console.log("pasa ");
            window.location.replace("http://lebron/datos-exitosos");
        </script>';

    }

}