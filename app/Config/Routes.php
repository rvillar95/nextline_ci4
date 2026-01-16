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
    
    // Ruta para renovar la sesión (keepalive)
    $routes->get('keepalive', 'Dashboard\UsuarioController::keepalive');

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
        $routes2->post('updateOrden', 'Dashboard\PerfilDetalleController::updateOrden'); // Edición inline
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
        $routes2->get('getClientesSelect', 'Dashboard\ProyectoController::getClientesSelect'); //get Data
        $routes2->post('registrar', 'Dashboard\ProyectoController::registrar'); //accion
        $routes2->post('update', 'Dashboard\ProyectoController::update'); //accion
        $routes2->post('eliminar', 'Dashboard\ProyectoController::eliminar'); //accion
        $routes2->post('setPortada', 'Dashboard\ProyectoController::setPortada'); //accion
        $routes2->post('eliminarImagen', 'Dashboard\ProyectoController::eliminarImagen'); //accion
    });

    $routes->group('leads', function ($routes2) {
        $routes2->get('lista', 'Dashboard\LeadController::lista');
        $routes2->get('getLeads', 'Dashboard\LeadController::getLeads');
        $routes2->get('getDetalle', 'Dashboard\LeadController::getDetalle');
        $routes2->get('getServicios', 'Dashboard\LeadController::getServicios');
        $routes2->post('cambiarEstado', 'Dashboard\LeadController::cambiarEstado');
        $routes2->post('eliminar', 'Dashboard\LeadController::eliminar');
    });

    $routes->group('testimonio', function ($routes2) {
        $routes2->get('registro', 'Dashboard\TestimonioController::registro'); //vista
        $routes2->get('editar/(:num)', 'Dashboard\TestimonioController::editar/$1'); //vista
        $routes2->get('lista', 'Dashboard\TestimonioController::lista'); //vista
        $routes2->get('getTestimonios', 'Dashboard\TestimonioController::getTestimonios'); //get Data
    });

    // ============================================
    // MÓDULOS DE NUTRICIONISTAS
    // ============================================
    
    $routes->group('paciente', function ($routes2) {
        $routes2->get('lista', 'Dashboard\PacienteController::lista');
        $routes2->get('registro', 'Dashboard\PacienteController::registro');
        $routes2->get('editar/(:num)', 'Dashboard\PacienteController::editar/$1');
        $routes2->get('detalle/(:num)', 'Dashboard\PacienteController::detalle/$1');
        $routes2->get('getPacientes', 'Dashboard\PacienteController::getPacientes');
        $routes2->get('getPacientesSelect', 'Dashboard\PacienteController::getPacientesSelect');
        $routes2->post('registrar', 'Dashboard\PacienteController::registrar');
        $routes2->post('update', 'Dashboard\PacienteController::update');
        $routes2->post('eliminar', 'Dashboard\PacienteController::eliminar');
    });

    $routes->group('documento', function ($routes2) {
        $routes2->get('lista', 'Dashboard\DocumentoController::lista');
        $routes2->get('registro', 'Dashboard\DocumentoController::registro');
        $routes2->get('editar/(:num)', 'Dashboard\DocumentoController::editar/$1');
        $routes2->get('detalle/(:num)', 'Dashboard\DocumentoController::detalle/$1');
        $routes2->get('getDocumentos', 'Dashboard\DocumentoController::getDocumentos');
        $routes2->post('registrar', 'Dashboard\DocumentoController::registrar');
        $routes2->post('update', 'Dashboard\DocumentoController::update');
        $routes2->post('eliminar', 'Dashboard\DocumentoController::eliminar');
        $routes2->post('enviar/(:num)', 'Dashboard\DocumentoController::enviar/$1');
    });

    $routes->group('historial', function ($routes2) {
        $routes2->get('lista', 'Dashboard\HistorialController::lista');
        $routes2->get('registro', 'Dashboard\HistorialController::registro');
        $routes2->get('editar/(:num)', 'Dashboard\HistorialController::editar/$1');
        $routes2->get('detalle/(:num)', 'Dashboard\HistorialController::detalle/$1');
        $routes2->get('comparar', 'Dashboard\HistorialController::comparar');
        $routes2->get('getHistorial', 'Dashboard\HistorialController::getHistorial');
        $routes2->get('getHistorialPaciente/(:num)', 'Dashboard\HistorialController::getHistorialPaciente/$1');
        $routes2->get('getHistorialesPaciente', 'Dashboard\HistorialController::getHistorialesPaciente');
        $routes2->post('compararHistoriales', 'Dashboard\HistorialController::compararHistoriales');
        $routes2->post('registrar', 'Dashboard\HistorialController::registrar');
        $routes2->post('update', 'Dashboard\HistorialController::update');
        $routes2->post('eliminar', 'Dashboard\HistorialController::eliminar');
        $routes2->post('eliminar', 'Dashboard\TestimonioController::eliminar'); //accion
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

    // Rutas para Listado de Materiales
    $routes->group('listado-material', function ($routes2) {
        $routes2->get('lista', 'Dashboard\ListadoMaterialController::lista'); // Vista lista
        $routes2->get('registro', 'Dashboard\ListadoMaterialController::registro'); // Vista registro
        $routes2->get('editar/(:num)', 'Dashboard\ListadoMaterialController::editar/$1'); // Vista editar
        $routes2->get('detalle/(:num)', 'Dashboard\ListadoMaterialController::detalle/$1'); // Vista detalle
        $routes2->get('getListadosMateriales', 'Dashboard\ListadoMaterialController::getListadosMateriales'); // Get data
        $routes2->get('getClientesSelect', 'Dashboard\ListadoMaterialController::getClientesSelect'); // Get clientes
        $routes2->get('getProyectosByCliente', 'Dashboard\ListadoMaterialController::getProyectosByCliente'); // Get proyectos
        $routes2->post('registrar', 'Dashboard\ListadoMaterialController::registrar'); // Accion registrar
        $routes2->post('update/(:num)', 'Dashboard\ListadoMaterialController::update/$1'); // Accion update
        $routes2->post('eliminar', 'Dashboard\ListadoMaterialController::eliminar'); // Accion eliminar
        $routes2->get('generarPDF/(:num)', 'Dashboard\ListadoMaterialController::generarPDF/$1'); // Generar PDF
    });

    // ============================================
    // MÓDULOS DE NUTRICIONISTAS
    // ============================================
    
    $routes->group('agenda', function ($routes2) {
        $routes2->get('lista', 'Dashboard\AgendaController::lista');
        $routes2->get('gestionar', 'Dashboard\AgendaController::gestionar');
        $routes2->get('calendario', 'Dashboard\AgendaController::calendario');
        $routes2->get('getAgendas', 'Dashboard\AgendaController::getAgendas');
        $routes2->get('getEventos', 'Dashboard\AgendaController::getEventos');
        $routes2->get('getDetalleCita', 'Dashboard\AgendaController::getDetalleCita');
        $routes2->get('getAgenda', 'Dashboard\AgendaController::getAgenda');
        $routes2->post('agendar', 'Dashboard\AgendaController::agendar');
        // Rutas para integración con calendario
        $routes2->get('calendario/connect', 'Dashboard\AgendaController::conectarCalendario');
        $routes2->get('calendario/verificar-token', 'Dashboard\AgendaController::verificarTokenCalendario');
        $routes2->post('crearHorarios', 'Dashboard\AgendaController::crearHorarios');
        $routes2->post('eliminarHorarios', 'Dashboard\AgendaController::eliminarHorarios');
        $routes2->post('actualizarModalidad', 'Dashboard\AgendaController::actualizarModalidad');
        $routes2->post('actualizarNotasNutricionista', 'Dashboard\AgendaController::actualizarNotasNutricionista');
        $routes2->get('consulta', 'Dashboard\AgendaController::consulta');
        $routes2->post('iniciarConsulta', 'Dashboard\AgendaController::iniciarConsulta');
        $routes2->post('terminarConsulta', 'Dashboard\AgendaController::terminarConsulta');
        $routes2->post('guardarNotasConsulta', 'Dashboard\AgendaController::guardarNotasConsulta');
        $routes2->get('estadisticas', 'Dashboard\AgendaController::estadisticas');
        $routes2->get('getConsultasProximas', 'Dashboard\AgendaController::getConsultasProximas');
        $routes2->get('getEstadisticas', 'Dashboard\AgendaController::getEstadisticas');
        $routes2->post('guardarMediciones', 'Dashboard\AgendaController::guardarMediciones');
        $routes2->post('confirmarCita', 'Dashboard\AgendaController::confirmarCita');
        $routes2->post('cancelarCita', 'Dashboard\AgendaController::cancelarCita');
    });

    $routes->group('agenda/cancelar-horas', function ($routes2) {
        $routes2->get('', 'Dashboard\CancelarHorasController::index');
        $routes2->post('obtener-citas', 'Dashboard\CancelarHorasController::obtenerCitas');
        $routes2->post('procesar', 'Dashboard\CancelarHorasController::procesarCancelacion');
    });

    $routes->group('pago', function ($routes2) {
        $routes2->get('lista', 'Dashboard\PagoController::lista');
        $routes2->get('registro', 'Dashboard\PagoController::registro');
        $routes2->get('editar/(:num)', 'Dashboard\PagoController::editar/$1');
        $routes2->get('getPagos', 'Dashboard\PagoController::getPagos');
        $routes2->post('registrar', 'Dashboard\PagoController::registrar');
        $routes2->post('update', 'Dashboard\PagoController::update');
        $routes2->post('procesar', 'Dashboard\PagoController::procesar');
    });

    $routes->group('configuracion', function ($routes2) {
        $routes2->get('', 'Dashboard\ConfiguracionController::index');
        $routes2->post('guardar', 'Dashboard\ConfiguracionController::guardar');
    });
});

$routes->post('/inicio-sesion', 'Dashboard\UsuarioController::inicio_sesion');
$routes->get('/logout', 'Dashboard\UsuarioController::logout');

$routes->group('', ['filter' => 'isLoggedIn'], function ($routes) {});

// Rutas públicas del sitio web
$routes->get('servicios', 'Web\ServicioController::index');
$routes->get('servicios/(:segment)', 'Web\ServicioController::detalle/$1');
$routes->get('servicios-categorias', 'Web\ServicioCategoriaController::index');
$routes->get('servicios-categorias/(:segment)', 'Web\ServicioCategoriaController::detalle/$1');
$routes->get('proyectos', 'Web\ProyectoController::index');
$routes->get('proyectos/(:segment)', 'Web\ProyectoController::detalle/$1');
$routes->get('galeria', 'Web\GaleriaController::index');
$routes->get('galeria/detalle/(:num)', 'Web\GaleriaController::detalle/$1');
$routes->get('galeria/categoria/(:segment)', 'Web\GaleriaController::categoria/$1');
$routes->get('galeria-categorias', 'Web\GaleriaCategoriaController::index');
$routes->get('galeria-categorias/(:segment)', 'Web\GaleriaCategoriaController::detalle/$1');
$routes->get('nosotros', 'Web\NosotrosController::index');
$routes->get('contacto', 'Web\ContactoController::index');
$routes->post('contacto/enviar', 'Web\ContactoController::enviar');
$routes->get('gracias', 'Web\ContactoController::gracias');
$routes->post('newsletter/suscribir', 'Web\NewsletterController::suscribir');

// Rutas públicas para confirmar/cancelar citas desde email
$routes->get('confirmar-cita', 'Dashboard\AgendaController::confirmarDesdeEmail');
$routes->get('cancelar-cita', 'Dashboard\AgendaController::cancelarDesdeEmail');
// Callback público de OAuth2 para calendario (no requiere autenticación porque Google lo llama directamente)
$routes->get('dashboard/agenda/calendario/callback', 'Dashboard\AgendaController::calendarCallback');

// Prueba de Email (solo para desarrollo local)
$routes->get('test-email', 'TestEmail::index');

// Webhooks de WhatsApp (públicos, sin autenticación)
$routes->get('whatsapp/webhook', 'WhatsAppWebhookController::verify');
$routes->post('whatsapp/webhook', 'WhatsAppWebhookController::webhook');

// Políticas
$routes->get('politica-privacidad', 'Web\PoliticasController::privacidad');
$routes->get('terminos-condiciones', 'Web\PoliticasController::terminos');

//$routes->get('/getPerfil', 'PerfilController::getPerfil');
