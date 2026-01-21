<?php

namespace App\Models;

use CodeIgniter\Model;

class MetodoCalculo extends Model
{
    protected $table = 'metodos_calculo';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nombre', 'slug', 'descripcion', 'componentes', 'formula',
        'requiere_datos', 'precio_mensual', 'es_addon', 'activo', 'orden'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[100]',
        'slug' => 'required|min_length[3]|max_length[50]',
        'componentes' => 'required|integer|greater_than[0]|less_than[8]'
    ];

    /**
     * Obtener todos los métodos activos
     */
    public function getMetodosActivos()
    {
        return $this->where('activo', 'A')
            ->orderBy('orden', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Obtener método por slug
     */
    public function getPorSlug($slug)
    {
        return $this->where('slug', $slug)
            ->where('activo', 'A')
            ->first();
    }

    /**
     * Obtener métodos disponibles para una empresa según su PAQUETE
     * Similar a ModuloDetalle::getMenu() pero para métodos
     */
    public function getMetodosDisponibles($empresaId)
    {
        $db = \Config\Database::connect();
        
        // Obtener paquete_id de la empresa
        $empresaData = $db->table('empresa')
            ->select('paquete_id')
            ->where('id', $empresaId)
            ->get()
            ->getRowArray();
        
        if (!$empresaData || empty($empresaData['paquete_id'])) {
            return [];
        }
        
        $paqueteId = $empresaData['paquete_id'];
        
        // Obtener rutas de métodos del paquete (usando paquete_modulo con modulo_detalle)
        $sql = "SELECT DISTINCT
                    mc.id,
                    mc.nombre,
                    mc.slug,
                    mc.descripcion,
                    mc.componentes,
                    mc.precio_mensual,
                    mc.es_addon
                FROM metodos_calculo mc
                INNER JOIN modulo_detalle md ON md.ruta = CONCAT('/calcular-', mc.slug)
                INNER JOIN paquete_modulo pm ON pm.modulo_id = md.id
                WHERE pm.paquete_id = :paquete_id:
                  AND pm.incluido = 'S'
                  AND mc.activo = 'A'
                  AND md.estado = 'A'
                ORDER BY mc.orden ASC";
        
        return $db->query($sql, ['paquete_id' => $paqueteId])->getResult();
    }

    /**
     * Verificar si empresa tiene acceso a un método según su PAQUETE
     */
    public function tieneAcceso($empresaId, $metodoSlug)
    {
        $db = \Config\Database::connect();
        
        // Obtener paquete_id de la empresa
        $empresaData = $db->table('empresa')
            ->select('paquete_id')
            ->where('id', $empresaId)
            ->get()
            ->getRowArray();
        
        if (!$empresaData || empty($empresaData['paquete_id'])) {
            return false;
        }
        
        $paqueteId = $empresaData['paquete_id'];
        
        // Verificar si la ruta del método está en el paquete
        $result = $db->table('modulo_detalle md')
            ->join('paquete_modulo pm', 'pm.modulo_id = md.id')
            ->where('md.ruta', '/calcular-' . $metodoSlug)
            ->where('pm.paquete_id', $paqueteId)
            ->where('pm.incluido', 'S')
            ->where('md.estado', 'A')
            ->countAllResults();
        
        return $result > 0;
    }
}
