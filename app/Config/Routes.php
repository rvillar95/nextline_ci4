<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ViewController::index');

$routes->get('/test','UsuarioController::test');

$routes->get('/login', 'ViewController::login');

$routes->get('/layout', 'ViewController::layout');



$routes->group('dashboard', function ($routes) {
    $routes->get('inicio', 'ViewController::index');

    $routes->get('registro/usuario', 'ViewController::registro');
    $routes->get('menu', 'ViewController::menu');
    $routes->get('layout_menu', 'ViewController::layout_menu');
   

    $routes->group('usuario', function ($routes2) {
        $routes2->get('', 'UsuarioController::inicio');
        $routes2->get('registro', 'UsuarioController::registro');
        $routes2->get('editar/(:num)', 'UsuarioController::editar/$1');
        $routes2->get('lista', 'UsuarioController::lista');
        $routes2->get('getUsuarios', 'UsuarioController::getUsuarios');
        $routes2->post('registrar', 'UsuarioController::registrar');
        $routes2->post('update', 'UsuarioController::update');
        $routes2->post('update/clave', 'UsuarioController::update_clave');
        $routes2->post('eliminar', 'UsuarioController::eliminar');
    });

    $routes->group('perfil', function ($routes2) {
        $routes2->get('registro', 'PerfilController::registro');
        $routes2->get('editar/(:num)', 'PerfilController::editar/$1');
        $routes2->get('lista', 'PerfilController::lista');
        $routes2->get('getPerfiles', 'PerfilController::getPerfiles');
        $routes2->post('registrar', 'PerfilController::registrar');
        $routes2->post('update', 'PerfilController::update');
        $routes2->post('eliminar', 'PerfilController::eliminar');
    });

    $routes->group('modulo', function ($routes2) {
        $routes2->get('registro', 'ModuloController::registro');
        $routes2->get('editar/(:num)', 'ModuloController::editar/$1');
        $routes2->get('lista', 'ModuloController::lista');
        $routes2->get('getModulo', 'ModuloController::getModulo');
        $routes2->post('registrar', 'ModuloController::registrar');
        $routes2->post('update', 'ModuloController::update');
        $routes2->post('update/clave', 'ModuloController::update_clave');
        $routes2->post('eliminar', 'ModuloController::eliminar');
    });

    $routes->group('perfil-detalle', function ($routes2) {
        $routes2->get('registro', 'PerfilDetalleController::registro');
        $routes2->get('editar/(:num)', 'PerfilDetalleController::editar/$1');
        $routes2->get('lista', 'PerfilDetalleController::lista');
        $routes2->get('getPerfilDetalle', 'PerfilDetalleController::getPerfilDetalle');
        $routes2->post('registrar', 'PerfilDetalleController::registrar');
        $routes2->post('update', 'PerfilDetalleController::update');
        $routes2->post('eliminar', 'PerfilDetalleController::eliminar');
    });

    $routes->group('modulo-detalle', function ($routes2) {
        $routes2->get('registro', 'ModuloDetalleController::registro');
        $routes2->get('editar/(:num)', 'ModuloDetalleController::editar/$1');
        $routes2->get('lista', 'ModuloDetalleController::lista');
        $routes2->get('getModuloDetalle', 'ModuloDetalleController::getModuloDetalle');
        $routes2->post('registrar', 'ModuloDetalleController::registrar');
        $routes2->post('update', 'ModuloDetalleController::update');
        $routes2->post('eliminar', 'ModuloDetalleController::eliminar');
    });

    $routes->group('servicio', function ($routes2) {
        $routes2->get('registro', 'ServicioController::registro');//vista
        $routes2->get('editar/(:num)', 'ServicioController::editar/$1');//vista
        $routes2->get('lista', 'ServicioController::lista');//vista
        $routes2->get('getServicio', 'ServicioController::getServicio');//get Data
        $routes2->post('registrar', 'ServicioController::registrar');//accion
        $routes2->post('update', 'ServicioController::update');//accion
        $routes2->post('eliminar', 'ServicioController::eliminar');//accion
    });

    $routes->group('galeria', function ($routes2) {
        $routes2->get('registro', 'GaleriaController::registro');//vista
        $routes2->get('editar/(:num)', 'GaleriaController::editar/$1');//vista
        $routes2->get('lista', 'GaleriaController::lista');//vista
        $routes2->get('getGaleria', 'GaleriaController::getGaleria');//get Data
        $routes2->post('registrar', 'GaleriaController::registrar');//accion
        $routes2->post('update', 'GaleriaController::update');//accion
        $routes2->post('eliminar', 'GaleriaController::eliminar');//accion
    });
});






$routes->post('/inicio-sesion', 'UsuarioController::inicio_sesion');
$routes->get('/logout', 'UsuarioController::logout');

$routes->group('', ['filter' => 'isLoggedIn'], function ($routes) {
});

//$routes->get('/getPerfil', 'PerfilController::getPerfil');
