<?php

namespace App\Models;

use CodeIgniter\Model;

class Imagen extends Model
{
    protected $table      = 'imagenes';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nombre_archivo', 'ruta', 'tipo', 'entidad_id', 'es_portada', 
        'orden', 'descripcion', 'estado'
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
     * Obtener todas las imágenes de una entidad específica
     */
    public function getImagenesPorEntidad($tipo, $entidadId, $soloActivas = true)
    {
        $builder = $this->where('tipo', $tipo)
                       ->where('entidad_id', $entidadId)
                       ->orderBy('orden', 'ASC')
                       ->orderBy('fcreacion', 'ASC');

        if ($soloActivas) {
            $builder->where('estado', 'A');
        }

        return $builder->findAll();
    }

    /**
     * Obtener imagen portada de una entidad
     */
    public function getImagenPortada($tipo, $entidadId)
    {
        return $this->where('tipo', $tipo)
                   ->where('entidad_id', $entidadId)
                   ->where('es_portada', true)
                   ->where('estado', 'A')
                   ->first();
    }

    /**
     * Establecer una imagen como portada (desmarcar otras)
     */
    public function setImagenPortada($tipo, $entidadId, $imagenId)
    {
        // Desmarcar todas las portadas de esta entidad
        $this->where('tipo', $tipo)
             ->where('entidad_id', $entidadId)
             ->set('es_portada', false)
             ->update();

        // Marcar la nueva portada
        return $this->update($imagenId, ['es_portada' => true]);
    }

    /**
     * Obtener siguiente orden para una entidad
     */
    public function getSiguienteOrden($tipo, $entidadId)
    {
        $result = $this->selectMax('orden')
                      ->where('tipo', $tipo)
                      ->where('entidad_id', $entidadId)
                      ->first();

        return ($result->orden ?? 0) + 1;
    }

    /**
     * Reordenar imágenes de una entidad
     */
    public function reordenarImagenes($tipo, $entidadId, $ordenes)
    {
        foreach ($ordenes as $imagenId => $nuevoOrden) {
            $this->update($imagenId, ['orden' => $nuevoOrden]);
        }
        return true;
    }

    /**
     * Eliminar todas las imágenes de una entidad
     */
    public function eliminarImagenesEntidad($tipo, $entidadId)
    {
        return $this->where('tipo', $tipo)
                   ->where('entidad_id', $entidadId)
                   ->delete();
    }

    /**
     * Contar imágenes de una entidad
     */
    public function contarImagenesEntidad($tipo, $entidadId)
    {
        return $this->where('tipo', $tipo)
                   ->where('entidad_id', $entidadId)
                   ->where('estado', 'A')
                   ->countAllResults();
    }

    /**
     * Obtener imágenes destacadas (portadas) de múltiples entidades
     */
    public function getImagenesDestacadas($tipo, $entidadIds = [])
    {
        $builder = $this->where('tipo', $tipo)
                       ->where('es_portada', true)
                       ->where('estado', 'A');

        if (!empty($entidadIds)) {
            $builder->whereIn('entidad_id', $entidadIds);
        }

        return $builder->findAll();
    }
}
