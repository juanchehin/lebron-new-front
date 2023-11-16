<?php

class AdminGastos extends AdminMain

{

    

    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuGastos);
    }



    public function index()
    {

        $this->setPageTitle("Gastos varios");

        // $this->setBotonNuevo("Nuevo Gasto");

        #--
        
        // $export = new ExportOpts();

        // $export->setExcelUrl($url = "!AdminProducto/exportar");

        // $export->setPdfUrl($url . "?pdf=1");

        // $this->setBotonNuevo("Agregar gasto", self::sysUrl . "/gastos/nuevoGastoForm", "<span class='pull-right' id='dv-export'></span>");}

        $this->setBotonNuevo("Agregar gasto", "javascript:void(0)");

        $columns[] = "#";

        $columns[] = "Fecha";

        $columns[] = "Usuario";

        $columns[] = "Monto";

        // $columns[] = "Mínimo.text-center";

        $columns[] = "Cantidad";

        $columns[] = "Descripcion";

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

        $this->setBody("gastos-index");

    }


    // **************************************** filtro por fecha ************************************************
    public function getRows()
    {
        $fechaGasto = HDate::sqlDate($_GET['fechaGasto'] ?: date('d/m/Y'));

        $fechaGasto = date("Y-m-d", strtotime($fechaGasto));

        #--

        $query = Gastos::whereRaw(
                "(created_at >= ? AND created_at <= ?)", 
                [
                   $fechaGasto ." 00:00:00", 
                   $fechaGasto ." 23:59:59"
                ]
        );

        $gastos = $query->join("usuario", "gastos.id_usuario", '=', 'usuario.id_usuario');
        $gastos = $query->orderBy("id_gasto", "DESC");
        $count = $gastos->count();

        $gastos_suma = $query->orderBy("id_gasto", "DESC");  

        $result = $gastos->paginate($this->x_page);

        $gasto_fecha = 0;

        $data = null;

        foreach ($result as $index => $gastos)
        {           
            $monto = $gastos->monto;
            $cantidad = $gastos->cantidad;

            $gasto_fecha += $monto * $cantidad;
        }

        $data .= "<tr id=''>";

            $data .= "<td></td>";

            $data .= "<td>{$fechaGasto}</td>";

            $data .= "<td>-</td>";

            $data .= "<td>$ {$gasto_fecha}</td>";

            $data .= "<td>-</td>";

            $data .= "<td> <p>Total gastos para la fecha </p></td>";

        $data .= "</tr>";

        foreach ($result as $index => $gastos)
        {                                  

                $data .= "<tr id='" . ($id = $gastos->id_gasto) . "'>";

                    $data .= "<td>{$gastos->id_gasto}</td>";

                    $data .= "<td>{$gastos->created_at}</td>";

                    $data .= "<td>{$gastos->usuario}</td>";

                    $data .= "<td>{$gastos->monto}</td>";

                    $data .= "<td>{$gastos->cantidad}</td>";

                    $data .= "<td>{$gastos->descripcion}</td>";

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
    public function modalForm()
    {

        $form_title = "Nuevo Gasto";

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarGasto" id="nuevo-gasto-form" autocomplete="off" method="post">
               
                    <div class="input-group-addon">

                    <div class="col-md-4 form-group">

                        <label for="codigo">Monto <i class="required"></i></label>

                        <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" type="text" maxlength="20" name="monto" class="form-control" id="monto" value="" required>
                        
                    </div>

                    <div class="col-md-4 form-group">

                        <label for="codigo">Cantidad <i class="required"></i></label>
                        <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" type="tel" maxlength="3" name="cantidad" class="form-control" id="cantidad" value="" required>

                        </div>
                            <div class="col-md-12 form-group">
                            <label for="descripcion">Concepto</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" name="descripcion"></textarea>
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

            // se dispara al hacer clic en 'Agregar gasto'
            // (nuevoGastoForm = document.getElementById('nuevo-gasto-form')).onsubmit = function (e) {

            //     e.preventDefault();

            //     theForm = this;

                // submit_form(theForm, function (rsp) {
                    
                //     theForm.reset();
                    
                //     if ( typeof get_rows === "function" )
                //     {
                //         get_rows();
                //     }
                //     delete rsp["notice"];

                //     theForm.setAttribute("rel", JSON.stringify(rsp));

                // });

                // document.querySelector('[data-dismiss="modal"]').click();

            // };

        </script>

        <?php

        $this->setBlockModal(ob_get_clean());

    }
    // ----------------------------------------------------------------
    // Fin * Controla el modal de nuevo gasto
    // ----------------------------------------------------------------

    public function guardarGasto()
    {

        $gasto = new Gastos;
        $gasto->id_usuario = $this->admin_user->id_usuario;

        $gasto->monto =  floatval($_POST['monto']);

        $gasto->cantidad = $_POST['cantidad'];

        $gasto->descripcion = $_POST['descripcion'];

        $gasto->created_at = date("Y-m-d H:i:s");

        $gasto->save();

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'Gasto guardado con exito';

        // HArray::jsonResponse($json,null,false);

        $url = '/ls-admin/gastos';

        header('Location: '.$url);



    }

}