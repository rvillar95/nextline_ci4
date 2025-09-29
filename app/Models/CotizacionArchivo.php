<?php

namespace App\Models;

use CodeIgniter\Model;

class CotizacionArchivo extends Model
{
    protected $table = 'cotizacion_archivos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'cotizacion_id', 'nombre_archivo', 'nombre_original', 'ruta_archivo',
        'tipo_archivo', 'tamaño_bytes', 'descripcion', 'es_principal', 'orden'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = null;

    protected $validationRules = [
        'cotizacion_id' => 'required|integer|is_natural_no_zero',
        'nombre_archivo' => 'required|string|max_length[255]',
        'nombre_original' => 'required|string|max_length[255]',
        'ruta_archivo' => 'required|string|max_length[500]',
        'tipo_archivo' => 'permit_empty|string|max_length[100]',
        'tamaño_bytes' => 'permit_empty|integer|greater_than_equal_to[0]',
        'descripcion' => 'permit_empty|string',
        'es_principal' => 'permit_empty|boolean',
        'orden' => 'permit_empty|integer|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'cotizacion_id' => [
            'required' => 'La cotización es obligatoria.',
            'integer' => 'La cotización debe ser un número válido.',
            'is_natural_no_zero' => 'La cotización debe ser un número válido.'
        ],
        'nombre_archivo' => [
            'required' => 'El nombre del archivo es obligatorio.',
            'string' => 'El nombre del archivo debe ser una cadena de texto.',
            'max_length' => 'El nombre del archivo no puede exceder de 255 caracteres.'
        ],
        'nombre_original' => [
            'required' => 'El nombre original del archivo es obligatorio.',
            'string' => 'El nombre original debe ser una cadena de texto.',
            'max_length' => 'El nombre original no puede exceder de 255 caracteres.'
        ],
        'ruta_archivo' => [
            'required' => 'La ruta del archivo es obligatoria.',
            'string' => 'La ruta debe ser una cadena de texto.',
            'max_length' => 'La ruta no puede exceder de 500 caracteres.'
        ]
    ];

    protected $skipValidation = true;
    protected $cleanValidationRules = true;

    /**
     * Obtener archivos de una cotización
     */
    public function getArchivosPorCotizacion($cotizacionId)
    {
        return $this->where('cotizacion_id', $cotizacionId)
                   ->orderBy('es_principal', 'DESC')
                   ->orderBy('orden', 'ASC')
                   ->orderBy('fcreacion', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener archivo principal de una cotización
     */
    public function getArchivoPrincipal($cotizacionId)
    {
        return $this->where('cotizacion_id', $cotizacionId)
                   ->where('es_principal', true)
                   ->first();
    }

    /**
     * Establecer archivo como principal
     */
    public function establecerComoPrincipal($archivoId, $cotizacionId)
    {
        // Quitar principal de otros archivos de la misma cotización
        $this->where('cotizacion_id', $cotizacionId)
             ->set('es_principal', false)
             ->update();

        // Establecer este archivo como principal
        return $this->update($archivoId, ['es_principal' => true]);
    }

    /**
     * Obtener tipos de archivo permitidos
     */
    public function getTiposPermitidos()
    {
        return [
            'pdf' => 'Documento PDF',
            'jpg' => 'Imagen JPG',
            'jpeg' => 'Imagen JPEG',
            'png' => 'Imagen PNG',
            'gif' => 'Imagen GIF',
            'dwg' => 'Dibujo AutoCAD',
            'dxf' => 'Intercambio CAD',
            'doc' => 'Documento Word',
            'docx' => 'Documento Word',
            'xls' => 'Hoja de cálculo Excel',
            'xlsx' => 'Hoja de cálculo Excel',
            'txt' => 'Archivo de texto',
            'zip' => 'Archivo comprimido',
            'rar' => 'Archivo comprimido'
        ];
    }

    /**
     * Validar tipo de archivo
     */
    public function validarTipoArchivo($tipoArchivo)
    {
        $tiposPermitidos = array_keys($this->getTiposPermitidos());
        return in_array(strtolower($tipoArchivo), $tiposPermitidos);
    }

    /**
     * Obtener tamaño máximo permitido (en bytes)
     */
    public function getTamañoMaximo()
    {
        return 10 * 1024 * 1024; // 10 MB
    }

    /**
     * Formatear tamaño de archivo
     */
    public function formatearTamaño($bytes)
    {
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($unidades) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $unidades[$pow];
    }

    /**
     * Generar nombre único para archivo
     */
    public function generarNombreUnico($nombreOriginal, $cotizacionId)
    {
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nombreBase = pathinfo($nombreOriginal, PATHINFO_FILENAME);
        
        // Limpiar nombre base
        $nombreBase = preg_replace('/[^a-zA-Z0-9_-]/', '', $nombreBase);
        $nombreBase = substr($nombreBase, 0, 50); // Limitar longitud
        
        $timestamp = time();
        $nombreUnico = "cotizacion_{$cotizacionId}_{$timestamp}_{$nombreBase}.{$extension}";
        
        return $nombreUnico;
    }

    /**
     * Eliminar archivo físico
     */
    public function eliminarArchivoFisico($rutaArchivo)
    {
        if (file_exists($rutaArchivo)) {
            return unlink($rutaArchivo);
        }
        return true;
    }

    /**
     * Eliminar archivo completo (base de datos + físico)
     */
    public function eliminarArchivoCompleto($archivoId)
    {
        $archivo = $this->find($archivoId);
        
        if (!$archivo) {
            return false;
        }
        
        // Eliminar archivo físico
        $this->eliminarArchivoFisico($archivo->ruta_archivo);
        
        // Eliminar registro de base de datos
        return $this->delete($archivoId);
    }

    /**
     * Obtener estadísticas de archivos
     */
    public function getEstadisticasArchivos()
    {
        $stats = [];
        
        $stats['total'] = $this->countAllResults();
        $stats['tamaño_total'] = $this->selectSum('tamaño_bytes')->first()->tamaño_bytes ?? 0;
        
        // Archivos por tipo
        $tipos = $this->select('tipo_archivo, COUNT(*) as cantidad')
                     ->groupBy('tipo_archivo')
                     ->findAll();
        
        $stats['por_tipo'] = [];
        foreach ($tipos as $tipo) {
            $stats['por_tipo'][$tipo->tipo_archivo] = $tipo->cantidad;
        }
        
        return $stats;
    }

    /**
     * Limpiar archivos huérfanos (sin cotización asociada)
     */
    public function limpiarArchivosHuerfanos()
    {
        $cotizacionModel = new Cotizacion();
        $cotizacionesExistentes = $cotizacionModel->select('id')->findAll();
        $idsExistentes = array_column($cotizacionesExistentes, 'id');
        
        if (empty($idsExistentes)) {
            return 0;
        }
        
        $archivosHuerfanos = $this->whereNotIn('cotizacion_id', $idsExistentes)->findAll();
        $eliminados = 0;
        
        foreach ($archivosHuerfanos as $archivo) {
            if ($this->eliminarArchivoCompleto($archivo->id)) {
                $eliminados++;
            }
        }
        
        return $eliminados;
    }
}
