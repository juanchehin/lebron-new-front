<?php

require_once "./conf/init.php";
define("CP_ADMIN", "/ls-admin");
define('tmp_url', "");
#-- Accesos generales
$routes[CP_ADMIN . "/login"] = "AdminAcceso";
$routes[CP_ADMIN] = 'AdminHome'; # --
$routes[CP_ADMIN . "/ec-articulos"] = 'AdminArticulo';
$routes[CP_ADMIN . "/articulos"] = 'AdminArticulo';
$routes[CP_ADMIN . "/productos-vencidos"] = 'AdminVencidos';

$routes[CP_ADMIN . "/categorias"] = 'AdminCategoria';
$routes[CP_ADMIN . "/administracion"] = 'AdminMovimiento';
$routes[CP_ADMIN . "/inversores"] = 'AdminInversores';
$routes[CP_ADMIN . "/facturacion"] = 'AdminFacturacionElectronica';
$routes[CP_ADMIN . "/inversores/historico/(\w+)"] = 'AdminInversoresHistorico';
$routes[CP_ADMIN . "/incidencias"] = 'AdminIncidencias';
$routes[CP_ADMIN . "/iva"] = 'AdminIva';

$routes[CP_ADMIN . "/cliente(s)?"] = 'AdminCliente';
$routes[CP_ADMIN . "/customers"] = 'AdminCustomers';
$routes[CP_ADMIN . "/cliente(s)?/(nuevo|\d+/editar)"] = 'AdminCliente/form';
$routes[CP_ADMIN . "/(cliente(s)?|proveedor)/\d+/saldos"] = 'AdminCuenta';
$routes[CP_ADMIN . "/proveedores"] = 'AdminProveedor';
$routes[CP_ADMIN . "/sucursales"] = 'AdminSucursales';
$routes[CP_ADMIN . "/gastos"] = 'AdminGastos';
$routes[CP_ADMIN . "/gestion/postulantes"] = 'AdminPostulantes';

$routes[CP_ADMIN . "/remitos"] = 'AdminRemitos';
$routes[CP_ADMIN . "/venta-quimicos(\/espera)?"] = 'AdminVentaQuimicos';
$routes[CP_ADMIN . "/venta-quimicos/nuevo"] = 'AdminVentaQuimicos/ventaForm';

$routes[CP_ADMIN . "/cuentas-corrientes(\/espera)?"] = 'AdminCuentasCorrientes';


$routes[CP_ADMIN . "/proveedor(es)?/(nuevo|\d+/editar)"] = 'AdminProveedor/form';
//$routes[CP_ADMIN . "/productos"] = 'AdminProducto';
$routes[CP_ADMIN . "/(articulo|producto)s"] = 'AdminArticulo';
$routes[CP_ADMIN . "/(productos/|)log(\/\d+)?"] = 'AdminCompra/historial';
$routes[CP_ADMIN . "/productos/(nuevo|editar/\d+)"] = 'AdminArticulo/productoForm';
$routes[CP_ADMIN . "/productos/\d+/online"] = 'AdminProductoAtr/onlineData';

$routes[CP_ADMIN . "/ventas(\/espera)?"] = 'AdminVenta';
$routes[CP_ADMIN . "/ventas/(nuevo|form)(\/\d+)?"] = 'AdminVenta/ventaForm';
$routes[CP_ADMIN . "/ventas/pedidos"] = 'AdminPedidos';



$routes[CP_ADMIN . "/stock/\w+"] = 'AdminCompra/form';
$routes[CP_ADMIN . "/venta-online"] = 'AdminVenta/ventaOnline';


$routes[CP_ADMIN . "/estadistica/\w+(&\d+)?"] = "AdminHome/estadistica";
$routes[CP_ADMIN . "/gestion"] = AdminPago::class;
$routes[CP_ADMIN . "/usuarios"] = 'AdminUsuarios';
//$routes[CP_ADMIN . "usuarios/(nuevo|editar\/\d+)"] = 'AdminUsuarios/formPage';
$routes[CP_ADMIN . "/usuarios/(nuevo|editar\/\d+)"] = 'AdminUsuarios/form';
$routes[CP_ADMIN . "/perfil"] = 'AdminUsuarios/perfilUsuario';
$routes[CP_ADMIN . "/salir"] = 'AdminMain/logout';
#-- Public
$routes[tmp_url . "(|index(\.php)?)"] = 'FrontInicio';
$routes[tmp_url . "/pay"] = 'FrontPayment/pagar';
$routes[tmp_url . "/webhook"] = 'FrontPayment/webhook';
$routes[tmp_url . "/auth"] = 'FrontUsuario/auth';
$routes[tmp_url . "/result(/\w+)?"] = 'FrontPayment/response';
$routes[tmp_url . "/(cart|checkout)"] = 'FrontCart';
$routes[tmp_url . "/articulo/\d+(/.+)?"] = 'FrontArticulo/detalle';
$routes[tmp_url . "/" . FrontMain::listPath . "(\.html)?"] = 'FrontArticulo';
$routes[tmp_url . "/catalogo-mayorista(\.html)?"] = 'FrontArticuloMayoristas';

$routes[tmp_url . "/lista-de-precios(\.html)?"] = 'FrontArticulo/listadoPrecios';
$routes[tmp_url . "/franquicia(\.html)?"] = 'Institucional/franquicia';
$routes[tmp_url . "/terminos-y-condiciones(\.html)?"] = 'Institucional/terminosCondiciones';
$routes["politica-de-cookies(\.html)?"] = 'Institucion/politicaDeCookies';
$routes["politicas-de-privacidad(\.html)?"] = 'Institucion/politicaDePrivacidad';
$routes[tmp_url . "/defensa-del-consumidor(\.html)?"] = 'Institucional/defensaDelConsumidor';
$routes[tmp_url . "/politicas-de-seguridad(\.html)?"] = 'Institucional/politicasDeSeguridad';
$routes[tmp_url . "/contacto(\.html)?"] = 'Institucional/contacto';
$routes[tmp_url . "/ingresar-mi-cv(\.html)?"] = 'Institucional/ingresarMiCv';
$routes[tmp_url . "/quienes-somos(\.html)?"] = 'Institucional/quienesSomos';
//* ***** Mayoristas ******* */
$routes[tmp_url . "(|index(\.php)?)/mayoristas"] = 'FrontInicioMayoristas';
$routes[tmp_url . "/mayoristas/(cart|checkout)"] = 'FrontCartMayoristas';
$routes[tmp_url . "/datos-exitosos"] = 'DatosExitosos';
$routes[CP_ADMIN . "/gestion/clientes-mayoristas"] = 'AdminVentaMayoristas';
//* ***** Fin Mayoristas ******* */

#--
$routes["registrarse"] = 'Usuarios/registrarse';
$routes["registro-comercio"] = 'Usuarios/registroComercio';
$routes["validar-cuenta/(\w+)"] = 'Usuarios/validarCuenta';
$routes["datos-guardados/\w+"] = 'Usuarios/postRegistro';
$routes["restablecer-contrasena/\w+"] = 'Usuarios/restablecerContrasena';
#-- Accesos usuarios logueados
$routes['pagina-no-encontrada'] = 'Main/paginaNoEncontrada';
#--
$res = Router::processRoutes($routes);
if ( $res == Router::ERROR_404 )
{
    //Router::redirect(HTTP_HOST . "/pagina-no-encontrada");
}
?>