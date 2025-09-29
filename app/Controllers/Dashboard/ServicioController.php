<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\ModuloDetalle;
use App\Models\Servicio;
use App\Models\ServicioCategoria;
use App\Traits\MaintainsFilters;

class ServicioController extends BaseController
{
    use MaintainsFilters;
    public function registro()
    {
        $servicioCategoria = new ServicioCategoria();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        $data['categorias'] = $servicioCategoria->getCategoriasActivas();
        echo view('Modulos/servicio/registro', $data);
    }

    public function registrar()
    {
        $servicio = new Servicio();

        if (!$this->validate('formServiceRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $img = $this->request->getFile('foto');

        $post = $this->request->getPost([
            'nombre', 'categoria_id', 'descripcionCorta', 'descripcionLarga', 
            'caracteristicas', 'beneficios', 'tiempo_estimado', 'garantia',
            'precio_desde', 'precio_hasta', 'mostrar_precio', 'estado', 'orden', 'destacado'
        ]);
        
        $data = [
            'nombre' => $post['nombre'],
            'categoria_id' => $post['categoria_id'],
            'descripcionCorta' => $post['descripcionCorta'],
            'descripcionLarga' => $post['descripcionLarga'],
            'caracteristicas' => $post['caracteristicas'],
            'beneficios' => $post['beneficios'],
            'tiempo_estimado' => $post['tiempo_estimado'],
            'garantia' => $post['garantia'],
            'precio_desde' => $post['precio_desde'],
            'precio_hasta' => $post['precio_hasta'],
            'mostrar_precio' => $post['mostrar_precio'] ?? 'S',
            'estado' => $post['estado'],
            'orden' => $post['orden'] ?? 0,
            'destacado' => $post['destacado'] ?? 'N'
        ];
        
        // Generar slug automáticamente
        $servicioModel = new Servicio();
        $data['slug'] = $servicioModel->generarSlug($post['nombre']);
        
        // Manejar imagen
        if ($img->isValid() && ! $img->hasMoved()) {
            $fecha = date('dmY');
            $newName = $img->getRandomName();
            $uploadPath = ROOTPATH . 'lib/img/' . $fecha . '/';
            
            // Crear directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            if ($img->move($uploadPath, $newName)) {
                $data['foto'] = 'lib/img/' . $fecha . '/' . $newName;
            } else {
                return redirect()->back()->withInput()->with('errors', 'Error al subir la imagen');
            }
        } else {
            $data['foto'] = 'lib/images/default-service.svg'; // Imagen por defecto
        }

        if ($servicio->insert($data)) {
            return redirect()->to(base_url('dashboard/servicio/lista'))->with('success', 'Servicio registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el Servicio');
        }
    }

    public function getServicio()
    {
        $servicio = new Servicio();
        $draw = intval($this->request->getGet("draw"));
        $books = $servicio->getServicioAll();

        $data = array();
        foreach ($books as $r) {
            // Formatear precio
            $precio = '';
            if ($r->mostrar_precio === 'S') {
                if ($r->precio_desde && $r->precio_hasta) {
                    $precio = '$' . number_format($r->precio_desde, 0, ',', '.') . ' - $' . number_format($r->precio_hasta, 0, ',', '.');
                } elseif ($r->precio_desde) {
                    $precio = 'Desde $' . number_format($r->precio_desde, 0, ',', '.');
                } elseif ($r->precio_hasta) {
                    $precio = 'Hasta $' . number_format($r->precio_hasta, 0, ',', '.');
                }
            } else {
                $precio = 'Consultar precio';
            }

            // Manejar imagen
            $imagenHtml = '';
            if (!empty($r->foto) && file_exists(ROOTPATH . $r->foto)) {
                $imagenHtml = "<img src='".base_url($r->foto)."' style='max-width: 50px; height: 50px; object-fit: cover; border-radius: 5px;' />";
            } else {
                $imagenHtml = "<div style='width: 50px; height: 50px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 12px;'>Sin foto</div>";
            }

            $data[] = array(
                $r->nombre,
                $r->categoria_nombre ?? 'Sin categoría',
                $r->descripcionCorta,
                $precio,
                $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                $r->destacado == 'S' ? '<span class="badge badge-warning mb-2 me-4">⭐ Destacado</span>' : '<span class="badge badge-secondary mb-2 me-4">Normal</span>',
                $imagenHtml,
                '<a href="editar/' . $r->id . '" style="display:inline-block; margin-right: 5px;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $servicio->countAll(),
            "recordsFiltered" => $servicio->countAll(),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function editar($id)
    {
        $servicio = new Servicio();
        $servicioCategoria = new ServicioCategoria();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['servicio'] = $servicio->select('servicio.*')
            ->where('servicio.id', $id)->first();
        
        $data['categorias'] = $servicioCategoria->getCategoriasActivas();

        echo view("Modulos/servicio/editar", $data);
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
        echo view('Modulos/servicio/lista', $data);
    }

    public function eliminar()
    {
        $servicio = new Servicio();
        $id = $this->request->getPost('id');
        
        if ($servicio->delete($id)) {
            return $this->redirectWithFilters(base_url('dashboard/servicio/lista'), 'Servicio eliminado con éxito', 'success');
        } else {
            return $this->redirectWithFilters(base_url('dashboard/servicio/lista'), 'Error al eliminar el servicio', 'errors');
        }
    }

    public function update()
    {
        // Log para debug
        log_message('debug', 'Iniciando update de servicio');
        
        if (!$this->validate('formServiceEdit')) {
            log_message('error', 'Validación falló: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        log_message('debug', 'Validación pasó correctamente');
        
        $servicio = new Servicio();

        $id = $this->request->getPost('id');
        $post = $this->request->getPost([
            'nombre', 'categoria_id', 'descripcionCorta', 'descripcionLarga', 
            'caracteristicas', 'beneficios', 'tiempo_estimado', 'garantia',
            'precio_desde', 'precio_hasta', 'mostrar_precio', 'estado', 'orden', 'destacado'
        ]);
        
        log_message('debug', 'Datos recibidos: ' . json_encode($post));
        
        $arreglo = [
            'nombre' => $post['nombre'],
            'categoria_id' => $post['categoria_id'],
            'descripcionCorta' => $post['descripcionCorta'],
            'descripcionLarga' => $post['descripcionLarga'],
            'caracteristicas' => $post['caracteristicas'],
            'beneficios' => $post['beneficios'],
            'tiempo_estimado' => $post['tiempo_estimado'],
            'garantia' => $post['garantia'],
            'precio_desde' => $post['precio_desde'],
            'precio_hasta' => $post['precio_hasta'],
            'mostrar_precio' => $post['mostrar_precio'] ?? 'S',
            'estado' => $post['estado'],
            'orden' => $post['orden'] ?? 0,
            'destacado' => $post['destacado'] ?? 'N'
        ];
        
        // Generar slug automáticamente si cambió el nombre
        $servicioActual = $servicio->find($id);
        if ($servicioActual->nombre !== $post['nombre']) {
            $arreglo['slug'] = $servicio->generarSlug($post['nombre']);
        }
        
        $img = $this->request->getFile('foto');
        if ($img && $img->isValid() && ! $img->hasMoved()) {
            $fecha = date('dmY');
            $newName = $img->getRandomName();
            $uploadPath = ROOTPATH . 'lib/img/' . $fecha . '/';
            
            // Crear directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            if ($img->move($uploadPath, $newName)) {
                $arreglo['foto'] = 'lib/img/' . $fecha . '/' . $newName;
            } else {
                return redirect()->back()->withInput()->with('errors', 'Error al subir la nueva imagen');
            }
        }

        log_message('debug', 'Datos a actualizar: ' . json_encode($arreglo));

        if ($servicio->update($id, $arreglo)) {
            log_message('debug', 'Servicio actualizado correctamente');
            return redirect()->to(base_url('dashboard/servicio/editar/' . $id))->with('success', 'Servicio editado con éxito');
        } else {
            log_message('error', 'Error al actualizar servicio: ' . json_encode($servicio->errors()));
            return redirect()->back()->withInput()->with('errors', 'Error al editar el servicio');
        }
    }
}
