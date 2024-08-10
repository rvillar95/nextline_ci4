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
        $perfil = new Perfil();
        $query = $perfil->getNombresPerfil();
        $perfiles = array_column($query, 'nombre');

        if (session()->get('usuario') == null) {
            return redirect()->to(route_to('login'));
        }

        if (!in_array(session()->get('usuario')['perfil_nombre'], $perfiles)) {
            return redirect()->to(route_to('login'))->withInput()->with('errors', 'No tiene permisos para esta funcionalidad');
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
            // Remover el último segmento variable si existe en la ruta de la base de datos
            $rutaBD = rtrim($rutaBD, '/');
            $rutaRegex = preg_replace('/\(:num\)/', '[0-9]+', $rutaBD) . '(/[0-9]+)?';
            return preg_match('#^' . $rutaRegex . '$#', $rutaActual);
        }



        $accesoPermitido = false;
        foreach ($menuTotal as $modulo) {
            //echo $ruta ." v/s ".$modulo['rutas']. "<br>";
            if (rutaCoincide($ruta, $modulo['rutas']) && in_array(session()->get('usuario')['perfil_nombre'], $perfiles)) {
                //echo $ruta . " v/s ". $modulo['rutas'];
                $accesoPermitido = true;
                break;
            }
        }

        if (!$accesoPermitido) {
            return redirect()->to(route_to('login'))->withInput()->with('errors', 'No tiene permisos para esta funcionalidad');
        }
        $modulo = new Modulo();

        $rutaCorta = "/" . explode("/", $ruta)[1] . "/" . explode("/", $ruta)[2];

        $respuesta = $modulo->getModuloLike($rutaCorta);
        /*         echo "<pre>";
        print_r($respuesta);
        echo "</pre>"; */
        $accesoPermitido = false;
        foreach ($data['menu'] as $entity) {
            $submenu = $moduloModel->getSubMenu($entity['id']);
            //array_push($menu, array("menu" => $entity, "submenu" => $submenu));

            foreach ($submenu as $submenu) {

                if ($submenu['modulo_id'] == $respuesta[0]["id"]) {

                    /*                     echo "<pre>";
                    print_r($submenu);
                    echo "</pre>";
                    echo $rutaCorta . $submenu["ruta"] . " v/s " . $ruta . "<br>"; */
                    if ($rutaCorta . $submenu["ruta"] == $ruta || rutaCoincide($ruta, $rutaCorta . $submenu["ruta"])) {
                        /* echo "Cae aca con " . $rutaCorta . $submenu["ruta"]; */
                        //$accesoPermitido = true;
                        //break;

                        $acciones = explode(',', $submenu['accion']);


                        /*                         echo "<pre>";
                        print_r($acciones);
                        echo "</pre>"; */
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

        /*         echo $accesoPermitido ? "True" : "False"; */



        /*         echo "<pre>";
        print_r($menuTotal);
        echo "</pre>";
        var_dump($accesoPermitido);
        exit();  */

        /* 
        foreach ($permisos as $permiso) {
            if (rutaCoincide($ruta, $permiso['ruta']) && in_array(session()->get('usuario')['perfil_nombre'], $perfiles)) {
                $accesoPermitido = true;
                break;
            }
        } */
        //exit();
        //$accesoPermitido = true;
        if (!$accesoPermitido) {
            return redirect()->to(route_to('login'))->withInput()->with('errors', 'No tiene permisos para esta funcionalidad');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
