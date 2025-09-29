<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Cliente;
use App\Models\ModuloDetalle;
use App\Traits\MaintainsFilters;

class ClienteController extends BaseController
{
    use MaintainsFilters;

    public function lista()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        return view('Modulos/cliente/lista', $data);
    }

    public function getClientes()
    {
        // Verificar autenticación
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $cliente = new Cliente();
        $draw = intval($this->request->getGet("draw"));
        
        // Obtener filtros
        $tipo_cliente = $this->request->getGet('tipo_cliente');
        $estado = $this->request->getGet('estado');
        $busqueda = $this->request->getGet('busqueda');
        
        // Construir consulta
        $query = $cliente;
        
        // Filtro de estado
        if (!empty($estado)) {
            $query = $query->where('estado', $estado);
        } else {
            // Por defecto mostrar solo activos si no se especifica estado
            $query = $query->where('estado', 'A');
        }
        
        if (!empty($tipo_cliente)) {
            $query->where('tipo_cliente', $tipo_cliente);
        }
        
        if (!empty($busqueda)) {
            $query->groupStart()
                  ->like('nombre_razon_social', $busqueda)
                  ->orLike('contacto_nombre', $busqueda)
                  ->orLike('rut_dni', $busqueda)
                  ->orLike('email', $busqueda)
                  ->groupEnd();
        }
        
        $rows = $query->orderBy('tipo_cliente', 'ASC')
                     ->orderBy('nombre_razon_social', 'ASC')
                     ->findAll();

        $data = array();
        foreach ($rows as $r) {
            $tipoBadge = match ($r->tipo_cliente) {
                'particular' => '<span class="badge badge-info">Particular</span>',
                'empresa' => '<span class="badge badge-success">Empresa</span>',
                'organizacion' => '<span class="badge badge-warning">Organización</span>',
                default => '<span class="badge badge-secondary">N/A</span>',
            };

            $contacto = $r->contacto_nombre ? "Contacto: {$r->contacto_nombre}" : '';
            if ($r->telefono) {
                $contacto .= $contacto ? " | Tel: {$r->telefono}" : "Tel: {$r->telefono}";
            }
            if ($r->email) {
                $contacto .= $contacto ? " | Email: {$r->email}" : "Email: {$r->email}";
            }

            $estadoBadge = $r->estado == 'A' 
                ? '<span class="badge badge-success">Activo</span>' 
                : '<span class="badge badge-danger">Inactivo</span>';

            $botones = '';
            if ($r->estado == 'A') {
                // Cliente activo - mostrar botones normales
                $botones = '<button class="btn btn-sm btn-outline-primary" onclick="editarCliente(' . $r->id . ')">Editar</button> ' .
                          '<button class="btn btn-sm btn-outline-info" onclick="verDetalleCliente(' . $r->id . ')">Ver</button> ' .
                          '<button class="btn btn-sm btn-outline-danger" onclick="eliminarCliente(' . $r->id . ')">Eliminar</button>';
            } else {
                // Cliente inactivo - mostrar botón de activar
                $botones = '<button class="btn btn-sm btn-outline-success" onclick="activarCliente(' . $r->id . ')">Activar</button> ' .
                          '<button class="btn btn-sm btn-outline-info" onclick="verDetalleCliente(' . $r->id . ')">Ver</button>';
            }

            $data[] = array(
                esc($r->nombre_razon_social),
                $tipoBadge,
                $estadoBadge,
                esc($contacto),
                esc($r->direccion ?? ''),
                esc($r->comuna ?? ''),
                $botones
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $cliente->where('estado', 'A')->countAllResults(),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        return $this->response->setJSON($output);
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

        return view('Modulos/cliente/registro', $data);
    }

    public function registrar()
    {
        $cliente = new Cliente();

        // Debug: Log de los datos recibidos
        log_message('debug', 'Datos POST recibidos: ' . json_encode($this->request->getPost()));
        
        if (!$this->validate('formClienteRegister')) {
            // Debug: Log de errores de validación
            log_message('debug', 'Errores de validación: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'tipo_cliente', 'nombre_razon_social', 'rut_dni', 'contacto_nombre', 'contacto_cargo',
            'telefono', 'email', 'direccion', 'region_id', 'comuna_id', 'sitio_web', 'observaciones'
        ]);

        $data = [
            'tipo_cliente' => $post['tipo_cliente'],
            'nombre_razon_social' => $post['nombre_razon_social'],
            'rut_dni' => $post['rut_dni'] ?? null,
            'contacto_nombre' => $post['contacto_nombre'] ?? null,
            'contacto_cargo' => $post['contacto_cargo'] ?? null,
            'telefono' => $post['telefono'] ?? null,
            'email' => $post['email'] ?? null,
            'direccion' => $post['direccion'] ?? null,
            'region_id' => $post['region_id'] ?? null,
            'comuna_id' => $post['comuna_id'] ?? null,
            'sitio_web' => $post['sitio_web'] ?? null,
            'observaciones' => $post['observaciones'] ?? null,
            'estado' => 'A'
        ];

        // Debug: Log de los datos a insertar
        log_message('debug', 'Datos a insertar: ' . json_encode($data));

        if ($cliente->insert($data)) {
            log_message('debug', 'Cliente insertado exitosamente');
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('success', 'Cliente registrado con éxito');
        } else {
            // Debug: Log de errores del modelo
            log_message('debug', 'Errores del modelo: ' . json_encode($cliente->errors()));
            return redirect()->back()->withInput()->with('errors', $cliente->errors());
        }
    }

    public function editar($id)
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $cliente = new Cliente();
        $data['cliente'] = $cliente->find($id);
        
        if (!$data['cliente']) {
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('error', 'Cliente no encontrado');
        }

        return view('Modulos/cliente/editar', $data);
    }

    public function update()
    {
        $cliente = new Cliente();
        $id = $this->request->getPost('id');

        if (!$this->validate('formClienteEdit')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'tipo_cliente', 'nombre_razon_social', 'rut_dni', 'contacto_nombre', 'contacto_cargo',
            'telefono', 'email', 'direccion', 'comuna', 'region', 'sitio_web', 'observaciones'
        ]);

        $data = [
            'tipo_cliente' => $post['tipo_cliente'],
            'nombre_razon_social' => $post['nombre_razon_social'],
            'rut_dni' => $post['rut_dni'] ?? null,
            'contacto_nombre' => $post['contacto_nombre'] ?? null,
            'contacto_cargo' => $post['contacto_cargo'] ?? null,
            'telefono' => $post['telefono'] ?? null,
            'email' => $post['email'] ?? null,
            'direccion' => $post['direccion'] ?? null,
            'comuna' => $post['comuna'] ?? null,
            'region' => $post['region'] ?? null,
            'sitio_web' => $post['sitio_web'] ?? null,
            'observaciones' => $post['observaciones'] ?? null
        ];

        if ($cliente->update($id, $data)) {
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('success', 'Cliente actualizado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al actualizar el cliente');
        }
    }

    public function activar()
    {
        $id = $this->request->getPost('id');
        
        if (!$id) {
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('error', 'ID de cliente requerido');
        }
        
        $cliente = new Cliente();
        
        // Verificar que el cliente existe
        $clienteData = $cliente->find($id);
        if (!$clienteData) {
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('error', 'Cliente no encontrado');
        }
        
        // Activar el cliente
        if ($cliente->update($id, ['estado' => 'A'])) {
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('success', 'Cliente activado exitosamente');
        } else {
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('error', 'Error al activar el cliente');
        }
    }

    public function eliminar($id = null)
    {
        // Si no viene por URL, intentar obtenerlo por POST
        if (!$id) {
            $id = $this->request->getPost('id');
        }
        
        if (!$id) {
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('error', 'ID de cliente requerido');
        }
        
        $cliente = new Cliente();
        
        // Verificar si tiene cotizaciones asociadas
        $db = \Config\Database::connect();
        $cotizaciones = $db->table('cotizaciones')->where('cliente_id', $id)->countAllResults();
        
        if ($cotizaciones > 0) {
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('error', 'No se puede eliminar el cliente porque tiene cotizaciones asociadas');
        }
        
        if ($cliente->update($id, ['estado' => 'I'])) {
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('success', 'Cliente eliminado con éxito');
        } else {
            return redirect()->back()->with('error', 'Error al eliminar el cliente');
        }
    }

    public function detalle($id)
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $cliente = new Cliente();
        $data['cliente'] = $cliente->find($id);
        
        if (!$data['cliente']) {
            return redirect()->to(base_url('dashboard/cliente/lista'))->with('error', 'Cliente no encontrado');
        }

        // Obtener cotizaciones del cliente
        $cotizacionModel = new \App\Models\Cotizacion();
        $data['cotizaciones'] = $cotizacionModel->where('cliente_id', $id)
                                              ->orderBy('fecha_cotizacion', 'DESC')
                                              ->findAll();

        return view('Modulos/cliente/detalle', $data);
    }

    public function getClientesSelect()
    {
        // Verificar autenticación
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $cliente = new Cliente();
        $clientes = $cliente->getClientesActivos();

        $data = [];
        foreach ($clientes as $c) {
            $data[] = [
                'id' => $c->id,
                'text' => $c->getDisplayName($c),
                'tipo' => $c->tipo_cliente,
                'contacto' => $c->getContactoCompleto($c)
            ];
        }

        return $this->response->setJSON($data);
    }
}
