<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Empresa;
use App\Models\ModuloDetalle;
use App\Traits\MaintainsFilters;

class EmpresaController extends BaseController
{
    use MaintainsFilters;

    public function lista()
    {
        // Redirigir directamente al registro/edición
        return redirect()->to(base_url('dashboard/empresa/registro'));
    }


    public function registro()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        // Obtener la empresa actual
        $empresa = new Empresa();
        $data['empresa'] = $empresa->getEmpresaActiva();

        return view('Modulos/empresa/registro', $data);
    }

    public function registrar()
    {
        $empresa = new Empresa();
        
        // Verificar si ya existe una empresa
        $empresaExistente = $empresa->getEmpresaActiva();
        
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'rut' => $this->request->getPost('rut'),
            'direccion' => $this->request->getPost('direccion'),
            'telefono' => $this->request->getPost('telefono'),
            'email' => $this->request->getPost('email'),
            'sitio_web' => $this->request->getPost('sitio_web'),
            'logo' => $this->request->getPost('logo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'mision' => $this->request->getPost('mision'),
            'vision' => $this->request->getPost('vision'),
            'valores' => $this->request->getPost('valores'),
            'estado' => 'A'
        ];

        if ($empresaExistente) {
            // Actualizar empresa existente
            if ($empresa->update($empresaExistente->id, $data)) {
                return redirect()->to(base_url('dashboard/empresa/registro'))->with('success', 'Datos de la empresa actualizados con éxito');
            } else {
                return redirect()->back()->withInput()->with('errors', $empresa->errors());
            }
        } else {
            // Crear nueva empresa
            if ($empresa->insert($data)) {
                return redirect()->to(base_url('dashboard/empresa/registro'))->with('success', 'Datos de la empresa registrados con éxito');
            } else {
                return redirect()->back()->withInput()->with('errors', $empresa->errors());
            }
        }
    }
}
