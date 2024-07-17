<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;


use App\Models\ModuloDetalle;
use App\Models\Perfil;
use App\Models\Permiso;

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
        $moduloModel = new ModuloDetalle();

        //$modulos = $moduloModel->getPermisos2(session()->get('usuario')['perfil_id']);

        $data['menu'] = $moduloModel->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $moduloModel->getSubMenu($entity['id']);
            array_push($menuTotal,array("rutas" => $entity['ruta']));
            foreach ($submenu as $sub) {
                array_push($menuTotal,array("rutas" => $entity['ruta'].$sub['ruta']));    
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
                $accesoPermitido = true;
                break;
            }
        }

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

        if (!$accesoPermitido) {
            return redirect()->to(route_to('login'))->withInput()->with('errors', 'No tiene permisos para esta funcionalidad');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
