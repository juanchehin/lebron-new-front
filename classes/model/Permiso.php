<?php

class Permiso extends MenuPanel
{
    const permisoVer = "consultar";
    const permisoCrear = "registrar";
    const permisoStockVenta = "venta";
    const permisoCuentaVer = "cuenta_ver";
    const permisoEditar = "editar";
    const permisoBorrar = "borrar";
    const permisoStockIngreso = "ingreso";
    const permisoStockTraspaso = "traspaso";
    const permisoStock = "editar_stock";
    const permisoPublicar = "publicar";
    const permisoPerfil = "editar_perfil";
    const permisoClave = "cambiar_clave";

    public static $_PERMISOS = array(
        self::menuProductos => array(
            self::permisoVer,
            self::permisoCrear,
            self::permisoEditar,
            self::permisoBorrar,
            self::permisoPublicar,
            self::permisoStock
        ),
        //self::menuCompra => array(
        self::menuStock => array(
            self::permisoVer,
            self::permisoStockIngreso,
            self::permisoStockTraspaso,
        ),
        self::menuVentas => array(
            self::permisoVer,
            self::permisoCrear,
            self::permisoBorrar
        ),
        self::menuProveedor => array(
            self::permisoVer,
            self::permisoCrear,
            self::permisoEditar,
            self::permisoBorrar,
            self::permisoCuentaVer
        ),
        self::menuContable => array(
            self::permisoVer,
            self::permisoCrear,
            self::permisoEditar,
            self::permisoBorrar,
        ),
        self::menuUsuarios => array(
            self::permisoVer,
            self::permisoCrear,
            self::permisoEditar,
            self::permisoPerfil,
            self::permisoClave,
            self::permisoBorrar
        )
    );

    public static function getPermisos($key = null)
    {
        $permisos = static::$_PERMISOS;
        $permisos[self::menuRegistro] = $permisos[self::menuProveedor];
        if ( $key )
        {
            return $permisos[$key];
        }
        return $permisos;
    }

    public static function permisoRol($rol)
    {
        $permisos = static::getPermisos();
        if ( !in_array($rol, [Usuario::USR_PANEL_ADMIN]) )
        {
            unset($permisos[self::menuUsuarios]);
            unset($permisos[self::menuProductos]);
        }
        return $permisos;
    }
}