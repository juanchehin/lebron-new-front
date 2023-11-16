<?php



class MenuPanel

{

    const menuInicio = "INICIO";

    const menuProductos = "PRODUCTO";

    const menuProductosVencidos = "PRODUCTO_VENCIDOS";

    const menuProveedor = "proveedor";

    const menuSucursal = "sucursal";

    const menuVentas = "venta";

    const menuUsuarios = "USUARIO";

    const menuNomina = "NOMINA";

    const menuCaja = "CUENTA";

    const menuCompra = "ingreso";

    const menuStock = "stock";

    const menuContable = "gestion";

    const menuRegistro = "cliente";

    const menuCategorias = "categoria";

    const menuOnlineArticulos = "online_articulo";

    const menuGastos = "gastos";

    const menuPostulantes = "postulantes";

    const menuMayoristas = "mayoristas";    

    const menuRemitos = "remitos";

    const menuQuimicos = "quimicos";

    const menuCuentasCorrientes = "cuentas_corrientes";    

    const menuInversores = "inversores";    

    const menuFacturacion = "facturacion";    

    const menuClientes = "customers";    


    private static function getItems()

    {

        $json_file = file_get_contents("conf/menu-panel.json");

        return (array)json_decode($json_file, true);

    }



    public static function getItemsParent()

    {

        $parents = array();

        foreach (static::getItems() as $item)

        {

            if ( !$item['id_parent'] && $item['habilitado'] )

            {

                $parents[] = $item; // Menu principal

            }

        }



        return $parents;

    }



    public static function getItemsChild($id_parent)

    {

        $children = array();

        foreach (static::getItems() as $child)

        {

            if ( ($child['id_parent'] == $id_parent) && $child['habilitado'] )

            {

                $children[] = $child;

            }

        }

        return $children;

    }



}

