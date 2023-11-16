<?php

class AdminPostulantes extends AdminMain

{

    

    public function __construct()
    {
        parent::__construct();
        $this->setItemSeleccionado(MenuPanel::menuPostulantes);
    }



    public function index()
    {

        $this->setPageTitle("Postulantes");

        $columns[] = "Fecha";

        $columns[] = "Apellidos";

        $columns[] = "Nombres";

        $columns[] = "Telefono";

        $columns[] = "DNI";

        $columns[] = "Email";

        $columns[] = "Domicilio";

        $columns[] = "Puesto deseado";

        $columns[] = "CV";

        // **** TABLA ****
        $table = new HDataTable();

        $table->setColumns($columns);

        $table->setHideDateRange();

        $table->setHideSearchBox();

        $table->setHideBuscador();
        
        $values['_table'] = $table->drawTable();

        $this->setParams($values);

        #--

        $this->setBody("postulantes-index");

    }


    // **************************************** filas ************************************************
    public function getRows()
    {
        #--

        $query = Postulantes::orderBy("id_postulante", "DESC");

        $count = $query->count();

        $result = $query->paginate($this->x_page);

        $data = null;

        foreach ($result as $index => $postulante)
        {                                  

                $data .= "<tr id='" . ($id = $postulante->id_gasto) . "'>";

                    $data .= "<td>{$postulante->created_at}</td>";

                    $data .= "<td>{$postulante->apellidos}</td>";

                    $data .= "<td>{$postulante->nombres}</td>";

                    $data .= "<td>{$postulante->telefono}</td>";

                    $data .= "<td>{$postulante->dni}</td>";

                    $data .= "<td>{$postulante->email}</td>";

                    $data .= "<td>{$postulante->domicilio}</td>";

                    $data .= "<td>{$postulante->puesto}</td>";

                    $data .=  "<td>
                                <a href='" . $_SERVER["HTTP_ORIGIN"] . "/media/uploads/" . "{$postulante->cv}' target='_blank'><i class='fa fa-file-pdf'></i></a>
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


}