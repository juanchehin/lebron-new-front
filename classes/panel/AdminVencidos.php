<?php


class AdminVencidos extends AdminMain

{

    private $file_name;


    public function __construct()

    {

        parent::__construct();

        $this->setItemSeleccionado(MenuPanel::menuProductosVencidos);

    }



    public function index($online = false)

    {

        $this->setPageTitle("Productos vencidos");

        $this->setBotonNuevo("Alta producto", "javascript:void(0)");

        $columns[] = "#";
        
        $columns[] = "Producto";
        
        $columns[] = "Cantidad";

        $columns[] = "Descripcion";

        $columns[] = "Vencimiento";

        
        if ( in_array($this->admin_user->id_usuario, [1, 28]) )
        {
            $columns[] = "";
        }

        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        $table->setHideSearchBox();

        $table->setSearchBoxProdVencido();

        $table->setHideBuscador();

        // $table->setMostrarBuscador();

        $table->clean_filter();

        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("productos-vencidos-index");

    }


    // ----------------------------------------------------------------
    // 
    // ----------------------------------------------------------------
    public function getRows($exportar = false)
    {

        // $articulos_vencidos = ProductosVencidos::orderBy("created_at", "DESC");     
        $busqueda_producto_vencido = trim($_POST['search_producto_vencido']);

        if($busqueda_producto_vencido)
        {
            $articulos_vencidos = ProductosVencidos::where('producto', 'LIKE', "%{$busqueda_producto_vencido}%");
        }
        else
        {
            $articulos_vencidos = ProductosVencidos::orderBy("created_at", "DESC");
        }


        $count = $articulos_vencidos->count();

        $result = $articulos_vencidos->paginate($this->x_page);

        $data = null;

        foreach ($result as $index => $producto)
        {   
            $data .= "<tr id='" . ($id = $producto->idproductos_vencidos) . "'>";
                $data .= "<td>{$producto->idproductos_vencidos}</td>";
                $data .= "<td>{$producto->producto}</td>";
                $data .= "<td>{$producto->monto}</td>";
                $data .= "<td>{$producto->observaciones}</td>";                
                $data .= "<td>" . date('Y-m-d', strtotime($producto->fecha_vencimiento)) . "</td>";

                if ( in_array($this->admin_user->id_usuario, [1, 28]) )
                {
                    $data .= "<td><a href='javascript:void(0)' onclick='get_modal_form_editar_producto_vencido($producto)'><i class='fa fa-edit'></i></a>";        
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
    // Controla el modal 
    // ----------------------------------------------------------------
    public function modalForm()
    {
        ob_start();

        $form_title = "Nuevo producto";

        $this->setPageTitle($form_title);


        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">
                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarProductoVencido" id="alta-producto-vencido" autocomplete="off" method="post">
                    <div class="form-group">

                        <!-- **** -->
                        <div class="col-md-12 ">
                            <label >Producto : </label>
                            <input id="producto" name="producto" class="form-control" name="producto"></input>
                        </div>

                        <!-- **** -->
                         <div class="col-md-12 ">
                            <label>Fecha Vencimiento: </label>
                            <input id="fecha-vencimiento" name="fecha-vencimiento" class="form-control" name="fecha-vencimiento" type="date"></input>
                        </div>

                         <!-- **** -->
                         <div class="col-md-12 form-group">
                            <label for="monto">Monto : </label>
                            <input type="text" id="monto" name="monto" class="form-control" name="monto" value='0'></input>
                        </div>


                        <div class="col-md-12 form-group">
                            <label for="observaciones">Observaciones : </label>
                            <textarea id="observaciones" name="observaciones" class="form-control" name="observaciones"></textarea>
                        </div>

                        <div class="col-md-12 text-right">    
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </form>
                <!-- ===== Fin form ===== -->
            </div>
        </div>

        <script type="text/javascript">
            var fechaActual = new Date().toISOString().split('T')[0];
            document.getElementById("fecha").value = fechaActual;
        </script>

        <?php

        $this->setBlockModal(ob_get_clean());

    }




    // ----------------------------------------------------------------
    //   Almacena una nueva 
    // ----------------------------------------------------------------
    public function guardarProductoVencido()
    {

        $producto_v = new ProductosVencidos();
        
        if(!$_POST['fecha-vencimiento'])
        {
            $producto_v->fecha_vencimiento = null;
        }else{
            $producto_v->fecha_vencimiento = $_POST['fecha-vencimiento'];
        }

        $producto_v->producto = $_POST['producto'];                
        $producto_v->monto = $_POST['monto'];        
        $producto_v->observaciones = $_POST['observaciones'];
        $producto_v->save();
      
        // $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $json['label'] = 'Producto guardado con exito';

        $url = '/ls-admin/productos-vencidos';

        header('Location: '.$url);


    }

    // ----------------------------------------------------------------
    // Abre un modal para editar
    // ----------------------------------------------------------------
    public function edicionProductoVencido()
    {
        $idproductos_vencidos = $_POST['idproductos_vencidos'];
        
        // Monto
        $monto = CuentasCorrientes::where('idcuentas_corrientes', $idproductos_vencidos)->first()->monto;

        // Tipo de pago
        $tipo_pago = CuentasCorrientes::where('idcuentas_corrientes', $idproductos_vencidos)->first()->id_concepto;

        if(!$monto)
        {
            $monto = 0;
        }

        // 
        $fecha_vencimiento = $_POST['fecha-vencimiento'];
        $producto = $_POST['producto'];
        $monto = $_POST['monto'];
        $observaciones = $_POST['observaciones'];

        // 
        $form_title = "Edicion - " . $idproductos_vencidos;

        $this->setPageTitle($form_title);

        ob_start();

        ?>

        <div class="panel panel-default">

            <div class="panel-heading" style="padding:5px;text-align: center"><?= $form_title ?></div>

            <div class="panel-body">

                <!-- ===== form ===== -->
                <form action="!<?= self::class ?>/guardarEditarProductoVencido" id="editar-producto-vencido" autocomplete="off" method="post">
                    <div class="form-group">

                        <input type="hidden" name="idproductos_vencidos" value="<?= $idproductos_vencidos ?>">

                        <!-- **** -->
                        <div class="col-md-12 ">
                            <label for="producto">Producto : </label>
                            <input id="producto" name="producto" class="form-control" value="<?= $producto ?>"></input>
                        </div>
                         <!-- **** -->
                         <div class="col-md-12 ">
                            <label for="fecha">Fecha : </label>
                            <input id="fecha-vencimiento" class="form-control hasDatepicker" name="fecha-vencimiento" type="date" value="<?= $fecha_vencimiento ?>"></input>
                        </div>

                        <!-- **** -->
                         <div class="col-md-12 ">
                            <label for="monto"> Monto : </label>
                            <input id="monto" name="monto" class="form-control" value="<?= $monto ?>"></input>
                        </div>
                        
                        <!-- **** -->
                        <div class="col-md-12 form-group">
                            <label for="observaciones">Observaciones : </label>
                            <textarea id="observaciones" name="observaciones" class="form-control" name="observaciones">
                                <?= $observaciones ?>
                            </textarea>
                        </div>

                        <div class="col-md-12 text-right">    
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </form>
                <!-- ===== Fin form ===== -->
                </div>
            </div>
        </div>

        <?php

        $this->setBlockModal(ob_get_clean());

    }
    // ****************************************************************
    // Update cuenta
    // ****************************************************************
    public function guardarEditarProductoVencido()
    {

        $idproductos_vencidos = $_POST['idproductos_vencidos'];
        $fecha_vencimiento = $_POST['fecha-vencimiento'];
        $producto = $_POST['producto'];
        $monto = $_POST['monto'];
        $observaciones = $_POST['observaciones'];

        //
        $producto_v = ProductosVencidos::find($idproductos_vencidos);

        $producto_v->producto = $producto;

        if($fecha_vencimiento){
            $producto_v->fecha_vencimiento = $fecha_vencimiento;
        }

        if($monto){
            $producto_v->monto = $monto;
        }

        $producto_v->producto = $producto;
        $producto_v->observaciones = $observaciones;
        $producto_v->updated_at = date("Y-m-d H:i:s");

        $producto_v->save();
      
        // $this->_setLineas(null, true);

        $json['notice'] = "La operaci&oacute;n se registr&oacute; correctamente";

        $json['ok'] = 'Ok';

        $json['codigo'] = '';

        $url = '/ls-admin/productos-vencidos';

        header('Location: '.$url);

    }
    

}