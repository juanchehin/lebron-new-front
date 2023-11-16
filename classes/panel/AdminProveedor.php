<?php

class AdminProveedor extends AdminRegistro
{
    public function __construct()
    {
        $this->setItemSeleccionado(MenuPanel::menuProveedor);
        parent::__construct();
    }

    public function index()
    {
        $this->setPageTitle("Proveedores");
        parent::index("Proveedor");
    }

    public function form($id = null)
    {
        parent::form($id);
    }
}