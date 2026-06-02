<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Usuario;
use App\Models\ModuloDetalle;
use App\Models\Perfil;
use App\Models\Empresa;

class UsuarioController extends BaseController
{

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
        $perfil = new Perfil();
        $data['perfiles'] = $perfil->getActivePerfil($this->poder);
        
        // Cargar empresas para el selector
        $empresa = new Empresa();
        $data['empresas'] = $empresa->where('estado', 'A')->orderBy('nombre', 'ASC')->findAll();
        
        echo view('Base/usuarios/registro', $data);
    }

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
        $perfil = new Perfil();
        $data['perfiles'] = $perfil->getActivePerfil($this->poder);
        echo view('Base/usuarios/lista', $data);
    }

    public function getUsuarios()
    {
        $usuarioModel = new Usuario();
        $draw = intval($this->request->getGet("draw"));
        $books = $usuarioModel->getData();
        $data = array();
        foreach ($books as $r) {
            $data[] = array(
                $r->nombre,
                $r->apellido,
                $r->correo,
                $r->telefono,
                $r->nombre_perfil,
                $r->fcreacion,
                '<a href="editar/' . $r->id . '" style="display:inline-block;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $usuarioModel->countAll(),
            "recordsFiltered" => 5,
            "data" => $data
        );

        return $this->response->setJSON($output);
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
        $perfil = new Perfil();
        // Filtrar perfiles según el poder del usuario actual
        $data['perfiles'] = $perfil->getActivePerfil($this->poder);
        
        // Cargar empresas para el selector
        $empresa = new Empresa();
        $data['empresas'] = $empresa->where('estado', 'A')->orderBy('nombre', 'ASC')->findAll();
        
        $usuario = new Usuario();
        $data['usuario'] = $usuario->select('usuario.* , perfil.nombre as nombre_perfil')
            ->join('perfil', 'usuario.perfil_id = perfil.id')
            ->where('usuario.id', $id)->first();
        echo view("Base/usuarios/editar", $data);
    }

    public function registrar()
    {
        if (!$this->validate('formUserRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $usuarioModel = new Usuario();

        $post = $this->request->getPost(['nombre', 'apellido', 'correo', 'clave', 'telefono', 'perfil', 'empresa']);
        $data = [
            'nombre' => $post['nombre'],
            'apellido' => $post['apellido'],
            'correo' => $post['correo'],
            'telefono' => $post['telefono'],
            'clave' => $usuarioModel->contrasenaHash($post['clave']),
            'perfil_id' => $post['perfil'],
            'empresa_id' => !empty($post['empresa']) ? $post['empresa'] : null,
            'estado' => 'A' // Estado inicial
        ];

        if ($usuarioModel->insert($data)) {
            return redirect()->to(base_url('dashboard/usuario/registro'))->with('success', 'Usuario registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el usuario');
        }
    }

    public function update()
    {

        if (!$this->validate('formUserEdit')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $usuarioModel = new Usuario();
        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');
        $apellido = $this->request->getPost('apellido');
        $correo = $this->request->getPost('correo');
        $telefono = $this->request->getPost('telefono');
        $perfil = $this->request->getPost('perfil');
        $empresa = $this->request->getPost('empresa');
        $estado = $this->request->getPost('estado');


        $updateData = [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'telefono' => $telefono,
            'perfil_id' => $perfil,
            'empresa_id' => !empty($empresa) ? $empresa : null,
            'estado' => $estado,
        ];

        if ((int) $perfil === Usuario::PERFIL_NUTRICIONISTA) {
            $updateData['titulo_profesional'] = $this->truncarCampo($this->request->getPost('titulo_profesional'), 150);
            $updateData['especialidad'] = $this->truncarCampo($this->request->getPost('especialidad'), 200);
            $updateData['carrera'] = $this->truncarCampo($this->request->getPost('carrera'), 200);
            $updateData['presentacion'] = $this->truncarCampo($this->request->getPost('presentacion'), 5000);
            $updateData['descripcion_profesional'] = $this->truncarCampo($this->request->getPost('descripcion_profesional'), 10000);
        }

        if ($usuarioModel->update($id, $updateData)) {
            return redirect()->to(base_url('dashboard/usuario/editar/' . $id))->with('success', 'Usuario editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el usuario');
        }
    }

    public function update_clave()
    {
        if (!$this->validate('formUserEditPassword')) {
            return redirect()->back()->withInput()->with('errorsPassword', $this->validator->getErrors());
        }
        $usuarioModel = new Usuario();
        $id = $this->request->getPost('id');
        $clave = $this->request->getPost('clave');
        if ($usuarioModel->update($id, [
            'clave' => $usuarioModel->contrasenaHash($clave), 
        ])) {
            return redirect()->to(base_url('dashboard/usuario/editar/' . $id))->with('successPassword', 'Clave editada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errorsPassword', 'Error al editar la clave');
        }
    }

    public function eliminar()
    {
        $usuarioModel = new Usuario();
        $id = $this->request->getPost('id');
        // Intenta eliminar el usuario
        if ($usuarioModel->delete($id)) {
            // Usuario eliminado con éxito
            return redirect()->to(base_url('dashboard/usuario/lista'))->with('success', 'Usuario eliminado con éxito.');
        } else {
            // Error al eliminar el usuario
            return redirect()->to(base_url('dashboard/usuario/lista'))->with('errors', 'No se pudo eliminar el usuario.');
        }
    }

    public function inicio_sesion()
    {
        if (!$this->validate('formUserLogin')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->listErrors());
        }


        $usuarioModel = new Usuario();

        $correo = $this->request->getPost('correo');
        $clave = $this->request->getPost('clave');

        //        $post = $this->request->getPost(['correo', 'clave']);

        $usuario = $usuarioModel->validateUser($correo, $clave);

        if ($usuario !== null) {
            $this->session->set('usuario', $usuario);
            if (es_usuario_portal_alumno($usuario)) {
                return redirect()->to(base_url('alumno/inicio'))->with('mensaje', 'Bienvenid@');
            }
            return redirect()->to(base_url('dashboard/menu'))->with('mensaje', 'Bienvenid@');
        }
        return redirect()->back()->withInput()->with('errors', 'El correo o contraseña son incorrectos.');
    }

    public function logout()
    {
        if ($this->session) {
            $this->session->destroy();
        }

        return redirect()->to(base_url('login'));
    }

    /**
     * Mantener la sesión activa (keepalive)
     * Esta ruta se llama periódicamente para renovar la sesión del usuario
     */
    private function truncarCampo($valor, int $max): ?string
    {
        if ($valor === null) {
            return null;
        }
        $s = trim((string) $valor);
        if ($s === '') {
            return null;
        }
        return mb_strlen($s) > $max ? mb_substr($s, 0, $max) : $s;
    }

    public function keepalive()
    {
        // Solo verificar que la sesión esté activa
        if (!session()->get('usuario')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Sesión no encontrada'
            ])->setStatusCode(401);
        }

        // Responder con éxito
        // El SessionFilter ya se encarga de renovar la sesión automáticamente
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Sesión renovada',
            'timestamp' => time()
        ]);
    }
}
