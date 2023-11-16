<?php

class AdminSucursales extends AdminMain

{

    

    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuSucursal);
    }



    public function index()
    {

        $this->setPageTitle("Sucursales");

        // $this->controlPermiso(Permiso::permisoVer);

        $url = $_SERVER['HTTP_REFERER'] ?: self::sysUrl;
        //Es luciana villagra? id usuario 29
        if ( $this->admin_user->id_usuario == 29 )
        {
            Router::redirect($url);
        }
        #--
        

        #--

        // $export = new ExportOpts();

        // $export->setExcelUrl($url = "!AdminProducto/exportar");

        // $export->setPdfUrl($url . "?pdf=1");

        // $this->setBotonNuevo("Agregar", self::sysUrl . "/productos/nuevo", "<span class='pull-right' id='dv-export'></span>");

        $columns[] = "Codigo";

        $columns[] = "Producto";

        $columns[] = "Detalle";

        // $columns[] = "Mínimo.text-center";

        $columns[] = "Cantidad";

        $columns[] = "Precio";

        $columns[] = "Acci&oacute;n.col-md-2.text-center";


        $control = $this->_selectSucursales();

        // $control .= "<label for='alerta' class='btn btn-warning' style='padding:2px 4px'><input type='checkbox' id='alerta' />&nbsp;Stock mínimo</label>";

        // $log_url = "<a href='" . self::sysUrl . "/productos/log' class='pull-right'>Historial</a>";

        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        $table->setHideSearchBox();

        $table->setHtmlControl($control);

        $table->setRows($this->getRows());

        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("articulo-index");

    }

    private function _selectSucursales()
    {
        $control = "<select id='id_sucursal' name='id_sucursal' class='form-control' required>";

        $control .= "<option value='' selected='selected'>Seleccione una Sucursal</option>";

        $control .= "<option value='9'>Warrior Gym</option>";

        $control .= "<option value='11'>Optimus Gym</option>";

        $control .= "<option value='13'>Tamara Gym</option>";

        $control .= "</select>";

        return $control;

    }

    public function getRows()
    {
        $id_sucursal = $_POST['id_sucursal'];

        #--

        $where['borrado'] = 0;

        #--
        
        $query = Articulo::where($where);
        

        if($_POST['id_sucursal'] == null)
        {
            foreach (array_keys(Local::$_LOCALES) as $v)
            {
                if($v == 9 || $v == 11 || $v == 13)
                {
                    $where[] = "CAST(JSON_EXTRACT(`cantidad`,'$.\"{$v}\"') AS UNSIGNED) <= `stock_alerta`";
                }
                
            }
        }else
        {
            foreach (array_keys(Local::$_LOCALES) as $v)
            {

                if($id_sucursal == $v)
                {
                    $where[] = "CAST(JSON_EXTRACT(`cantidad`,'$.\"{$v}\"') AS UNSIGNED) <= `stock_alerta`";
                }
                
            }
        }

        

        $where = implode(" OR ", $where);
            
        $query = $query->whereRaw("({$where})");

        $articulos = $query->orderBy("id_producto", "DESC");        

        $articulos_suma = $articulos->get();       

        $suma_precio_publico_warrior = 0;
        $suma_precio_mayorista_warrior = 0;

        $suma_precio_publico_optimus = 0;
        $suma_precio_mayorista_optimus = 0;

        $suma_precio_publico_tamara = 0;
        $suma_precio_mayorista_tamara = 0;

        
        foreach ($articulos_suma as $index => $articulo_suma)
        {        
                foreach ($articulo_suma->array_precios as $label => $precio)
                {
                    $decode1 = json_decode($articulo_suma, TRUE);            
                    $decode2 = json_decode($decode1["cantidad"], TRUE); 
                    $cantidad =  $decode2[9];

                    if($label == 'publico')
                    {                  
                        $suma_precio_publico_warrior += ($precio * $cantidad);
                    }

                    if($label == 'mayorista')
                    {
                        $suma_precio_mayorista_warrior += ($precio * $cantidad);
                    }

                }

                foreach ($articulo_suma->array_precios as $label => $precio)
                {
                    $decode1 = json_decode($articulo_suma, TRUE);            
                    $decode2 = json_decode($decode1["cantidad"], TRUE); 
                    $cantidad =  $decode2[11];

                    if($label == 'publico')
                    {                  
                        $suma_precio_publico_optimus += ($precio * $cantidad);
                    }

                    if($label == 'mayorista')
                    {
                        $suma_precio_mayorista_optimus += ($precio * $cantidad);
                    }

                }

                foreach ($articulo_suma->array_precios as $label => $precio)
                {
                    $decode1 = json_decode($articulo_suma, TRUE);            
                    $decode2 = json_decode($decode1["cantidad"], TRUE); 
                    $cantidad =  $decode2[13];

                    if($label == 'publico')
                    {                  
                        $suma_precio_publico_tamara += ($precio * $cantidad);
                    }

                    if($label == 'mayorista')
                    {
                        $suma_precio_mayorista_tamara += ($precio * $cantidad);
                    }

                }
        }


        $this->reporte($articulos->get());
        
        $count = $articulos->count();

        $result = $articulos->paginate($this->x_page);

        $data = $table = null;

        foreach ($result as $index => $articulo)
        {                       
                $decode1 = json_decode($articulo, TRUE);
            
                $decode2 = json_decode($decode1["cantidad"], TRUE);                

                $data .= "<tr id='" . ($id = $articulo->id_producto) . "'>";

                $data .= "<td>{$articulo->codigo}</td>";

                $data .= "<td>";

                $data .= $id . " - " . $articulo->nombre;

                $data .= "</td>";

                $data .= "<td>" . ($articulo->detalle ?: " - ") . "</td>";

                $data .= "<td>{$decode2[$id_sucursal]}</td>";

                $data .= "<td class='amount'>";

                foreach ($articulo->array_precios as $label => $precio)
                {
                    $data .= strtoupper($label . " {$precio}") . "<br/>";
                }

                $data .= "</td>";
                $data .= "<td class='text-center'>";
                $data .= "<a href='javascript:void(0)' class='btn btn-info' onclick='get_form(\"{$id}\")'><i class='fa fa-calculator'></i> Stock</a>";

                $data .= "</td>";               
                

        }

        #--

        $data .= "<tr class='not' data-count='{$count}'><td colspan='12'>{$this->replaceLinks($result->links())}</td></tr>";

        #--

        $data .= "<div class='row'>";

            $data .= "<div class='col-md-4'>";

                $data .= "<p id='suma_precio_publico'>Suma precio publico Warrior Gym : $ " . $suma_precio_publico_warrior . "</p>";
                $data .= "<p id='suma_precio_mayorista'>Suma precio mayorista Warrior Gym :  $ " . $suma_precio_mayorista_warrior . "</p>";

            $data .= "</div>";

            $data .= "<div class='col-md-4'>";

                $data .= "<p id='suma_precio_publico'>Suma precio publico Optimus Gym : $ " . $suma_precio_publico_optimus . "</p>";
                $data .= "<p id='suma_precio_mayorista'>Suma precio mayorista Optimus Gym :  $ " . $suma_precio_mayorista_optimus . "</p>";

            $data .= "</div>";

            $data .= "<div class='col-md-4'>";

                $data .= "<p id='suma_precio_publico'>Suma precio publico Tamara Gym : $ " . $suma_precio_publico_tamara . "</p>";
                $data .= "<p id='suma_precio_mayorista'>Suma precio mayorista Tamara Gym :  $ " . $suma_precio_mayorista_tamara . "</p>";
            
            $data .= "</div>";

        $data .= "</div>";

        if ( self::isXhrRequest() )

        {

            die($data);

        }

        return $data;

    }


}