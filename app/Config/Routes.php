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
        
    });
});






$routes->post('/inicio-sesion', 'UsuarioController::inicio_sesion');
$routes->get('/logout', 'UsuarioController::logout');

$routes->group('', ['filter' => 'isLoggedIn'], function ($routes) {
});

//$routes->get('/getPerfil', 'PerfilController::getPerfil');
