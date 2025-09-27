<?php

namespace App\Models;

use CodeIgniter\Model;

class Proyecto extends Model
{
    protected $table      = 'proyectos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nombre', 'slug', 'cliente', 'tipo_proyecto', 'ubicacion', 'direccion',
        'fecha_inicio', 'fecha_finalizacion', 'presupuesto', 'mostrar_presupuesto',
        'estado', 'descripcion_corta', 'descripcion_detallada', 'caracteristicas_tecnicas',
        'area_construida', 'materiales_principales', 'testimonio_cliente', 'nombre_cliente',
        'destacado', 'meta_titulo', 'meta_descripcion', 'meta_keywords', 'estado_publico'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    // Validación deshabilitada - se usa la del controlador
    protected $skipValidation = true;

    /**
     * Generar slug automáticamente
     */
    public function generarSlug($nombre)
    {
        $slug = strtolower(trim($nombre));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Verificar si el slug ya existe
        $originalSlug = $slug;
        $counter = 1;
        while ($this->where('slug', $slug)->first()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Obtener proyectos públicos (para web)
     */
    public function getProyectosPublicos($limit = null, $destacados = false)
    {
        $builder = $this->where('estado_publico', 'A')
                       ->orderBy('destacado', 'DESC')
                       ->orderBy('fecha_finalizacion', 'DESC');

        if ($destacados) {
            $builder->where('destacado', true);
        }

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Obtener proyecto por slug
     */
    public function getProyectoPorSlug($slug)
    {
        return $this->where('slug', $slug)
                   ->where('estado_publico', 'A')
                   ->first();
    }

    /**
     * Obtener proyectos por tipo
     */
    public function getProyectosPorTipo($tipo, $limit = null)
    {
        $builder = $this->where('tipo_proyecto', $tipo)
                       ->where('estado_publico', 'A')
                       ->orderBy('destacado', 'DESC')
                       ->orderBy('fecha_finalizacion', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Obtener proyectos por estado
     */
    public function getProyectosPorEstado($estado)
    {
        return $this->where('estado', $estado)
                   ->orderBy('fecha_inicio', 'DESC')
                   ->findAll();
    }

    /**
     * Obtener estadísticas de proyectos
     */
    public function getEstadisticasProyectos()
    {
        $stats = [];
        
        $stats['total'] = $this->countAllResults();
        $stats['completados'] = $this->where('estado', 'completado')->countAllResults();
        $stats['en_progreso'] = $this->where('estado', 'en_progreso')->countAllResults();
        $stats['en_pausa'] = $this->where('estado', 'en_pausa')->countAllResults();
        $stats['destacados'] = $this->where('destacado', true)->countAllResults();
        
        return $stats;
    }

    /**
     * Obtener proyectos con imágenes
     */
    public function getProyectosConImagenes($limit = null)
    {
        $proyectos = $this->getProyectosPublicos($limit);
        
        if (empty($proyectos)) {
            return [];
        }

        $imagenModel = new Imagen();
        $proyectoIds = array_column($proyectos, 'id');
        
        // Obtener todas las imágenes de estos proyectos
        $imagenes = $imagenModel->getImagenesDestacadas('proyecto', $proyectoIds);
        
        // Crear un array indexado por proyecto_id
        $imagenesPorProyecto = [];
        foreach ($imagenes as $imagen) {
            $imagenesPorProyecto[$imagen->entidad_id] = $imagen;
        }

        // Asociar imágenes a proyectos
        foreach ($proyectos as $proyecto) {
            $proyecto->imagen_portada = $imagenesPorProyecto[$proyecto->id] ?? null;
        }

        return $proyectos;
    }

    /**
     * Buscar proyectos
     */
    public function buscarProyectos($termino, $tipo = null, $estado = null)
    {
        $builder = $this->groupStart()
                       ->like('nombre', $termino)
                       ->orLike('descripcion_corta', $termino)
                       ->orLike('descripcion_detallada', $termino)
                       ->orLike('ubicacion', $termino)
                       ->groupEnd()
                       ->where('estado_publico', 'A')
                       ->orderBy('destacado', 'DESC')
                       ->orderBy('fecha_finalizacion', 'DESC');

        if ($tipo) {
            $builder->where('tipo_proyecto', $tipo);
        }

        if ($estado) {
            $builder->where('estado', $estado);
        }

        return $builder->findAll();
    }
}
