<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ViewController::index');

$routes->get('/login', 'ViewController::login');


$routes->post('/inicio-sesion', 'UsuarioController::inicio_sesion');
$routes->post('/registro-usuario', 'UsuarioController::registro_usuario');

$routes->get('/registro_publico', 'ViewController::registro_publico');

//$routes->get('/getPerfil', 'PerfilController::getPerfil');








