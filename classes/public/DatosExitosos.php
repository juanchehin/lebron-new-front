<?php



class DatosExitosos extends FrontMain

{

    public function __construct()

    {

        parent::__construct();

        $this->setParams('modalBlock', $this->modalBlock());

    }



    public function index()

    {

        $this->setPageTitle("Datos exitosos");

        $this->setBody("datos-exitosos.html");

    }


}