<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Proyecto;
use App\Models\Imagen;
use App\Models\Servicio;
use App\Models\ServicioCategoria;
use App\Models\Galeria;
use App\Models\GaleriaCategoria;

class ProyectoController extends BaseController
{
    public function index()
    {
        $proyectoModel = new Proyecto();
        $imagenModel = new Imagen();
        
        // Obtener filtros
        $tipo = $this->request->getGet('tipo');
        $busqueda = $this->request->getGet('busqueda');
        
        // Obtener proyectos públicos
        $proyectos = $proyectoModel->getProyectosPublicos();
        
        // Aplicar filtros
        if ($tipo && $tipo !== 'todos') {
            $proyectos = array_filter($proyectos, function($proyecto) use ($tipo) {
                return $proyecto->tipo_proyecto === $tipo;
            });
        }
        
        if ($busqueda) {
            $proyectos = array_filter($proyectos, function($proyecto) use ($busqueda) {
                return stripos($proyecto->nombre, $busqueda) !== false ||
                       stripos($proyecto->descripcion_corta, $busqueda) !== false ||
                       stripos($proyecto->ubicacion, $busqueda) !== false;
            });
        }
        
        // Obtener imágenes portada para cada proyecto
        $proyectoIds = array_column($proyectos, 'id');
        $imagenesPortada = [];
        if (!empty($proyectoIds)) {
            $imagenes = $imagenModel->getImagenesDestacadas('proyecto', $proyectoIds);
            foreach ($imagenes as $imagen) {
                $imagenesPortada[$imagen->entidad_id] = $imagen;
            }
        }
        
        // Asociar imágenes a proyectos
        foreach ($proyectos as $proyecto) {
            $proyecto->imagen_portada = $imagenesPortada[$proyecto->id] ?? null;
        }
        
        // Obtener estadísticas
        $estadisticas = $proyectoModel->getEstadisticasProyectos();
        
        // Tipos de proyecto para filtros (formato objeto para la vista)
        $categorias = [
            (object)['nombre' => 'Residencial', 'icono' => 'fas fa-home', 'slug' => 'residencial'],
            (object)['nombre' => 'Comercial', 'icono' => 'fas fa-building', 'slug' => 'comercial'],
            (object)['nombre' => 'Industrial', 'icono' => 'fas fa-industry', 'slug' => 'industrial'],
            (object)['nombre' => 'Institucional', 'icono' => 'fas fa-university', 'slug' => 'institucional'],
            (object)['nombre' => 'Otro', 'icono' => 'fas fa-folder', 'slug' => 'otro']
        ];
        
        $data = [
            'title' => 'Nuestros Proyectos - NextLine Constructor',
            'description' => 'Conoce nuestros proyectos de construcción residencial y comercial. Más de 15 años de experiencia construyendo sueños.',
            'keywords' => 'proyectos construcción, obras terminadas, constructor Linares, proyectos residenciales, proyectos comerciales',
            'proyectos' => $proyectos,
            'estadisticas' => $estadisticas,
            'categorias' => $categorias,
            'tipo_actual' => $tipo ?? '',
            'busqueda_actual' => $busqueda ?? ''
        ];
        
        return view('Web/proyectos', $data);
    }
    
    public function detalle($slug)
    {
        $proyectoModel = new Proyecto();
        $imagenModel = new Imagen();
        
        // Obtener proyecto por slug
        $proyecto = $proyectoModel->getProyectoPorSlug($slug);
        
        if (!$proyecto) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Proyecto no encontrado');
        }
        
        // Obtener todas las imágenes del proyecto
        $imagenes = $imagenModel->getImagenesPorEntidad('proyecto', $proyecto->id);
        
        // Obtener proyectos relacionados (mismo tipo)
        $proyectosRelacionados = $proyectoModel->getProyectosPorTipo($proyecto->tipo_proyecto, 3);
        
        // Remover el proyecto actual de los relacionados
        $proyectosRelacionados = array_filter($proyectosRelacionados, function($p) use ($proyecto) {
            return $p->id !== $proyecto->id;
        });
        
        // Obtener imágenes portada de proyectos relacionados
        if (!empty($proyectosRelacionados)) {
            $proyectoIds = array_column($proyectosRelacionados, 'id');
            $imagenesRelacionadas = $imagenModel->getImagenesDestacadas('proyecto', $proyectoIds);
            $imagenesPorProyecto = [];
            foreach ($imagenesRelacionadas as $imagen) {
                $imagenesPorProyecto[$imagen->entidad_id] = $imagen;
            }
            
            foreach ($proyectosRelacionados as $proyectoRel) {
                $proyectoRel->imagen_portada = $imagenesPorProyecto[$proyectoRel->id] ?? null;
            }
        }
        
        $data = [
            'title' => $proyecto->meta_titulo ?: $proyecto->nombre . ' - NextLine Constructor',
            'description' => $proyecto->meta_descripcion ?: $proyecto->descripcion_corta,
            'keywords' => $proyecto->meta_keywords ?: 'proyecto construcción, ' . $proyecto->tipo_proyecto . ', ' . $proyecto->ubicacion,
            'proyecto' => $proyecto,
            'imagenes' => $imagenes,
            'proyectos_relacionados' => array_slice($proyectosRelacionados, 0, 3)
        ];
        
        return view('Web/proyecto_detalle', $data);
    }
}
