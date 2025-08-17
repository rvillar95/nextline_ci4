<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;


use App\Models\ModuloDetalle;
use App\Models\Perfil;
use App\Models\Modulo;

class SessionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ruta = $_SERVER['PATH_INFO'];

        if (session()->get('usuario') == null) {
            return redirect()->to(route_to('login'));
        }

        $perfil = new Perfil();
        $query = $perfil->getNombresPerfil();
        $perfiles = array_column($query, 'nombre');



        if (!in_array(session()->get('usuario')['perfil_nombre'], $perfiles)) {
            return redirect()->back()->withInput()->with('errors', 'No tiene permisos para esta funcionalidad');
        }

        $menuTotal = array();

        $menu = array();
        $moduloModel = new ModuloDetalle();

        //$modulos = $moduloModel->getPermisos2(session()->get('usuario')['perfil_id']);

        $data['menu'] = $moduloModel->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $moduloModel->getSubMenu($entity['id']);
            array_push($menuTotal, array("rutas" => $entity['ruta']));
            foreach ($submenu as $sub) {
                array_push($menuTotal, array("rutas" => $entity['ruta'] . $sub['ruta']));
            }
        }

        function rutaCoincide($rutaActual, $rutaBD)
        {
            $rutaBD = rtrim($rutaBD, '/');
            $rutaBD = trim($rutaBD);
            $rutaRegex = preg_replace('/\(:num\)/', '[0-9]+', $rutaBD) . '(/[0-9]+)?';
            //echo "++++++++++".$rutaRegex."  vs  ".$rutaActual."<br>";
            return preg_match('#^' . $rutaRegex . '$#', $rutaActual);
        }

        //echo "<pre>";
        //print_r($menuTotal);
        //echo "</pre>";
        $accesoPermitido = false;
        foreach ($menuTotal as $modulo) {
            //echo "Ruta:  ".$ruta."   vs   ".$modulo['rutas']."<br>";
            //echo session()->get('usuario')['perfil_nombre']."<br>";
            //echo "Es => ".rutaCoincide($ruta, $modulo['rutas'])."<br>";
            //print_r($perfiles)."<br>";
            if (rutaCoincide($ruta, $modulo['rutas']) && in_array(session()->get('usuario')['perfil_nombre'], $perfiles)) {
                $accesoPermitido = true;
                //echo "paso";
                break;
            }
        }
        //exit();


        if (!$accesoPermitido) {
            return redirect()->to(route_to('login'))->withInput()->with('errors', 'No tiene permisos para esta funcionalidada');
        }
        $modulo = new Modulo();

        $rutaCorta = "/" . explode("/", $ruta)[1] . "/" . explode("/", $ruta)[2];

        $respuesta = $modulo->getModuloLike($rutaCorta);
        $accesoPermitido = false;
        foreach ($data['menu'] as $entity) {
            $submenu = $moduloModel->getSubMenu($entity['id']);
            foreach ($submenu as $submenu) {

                if ($submenu['modulo_id'] == $respuesta[0]["id"]) {
                    if ($rutaCorta . $submenu["ruta"] == $ruta || rutaCoincide($ruta, $rutaCorta . $submenu["ruta"])) {
                        $acciones = explode(',', $submenu['accion']);
                        foreach ($acciones as $accion) {
                            if ($accion == "ver" && $entity['ver'] == 1) {
                                $accesoPermitido = true;
                                break;
                            }
                            if ($accion == "registrar" && $entity['registrar'] == 1) {
                                $accesoPermitido = true;
                                break;
                            }
                            if ($accion == "editar" && $entity['editar'] == 1) {
                                $accesoPermitido = true;
                                break;
                            }
                            if ($accion == "eliminar" && $entity['eliminar'] == 1) {
                                $accesoPermitido = true;
                                break;
                            }
                        }
                    }
                }
            }
        }
        if (!$accesoPermitido) {
            return redirect()->back()->withInput()->with('errors', 'No tiene permisos para esta funcionalidad');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
