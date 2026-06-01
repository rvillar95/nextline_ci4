<?php

namespace App\Models;

use CodeIgniter\Model;

class Modulo extends Model
{
    protected $table      = 'modulo';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id', 'nombre', 'descripcion', 'sa', 'ruta', 'estado', 'mostrar',
        'menu_grupo_id', 'menu_icono', 'menu_etiqueta', 'menu_aplanar',
        'menu_ruta_alterna', 'menu_etiqueta_alterna', 'menu_solo_sa',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'ruta' => 'string|max_length[100]',
        'estado' => 'required|in_list[A,I]',
        'mostrar' => 'required|in_list[S,N]'
    ];

    public function getModuloAll()
    {
        $db = \Config\Database::connect();
        $poder = session()->get('usuario')['poder'];
        if ($poder <= 2) {
            $sql = "select * from modulo where id != 13";
        }else{
            $sql = "select * from modulo";
        }
   
        $modulo = $db->query($sql)->getResult('object');
        return $modulo;   
    }

    public function getModuloLike($ruta)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('modulo');
        $builder->like('ruta', $ruta);
        $modulo = $builder->get()->getResultArray();
        return $modulo;
    }

    public function getActiveModulo($empresaId = null)
    {
        $db = \Config\Database::connect();
        $usuario = session()->get('usuario');
        $poder = $usuario['poder'] ?? 0;
        
        // Si es Super Admin (poder=3), ver todos los módulos
        if ($poder == 3) {
            $sql = "SELECT * FROM modulo WHERE estado = 'A' ORDER BY nombre ASC";
            return $db->query($sql)->getResult('array');
        }
        
        // Si no se proporciona empresa_id, obtenerlo de la sesión
        if ($empresaId === null) {
            $empresaId = $usuario['empresa_id'] ?? null;
        }
        
        // Si tiene empresa, filtrar por módulos del paquete de la empresa
        if ($empresaId !== null) {
            // Obtener paquete_id de la empresa
            $empresaData = $db->table('empresa')
                ->select('paquete_id')
                ->where('id', $empresaId)
                ->get()
                ->getRowArray();
            
            if ($empresaData && !empty($empresaData['paquete_id'])) {
                $paqueteId = $empresaData['paquete_id'];
                // Obtener módulos del paquete
                $sql = "SELECT m.* 
                        FROM modulo m
                        INNER JOIN paquete_modulo pm ON pm.modulo_id = m.id 
                            AND pm.paquete_id = :paquete_id:
                            AND pm.incluido = 'S'
                        WHERE m.estado = 'A' 
                          AND m.sa = 'N'
                        ORDER BY m.nombre ASC";
                return $db->query($sql, ['paquete_id' => $paqueteId])->getResult('array');
            }
        }
        
        // Si no tiene empresa o no tiene paquete, ver módulos no-SA
        $sql = "SELECT * FROM modulo WHERE estado = 'A' AND sa = 'N' ORDER BY nombre ASC";
        return $db->query($sql)->getResult('array');
    }
    
    /**
     * Obtener módulos del paquete de una empresa específica
     */
    public function getModulosPorPaquete($paqueteId): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT m.* 
                FROM modulo m
                INNER JOIN paquete_modulo pm ON pm.modulo_id = m.id 
                    AND pm.paquete_id = :paquete_id:
                    AND pm.incluido = 'S'
                WHERE m.estado = 'A'
                ORDER BY m.nombre ASC";
        return $db->query($sql, ['paquete_id' => $paqueteId])->getResult('array');
    }

}
