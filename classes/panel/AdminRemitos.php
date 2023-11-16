<?php

class AdminRemitos extends AdminMain
{
    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuRemitos);
    }
    public function index()
    {

        $this->setPageTitle("Remitos");
    
        $columns[] = "#";

        $columns[] = "Fecha";

        $columns[] = "Cliente";

        $columns[] = "Bultos";

        $columns[] = "&nbsp;";

        $columns[] = "&nbsp;";

        $columns[] = "&nbsp;";
        
        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();
        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("remitos-index");

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

            $remitos = $query->orderBy("id_venta", "DESC")->whereRaw("visible","1");
        }
        else
        {
            $remitos = Venta::orderBy("id_venta", "DESC")->whereRaw("visible","1");
        }
                

        $count = $remitos->count();

        $result = $remitos->paginate($this->x_page);

        $data = null;

        foreach ($result as $index => $ventas)
        {                                  

                $data .= "<tr id='" . ($id = $ventas->id_venta) . "'>";

                    $data .= "<td>{$ventas->id_venta}</td>";

                    $data .= "<td>{$ventas->fecha_hora}</td>";

                    $data .= "<td>{$ventas->cliente}</td>";

                    $data .= "<td>{$ventas->cantidad_bultos}</td>";

                    $data .= "<td><a href='!AdminVenta/modalForm?n={$ventas->id_venta}' target='_blank'><i class='fa fa-file-pdf'></i></a></td>";

                    $data .= "<td><a href='javascript:void(0)' onclick='get_modal_form_bulto($ventas->id_venta)'><i class='fa fa-truck'></i></a></td>";

                    if(!($ventas->comprobante))
                    {
                        $data .= "<td><a href='javascript:void(0)' rel='1' onclick='get_form_comprobante(this)'><i class='fa fa-pencil-alt'></i></a>&nbsp;</td>";

                    }
                    else{
                        $data .=  "<td><a href='" . $_SERVER["HTTP_ORIGIN"] . "/media/uploads/comprobantes/" . "{$ventas->comprobante}' target='_blank'><i class='fa fa-file'></i></a></td>";
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
    // Fin * Controla el modal de nuevo bulto
    // ----------------------------------------------------------------
    public function guardarBulto()
    {

        $nro_remito = intval($_POST['nro_remito']);

        $remito = Venta::findOrNew($nro_remito);

        $cant_bultos =  floatval($_POST['cantidad_bultos']);

        $remito->cantidad_bultos = $cant_bultos;

        $remito->save();

    }


    // ----------------------------------------------------------------
    // Controla el modal de edicion de comprobante /remitos
    // ----------------------------------------------------------------
    public function updateComprobanteForm()
    {

        $form_title = "Edicion de comprobante";

        $permiso = Permiso::permisoCrear;

        $id_venta = floatval($_POST['id_venta']);

        if ( $venta = Venta::find($id_venta) )

        {

            $permiso = Permiso::permisoEditar;

            $form_title = "Editar Comprovante - #{$id_venta} ";


        }

        $this->setPageTitle($form_title);

        // $selectTipoVenta = $this->_selectTipoVenta();

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/updateComprobante?id_venta=<?= $id_venta ?>" id="editar-comprobante-form" autocomplete="off" method="post">
               
                    <div class="input-group-addon">

                        <div class="col-md-6 form-group">

                            <label for="id_tipo_venta">Adjuntar comprobante <i class="required"></i></label>

                                <input class="form-control form-file" type="file" id="comprobante" name="comprobante" size="22">

                        </div>

                        <div class="col-md-12 text-right">
    
                            <button type="submit" class="btn btn-primary">Guardar</button>

                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <script type="text/javascript">

            // se dispara al hacer clic en 'Guardar'
            (editarVentaForm = document.getElementById('editar-comprobante-form')).onsubmit = function (e) {

                e.preventDefault();

                theForm = this;

                submit_form(theForm, function (rsp) {

                    theForm.reset();
                    
                    if ( typeof get_rows === "function" )
                    {
                        get_rows();
                    }
                    delete rsp["notice"];

                    theForm.setAttribute("rel", JSON.stringify(rsp));

                });

                document.querySelector('[data-dismiss="modal"]').click();

            };

        </script>

        <?php

        $this->setBlockModal(ob_get_clean());

    }

    // ===================
    public function updateComprobante()
    {

            $id_venta = floatval($_GET['id_venta']);

            $venta = Venta::findOrNew($id_venta);
        
            $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

            // ** Comprobante **

            if($_FILES["comprobante"]["type"])
            {

                $file_type = $_FILES["comprobante"]["type"];

                if (($file_type != "image/png") && ($file_type != "image/jpeg") && ($file_type != "image/jpg") && ($file_type != "application/pdf") && ($file_type != "image/webp")) {
                    HArray::jsonError("Comprobante invalido");
                }else{
                    $extension = substr($file_type, strpos($file_type, "/") + 1);

                    $targetfolder = $_SERVER["DOCUMENT_ROOT"] . "/media/uploads/comprobantes/";
        
                    $namedFile = rand() . "." . $extension;
        
                    $targetfolder = $targetfolder . $namedFile;
        
                    $moved = move_uploaded_file($_FILES['comprobante']['tmp_name'], $targetfolder);
        
                    $sizeFile = $_FILES['comprobante']['size'];

                    if ((!$moved) || ($sizeFile > 500000)) {
                        HArray::jsonError("Comprobante invalido");
                    }else{
                        $venta->comprobante = $namedFile;
                    }
            }

            $venta->save();

            HArray::jsonResponse($json);
        }
    }
}