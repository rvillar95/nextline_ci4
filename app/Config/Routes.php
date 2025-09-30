<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Web\HomeController::index');

$routes->get('/test', 'UsuarioController::test');

$routes->get('/login', 'ViewController::login');

$routes->get('/layout', 'ViewController::layout');



$routes->group('dashboard', function ($routes) {
    $routes->get('inicio', 'ViewController::index');

    $routes->get('registro/usuario', 'ViewController::registro');
    $routes->get('menu', 'ViewController::menu');
    $routes->get('layout_menu', 'ViewController::layout_menu');


    $routes->group('usuario', function ($routes2) {
        $routes2->get('', 'Dashboard\UsuarioController::inicio');
        $routes2->get('registro', 'Dashboard\UsuarioController::registro');
        $routes2->get('editar/(:num)', 'Dashboard\UsuarioController::editar/$1');
        $routes2->get('lista', 'Dashboard\UsuarioController::lista');
        $routes2->get('getUsuarios', 'Dashboard\UsuarioController::getUsuarios');
        $routes2->post('registrar', 'Dashboard\UsuarioController::registrar');
        $routes2->post('update', 'Dashboard\UsuarioController::update');
        $routes2->post('update/clave', 'Dashboard\UsuarioController::update_clave');
        $routes2->post('eliminar', 'Dashboard\UsuarioController::eliminar');
    });

    $routes->group('perfil', function ($routes2) {
        $routes2->get('registro', 'Dashboard\PerfilController::registro');
        $routes2->get('editar/(:num)', 'Dashboard\PerfilController::editar/$1');
        $routes2->get('lista', 'Dashboard\PerfilController::lista');
        $routes2->get('getPerfiles', 'Dashboard\PerfilController::getPerfiles');
        $routes2->post('registrar', 'Dashboard\PerfilController::registrar');
        $routes2->post('update', 'Dashboard\PerfilController::update');
        $routes2->post('eliminar', 'Dashboard\PerfilController::eliminar');
    });

    $routes->group('modulo', function ($routes2) {
        $routes2->get('registro', 'Dashboard\ModuloController::registro');
        $routes2->get('editar/(:num)', 'Dashboard\ModuloController::editar/$1');
        $routes2->get('lista', 'Dashboard\ModuloController::lista');
        $routes2->get('getModulo', 'Dashboard\ModuloController::getModulo');
        $routes2->post('registrar', 'Dashboard\ModuloController::registrar');
        $routes2->post('update', 'Dashboard\ModuloController::update');
        $routes2->post('update/clave', 'Dashboard\ModuloController::update_clave');
        $routes2->post('eliminar', 'Dashboard\ModuloController::eliminar');
    });

    $routes->group('perfil-detalle', function ($routes2) {
        $routes2->get('registro', 'Dashboard\PerfilDetalleController::registro');
        $routes2->get('editar/(:num)', 'Dashboard\PerfilDetalleController::editar/$1');
        $routes2->get('lista', 'Dashboard\PerfilDetalleController::lista');
        $routes2->get('getPerfilDetalle', 'Dashboard\PerfilDetalleController::getPerfilDetalle');
        $routes2->post('registrar', 'Dashboard\PerfilDetalleController::registrar');
        $routes2->post('update', 'Dashboard\PerfilDetalleController::update');
        $routes2->post('eliminar', 'Dashboard\PerfilDetalleController::eliminar');
    });

    $routes->group('modulo-detalle', function ($routes2) {
        $routes2->get('registro', 'Dashboard\ModuloDetalleController::registro');
        $routes2->get('editar/(:num)', 'Dashboard\ModuloDetalleController::editar/$1');
        $routes2->get('lista', 'Dashboard\ModuloDetalleController::lista');
        $routes2->get('getModuloDetalle', 'Dashboard\ModuloDetalleController::getModuloDetalle');
        $routes2->post('registrar', 'Dashboard\ModuloDetalleController::registrar');
        $routes2->post('update', 'Dashboard\ModuloDetalleController::update');
        $routes2->post('eliminar', 'Dashboard\ModuloDetalleController::eliminar');
    });

    $routes->group('servicio', function ($routes2) {
        $routes2->get('registro', 'Dashboard\ServicioController::registro'); //vista
        $routes2->get('editar/(:num)', 'Dashboard\ServicioController::editar/$1'); //vista
        $routes2->get('lista', 'Dashboard\ServicioController::lista'); //vista
        $routes2->get('getServicio', 'Dashboard\ServicioController::getServicio'); //get Data
        $routes2->post('registrar', 'Dashboard\ServicioController::registrar'); //accion
        $routes2->post('update', 'Dashboard\ServicioController::update'); //accion
        $routes2->post('eliminar', 'Dashboard\ServicioController::eliminar'); //accion
    });

    $routes->group('galeria', function ($routes2) {
        $routes2->get('registro', 'Dashboard\GaleriaController::registro'); //vista
        $routes2->get('editar/(:num)', 'Dashboard\GaleriaController::editar/$1'); //vista
        $routes2->get('lista', 'Dashboard\GaleriaController::lista'); //vista
        $routes2->get('getGaleria', 'Dashboard\GaleriaController::getGaleria'); //get Data
        $routes2->post('registrar', 'Dashboard\GaleriaController::registrar'); //accion
        $routes2->post('update', 'Dashboard\GaleriaController::update'); //accion
        $routes2->post('eliminar', 'Dashboard\GaleriaController::eliminar'); //accion
    });

    $routes->group('servicio-categoria', function ($routes2) {
        $routes2->get('registro', 'Dashboard\ServicioCategoriaController::registro'); //vista
        $routes2->get('editar/(:num)', 'Dashboard\ServicioCategoriaController::editar/$1'); //vista
        $routes2->get('lista', 'Dashboard\ServicioCategoriaController::lista'); //vista
        $routes2->get('getServicioCategoria', 'Dashboard\ServicioCategoriaController::getServicioCategoria'); //get Data
        $routes2->post('registrar', 'Dashboard\ServicioCategoriaController::registrar'); //accion
        $routes2->post('update', 'Dashboard\ServicioCategoriaController::update'); //accion
        $routes2->post('eliminar', 'Dashboard\ServicioCategoriaController::eliminar'); //accion
    });

    $routes->group('galeria-categoria', function ($routes2) {
        $routes2->get('registro', 'Dashboard\GaleriaCategoriaController::registro'); //vista
        $routes2->get('editar/(:num)', 'Dashboard\GaleriaCategoriaController::editar/$1'); //vista
        $routes2->get('lista', 'Dashboard\GaleriaCategoriaController::lista'); //vista
        $routes2->get('getGaleriaCategoria', 'Dashboard\GaleriaCategoriaController::getGaleriaCategoria'); //get Data
        $routes2->post('registrar', 'Dashboard\GaleriaCategoriaController::registrar'); //accion
        $routes2->post('update', 'Dashboard\GaleriaCategoriaController::update'); //accion
        $routes2->post('eliminar', 'Dashboard\GaleriaCategoriaController::eliminar'); //accion
    });

    $routes->group('proyecto', function ($routes2) {
        $routes2->get('registro', 'Dashboard\ProyectoController::registro'); //vista
        $routes2->get('editar/(:num)', 'Dashboard\ProyectoController::editar/$1'); //vista
        $routes2->get('lista', 'Dashboard\ProyectoController::lista'); //vista
        $routes2->get('getProyecto', 'Dashboard\ProyectoController::getProyecto'); //get Data
        $routes2->post('registrar', 'Dashboard\ProyectoController::registrar'); //accion
        $routes2->post('update', 'Dashboard\ProyectoController::update'); //accion
        $routes2->post('eliminar', 'Dashboard\ProyectoController::eliminar'); //accion
        $routes2->post('setPortada', 'Dashboard\ProyectoController::setPortada'); //accion
        $routes2->post('eliminarImagen', 'Dashboard\ProyectoController::eliminarImagen'); //accion
    });

    $routes->group('leads', function ($routes2) {
        $routes2->get('lista', 'Dashboard\LeadController::lista');
        $routes2->get('getLeads', 'Dashboard\LeadController::getLeads');
        $routes2->get('getServicios', 'Dashboard\LeadController::getServicios');
        $routes2->post('cambiarEstado', 'Dashboard\LeadController::cambiarEstado');
        $routes2->post('eliminar', 'Dashboard\LeadController::eliminar');
    });

    $routes->group('testimonio', function ($routes2) {
        $routes2->get('registro', 'Dashboard\TestimonioController::registro'); //vista
        $routes2->get('editar/(:num)', 'Dashboard\TestimonioController::editar/$1'); //vista
        $routes2->get('lista', 'Dashboard\TestimonioController::lista'); //vista
        $routes2->get('getTestimonios', 'Dashboard\TestimonioController::getTestimonios'); //get Data
        $routes2->post('registrar', 'Dashboard\TestimonioController::registrar'); //accion
        $routes2->post('update', 'Dashboard\TestimonioController::update'); //accion
        $routes2->get('eliminar/(:num)', 'Dashboard\TestimonioController::eliminar/$1'); //accion
    });

    $routes->group('cliente', function ($routes2) {
        $routes2->get('registro', 'Dashboard\ClienteController::registro'); //vista
        $routes2->get('editar/(:num)', 'Dashboard\ClienteController::editar/$1'); //vista
        $routes2->get('lista', 'Dashboard\ClienteController::lista'); //vista
        $routes2->get('detalle/(:num)', 'Dashboard\ClienteController::detalle/$1'); //vista
        $routes2->get('getClientes', 'Dashboard\ClienteController::getClientes'); //get Data
        $routes2->get('getClientesSelect', 'Dashboard\ClienteController::getClientesSelect'); //get Data
        $routes2->post('registrar', 'Dashboard\ClienteController::registrar'); //accion
        $routes2->post('update', 'Dashboard\ClienteController::update'); //accion
        $routes2->post('activar', 'Dashboard\ClienteController::activar'); //accion
        $routes2->post('eliminar/(:num)', 'Dashboard\ClienteController::eliminar/$1'); //accion
    });

    $routes->group('cotizacion', function ($routes2) {
        $routes2->get('registro', 'Dashboard\CotizacionController::registro'); //vista
        $routes2->get('editar/(:num)', 'Dashboard\CotizacionController::editar/$1'); //vista
        $routes2->get('lista', 'Dashboard\CotizacionController::lista'); //vista
        $routes2->get('detalle/(:num)', 'Dashboard\CotizacionController::detalle/$1'); //vista
        $routes2->get('getCotizaciones', 'Dashboard\CotizacionController::getCotizaciones'); //get Data
        $routes2->get('getClientesSelect', 'Dashboard\CotizacionController::getClientesSelect'); //get Data
        $routes2->get('generarPDF/(:num)', 'Dashboard\CotizacionController::generarPDF/$1'); //get Data
        $routes2->post('registrar', 'Dashboard\CotizacionController::registrar'); //accion
        $routes2->post('update', 'Dashboard\CotizacionController::update'); //accion
        $routes2->post('eliminar/(:num)', 'Dashboard\CotizacionController::eliminar/$1'); //accion
        $routes2->post('convertir-proyecto/(:num)', 'Dashboard\CotizacionController::convertirEnProyecto/$1'); //accion
    });

    $routes->group('empresa', function ($routes2) {
        $routes2->get('lista', 'Dashboard\EmpresaController::lista'); //redirige a registro
        $routes2->get('registro', 'Dashboard\EmpresaController::registro'); //vista
        $routes2->post('registrar', 'Dashboard\EmpresaController::registrar'); //accion
    });

    // Rutas para ubicaciones (regiones y comunas)
    $routes->group('ubicacion', function ($routes2) {
        $routes2->get('regiones', 'Dashboard\UbicacionController::getRegiones');
        $routes2->get('comunas/(:num)', 'Dashboard\UbicacionController::getComunasPorRegion/$1');
        $routes2->get('comunas', 'Dashboard\UbicacionController::getComunasPorRegion');
        $routes2->get('buscar-comunas', 'Dashboard\UbicacionController::buscarComunas');
        $routes2->get('comuna-info/(:num)', 'Dashboard\UbicacionController::getComunaInfo/$1');
        $routes2->get('comuna-info', 'Dashboard\UbicacionController::getComunaInfo');
        $routes2->post('validar', 'Dashboard\UbicacionController::validarUbicacion');
        $routes2->get('estadisticas', 'Dashboard\UbicacionController::getEstadisticas');
    });
});

$routes->post('/inicio-sesion', 'Dashboard\UsuarioController::inicio_sesion');
$routes->get('/logout', 'UsuarioController::logout');

$routes->group('', ['filter' => 'isLoggedIn'], function ($routes) {});

// Rutas públicas del sitio web
$routes->get('servicios', 'Web\ServicioController::index');
$routes->get('servicios/(:segment)', 'Web\ServicioController::detalle/$1');
$routes->get('servicios-categorias', 'Web\ServicioCategoriaController::index');
$routes->get('servicios-categorias/(:segment)', 'Web\ServicioCategoriaController::detalle/$1');
$routes->get('proyectos', 'Web\ProyectoController::index');
$routes->get('proyectos/(:segment)', 'Web\ProyectoController::detalle/$1');
$routes->get('galeria', 'Web\GaleriaController::index');
$routes->get('galeria-categorias', 'Web\GaleriaCategoriaController::index');
$routes->get('galeria-categorias/(:segment)', 'Web\GaleriaCategoriaController::detalle/$1');
$routes->get('nosotros', 'Web\NosotrosController::index');
$routes->get('contacto', 'Web\ContactoController::index');
$routes->post('contacto/enviar', 'Web\ContactoController::enviar');
$routes->get('gracias', 'Web\ContactoController::gracias');
$routes->post('newsletter/suscribir', 'Web\NewsletterController::suscribir');

//$routes->get('/getPerfil', 'PerfilController::getPerfil');
