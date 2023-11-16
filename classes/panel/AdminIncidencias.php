<?php

class AdminIncidencias extends AdminMain

{

    

    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuContable);

        if ( !in_array($this->admin_user->id_usuario, [1, 28]) )

        {

            $url = '/ls-admin';

            header('Location: '.$url);

        }
    }



    public function index()
    {

        $this->setPageTitle("Incidencias - Logs del sistema");

        // $this->setBotonNuevo("Nuevo Gasto");

        #--
        
        // $export = new ExportOpts();

        // $export->setExcelUrl($url = "!AdminProducto/exportar");

        // $export->setPdfUrl($url . "?pdf=1");

        // $this->setBotonNuevo("Agregar gasto", self::sysUrl . "/gastos/nuevoGastoForm", "<span class='pull-right' id='dv-export'></span>");}

        // $this->setBotonNuevo("Agregar gasto", "javascript:void(0)");

        $columns[] = "#";

        $columns[] = "Fecha";

        $columns[] = "Usuario";

        $columns[] = "# Op.";

        $columns[] = "Operacion";

        $columns[] = "Detalle";

        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        $table->setHideSearchBox();

        $table->setHideBuscador();

        $table->setFiltroFechaIncidencia();
        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("incidencias-index");

    }


    // **************************************** filtro por fecha ************************************************
    public function getRows()
    {
        $fechaIncidencia = HDate::sqlDate($_GET['fechaIncidencia'] ?: date('d/m/Y'));

        $fechaIncidencia = date("Y-m-d", strtotime($fechaIncidencia));

        #--

        $query = Incidencias::whereRaw(
                "(fecha_hora >= ? AND fecha_hora <= ?)", 
                [
                   $fechaIncidencia ." 00:00:00", 
                   $fechaIncidencia ." 23:59:59"
                ]
        );

        $incidencias = $query->leftjoin("usuario", "incidencia.id_usuario", '=', 'usuario.id_usuario');
        $incidencias = $query->orderBy("id", "DESC");
        $count = $incidencias->count();

        // $incidencias_suma = $query->orderBy("id", "DESC");  

        $result = $incidencias->paginate($this->x_page);

        $data = null;

        foreach ($result as $index => $incidencias)
        {                                  

                $data .= "<tr id='" . ($id = $incidencias->id_gasto) . "'>";

                    $data .= "<td>{$incidencias->id}</td>";

                    $data .= "<td>{$incidencias->fecha_hora}</td>";

                    $data .= "<td>{$incidencias->usuario}</td>";

                    $data .= "<td>{$incidencias->id_operacion}</td>";

                    $data .= "<td>{$incidencias->operacion}</td>";

                    $data .= "<td>{$incidencias->detalle}</td>";

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


}