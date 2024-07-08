<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ViewController::index');



$routes->get('/login', 'ViewController::login');

$routes->get('/layout', 'ViewController::layout');



$routes->group('dashboard', function ($routes) {
    $routes->get('registro/usuario', 'ViewController::registro');
    $routes->get('menu', 'ViewController::menu');
    $routes->get('layout_menu', 'ViewController::layout_menu');
   

    $routes->group('usuario', function ($routes2) {
        $routes2->get('inicio', 'UsuarioController::index');
        $routes2->get('detalle/(:num)', 'UsuarioController::detalle/$1');
        $routes2->post('registrar', 'UsuarioController::registrar');
        $routes2->post('editar', 'UsuarioController::editar');
        $routes2->post('editar/clave', 'UsuarioController::editar_clave');
        
    });
});






$routes->post('/inicio-sesion', 'UsuarioController::inicio_sesion');
$routes->get('/logout', 'UsuarioController::logout');

$routes->group('', ['filter' => 'isLoggedIn'], function ($routes) {
});

//$routes->get('/getPerfil', 'PerfilController::getPerfil');
