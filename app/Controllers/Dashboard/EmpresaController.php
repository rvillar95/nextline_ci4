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
        
        // Pasar el poder del usuario a la vista
        $data['poder_usuario'] = $this->poder;
        
        // Si es Super Admin (poder=3), cargar lista de paquetes
        if ($this->poder >= 3) {
            try {
                $db = \Config\Database::connect();
                $data['paquetes'] = $db->table('paquetes')
                    ->where('activo', 'A')
                    ->orderBy('orden', 'ASC')
                    ->get()
                    ->getResultArray();
            } catch (\Exception $e) {
                // Si la tabla no existe aún, dejar array vacío
                log_message('warning', 'Tabla paquetes no encontrada: ' . $e->getMessage());
                $data['paquetes'] = [];
            }
        } else {
            $data['paquetes'] = [];
        }

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
        
        // Solo Super Admin (poder>=3) puede modificar el paquete
        if ($this->poder >= 3 && $this->request->getPost('paquete_id')) {
            $data['paquete_id'] = $this->request->getPost('paquete_id');
        }

        if ($empresaExistente) {
            // Actualizar empresa existente
            if ($empresa->update($empresaExistente->id, $data)) {
                return redirect()->to(base_url('dashboard/empresa/registro'))->with('success', 'Datos de la empresa actualizados con éxito');
            } else {
                return redirect()->back()->withInput()->with('errors', $empresa->errors());
            }
        } else {
            // Crear nueva empresa (por defecto paquete_id = 1 si no se especifica)
            if (!isset($data['paquete_id'])) {
                $data['paquete_id'] = 1; // Presencia por defecto
            }
            
            if ($empresa->insert($data)) {
                return redirect()->to(base_url('dashboard/empresa/registro'))->with('success', 'Datos de la empresa registrados con éxito');
            } else {
                return redirect()->back()->withInput()->with('errors', $empresa->errors());
            }
        }
    }
}
