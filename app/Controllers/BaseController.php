<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['form'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    protected $poder;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        
        // Asegurar que todas las respuestas HTML tengan charset UTF-8
        // Esto corrige el problema de tildes y caracteres especiales
        if (!$response->hasHeader('Content-Type')) {
            $response->setHeader('Content-Type', 'text/html; charset=UTF-8');
        } else {
            $contentType = $response->getHeaderLine('Content-Type');
            if (strpos($contentType, 'text/html') !== false && strpos($contentType, 'charset') === false) {
                $response->setHeader('Content-Type', 'text/html; charset=UTF-8');
            }
        }
        
        // Log temporal para debugging de cancelar-cita
        $uri = $request->getUri();
        $path = $uri->getPath();
        if (strpos($path, 'cancelar-cita') !== false || strpos($path, 'confirmar-cita') !== false) {
            error_log('BaseController::initController - Ruta: ' . $path);
            error_log('BaseController::initController - URI completa: ' . (string)$uri);
            log_message('info', 'BaseController::initController - Ruta: ' . $path);
            log_message('info', 'BaseController::initController - URI completa: ' . (string)$uri);
        }
        
        $this->session = \Config\Services::session();
        $this->poder = (int) (session('usuario')['poder'] ?? 0);
    }
}
