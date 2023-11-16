<?php

class AdminIva extends AdminMain

{

    

    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuFacturacion);
    }



    public function index()
    {

        $this->setPageTitle("IVA");

        $this->setBotonesIva("boton", "javascript:void(0)");

        $columns[] = "#";

        $columns[] = "Fecha";

        $columns[] = "Tipo";

        $columns[] = "Monto";

        $columns[] = "Descripcion";

        $columns[] = "Comprobante";



        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        $table->setHideSearchBox();

        $table->setHideBuscador();

        $table->setFiltroFecha();
        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("iva-index");

    }


    // **************************************** filtro por fecha ************************************************
    public function getRows()
    {
        $fechaIva = HDate::sqlDate($_GET['fechaIva'] ?: date('d/m/Y'));

        $fechaIva = date("Y-m-d", strtotime($fechaIva));

        #--

        $query = iva::whereRaw(
                "(created_at >= ? AND created_at <= ?)", 
                [
                   $fechaIva ." 00:00:00", 
                   $fechaIva ." 23:59:59"
                ]
        );

        $movimientos_iva = $query->orderBy("id_iva", "DESC");
        $count = $movimientos_iva->count();

        // $gastos_suma = $query->orderBy("id_gasto", "DESC");  

        $result = $movimientos_iva->paginate($this->x_page);

        $suma_iva = 0;

        $data = null;

        foreach ($result as $index => $iva)
        {           
            if($iva->tipo_iva == 'c'){
                $suma_iva += $iva->monto;
            }else{
                $suma_iva -= $iva->monto;
            }
        }

        $data .= "<tr id=''>";

            $data .= "<td></td>";

            $data .= "<td>-</td>";

            $data .= "<td>-</td>";

            $data .= "<td>$ " . Facturacion::numberFormat($suma_iva) . "</td>";

            $data .= "<td> <p>Suma iva para la fecha </p></td>";

            $data .= "<td>-</td>";

        $data .= "</tr>";

        foreach ($result as $iva)
        {                                  

                $data .= "<tr id='" . ($iva->id_gasto) . "'>";

                    $data .= "<td>{$iva->id_iva}</td>";

                    $data .= "<td>{$iva->created_at}</td>";

                    if($iva->tipo_iva == 'c')
                    {
                        $data .= "<td>Compra</td>";
                    }else{
                        $data .= "<td>Venta</td>";
                    }

                    $data .= "<td>$ ". Facturacion::numberFormat($iva->monto) . " </td>";

                    $data .= "<td>{$iva->observaciones}</td>";

                    $data .=  "<td>
                    <a href='" . $_SERVER["HTTP_ORIGIN"] . "/media/uploads/comprobantes/iva/" . $iva->factura. "' target='_blank'><i class='fa fa-file-pdf'></i></a>
                    </td>";

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
    // Controla el modal de nuevo gasto
    // ----------------------------------------------------------------
    public function modalFormIvaCompra()
    {

        $form_title = "Nuevo Iva Compra";

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarIvaCompra" id="nuevo-iva-compra-form" autocomplete="off" method="post" enctype="multipart/form-data">
               
                    <div class="input-group-addon">

                    <div class="col-md-4 form-group">

                        <label for="codigo">Monto <i class="required"></i></label>

                        <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" type="text" maxlength="20" name="monto" class="form-control" id="monto" value="" required>
                        
                    </div>

                        <div class="col-md-4 form-group">
                            <label for="codigo">Comprobante (*.PDF) <i class="required"></i></label>
                            <input class="form-control form-file" type="file" id="comprobante" name="comprobante" size="22">
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="observaciones">Observaciones</label>
                            <textarea id="observaciones" name="observaciones" class="form-control" name="observaciones"></textarea>
                        </div>

                        <div class="col-md-12 text-right">    
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <?php

        $this->setBlockModal(ob_get_clean());

    }

    // ----------------------------------------------------------------
    // Controla el modal de nuevo gasto
    // ----------------------------------------------------------------
    public function modalFormIvaventa()
    {

        $form_title = "Nuevo Iva Venta";

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarIvaVenta" id="nuevo-iva-compra-form" autocomplete="off" method="post" enctype="multipart/form-data">
               
                    <div class="input-group-addon">

                    <div class="col-md-4 form-group">

                        <label for="codigo">Monto <i class="required"></i></label>

                        <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" type="text" maxlength="20" name="monto" class="form-control" id="monto" value="" required>
                        
                    </div>

                        <div class="col-md-4 form-group">
                            <label for="codigo">Comprobante (*.PDF) <i class="required"></i></label>
                            <input class="form-control form-file" type="file" id="comprobante" name="comprobante" size="22">
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="observaciones">Observaciones</label>
                            <textarea id="observaciones" name="observaciones" class="form-control" name="observaciones"></textarea>
                        </div>

                        <div class="col-md-12 text-right">    
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <?php

        $this->setBlockModal(ob_get_clean());

    }
    // ----------------------------------------------------------------
    // Fin * Controla el modal de nuevo gasto
    // ----------------------------------------------------------------

    public function guardarIvaCompra()
    {	
	
        $iva = new iva();
        
        $iva->tipo_iva =  'c';
	
        $iva->monto =  floatval($_POST['monto']);

        $iva->estado_iva = 'A';

        $iva->observaciones = $_POST['observaciones'];

        $iva->created_at = date("Y-m-d H:i:s");
        
        if($_FILES["comprobante"]["type"])
        {
            $file_type = $_FILES["comprobante"]["type"];

            if (($file_type != "image/png") && ($file_type != "image/jpeg") && ($file_type != "image/jpg") && ($file_type != "application/pdf") && ($file_type != "image/webp")) {
                HArray::jsonError("Comprobante invalido");
            }else{
                $extension = substr($file_type, strpos($file_type, "/") + 1);

                $targetfolder = $_SERVER["DOCUMENT_ROOT"] . "/media/uploads/comprobantes/iva/";
    
                $namedFile = rand() . "." . $extension;
    
                $targetfolder = $targetfolder . $namedFile;
    
                $moved = move_uploaded_file($_FILES['comprobante']['tmp_name'], $targetfolder);
    
                $sizeFile = $_FILES['comprobante']['size'];

                if ((!$moved) || ($sizeFile > 500000)) {
                    HArray::jsonError("Comprobante invalido");
                }else{
                    $iva->factura = $namedFile;
                }
            }
        }

        $iva->save();

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'IVA guardado con exito';

        // HArray::jsonResponse($json,null,false);

        $url = '/ls-admin/iva';

        header('Location: '.$url);



    }

    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------

    public function guardarIvaVenta()
    {

        $iva = new Iva;
        
        $iva->tipo_iva =  'v';

        $iva->monto =  floatval($_POST['monto']);

        $iva->estado_iva = 'A';

        $iva->observaciones = $_POST['observaciones'];

        $iva->created_at = date("Y-m-d H:i:s");
        
        if($_FILES["comprobante"]["type"])
        {
            $file_type = $_FILES["comprobante"]["type"];

            if (($file_type != "image/png") && ($file_type != "image/jpeg") && ($file_type != "image/jpg") && ($file_type != "application/pdf") && ($file_type != "image/webp")) {
                HArray::jsonError("Comprobante invalido");
            }else{
                $extension = substr($file_type, strpos($file_type, "/") + 1);

                $targetfolder = $_SERVER["DOCUMENT_ROOT"] . "/media/uploads/comprobantes/iva/";
    
                $namedFile = rand() . "." . $extension;
    
                $targetfolder = $targetfolder . $namedFile;
    
                $moved = move_uploaded_file($_FILES['comprobante']['tmp_name'], $targetfolder);
    
                $sizeFile = $_FILES['comprobante']['size'];

                if ((!$moved) || ($sizeFile > 500000)) {
                    HArray::jsonError("Comprobante invalido");
                }else{
                    $iva->factura = $namedFile;
                }
            }
        }

        $iva->save();

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'IVA guardado con exito';

        // HArray::jsonResponse($json,null,false);

        $url = '/ls-admin/iva';

        header('Location: '.$url);



    }

}