<?php





class AdminCliente extends AdminRegistro

{

    public function __construct()

    {

        $this->setItemSeleccionado(MenuPanel::menuClientes);

        parent::__construct();

    }

}