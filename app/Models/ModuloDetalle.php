<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuloDetalle extends Model
{
    protected $table      = 'modulo_detalle';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id', 'modulo_id', 'descripcion', 'ruta', 'accion', 'estado', 'mostrar', 'orden'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'modulo_id' => 'required|integer|greater_than[0]',
        'descripcion' => 'string|max_length[500]',
        'ruta' => 'string|max_length[100]',
        'accion' => 'string|max_length[50]',
        'estado' => 'required|in_list[A,I]',
        'mostrar' => 'required|in_list[S,N]',
        'orden' => 'required|integer'
    ];

    public function getModuloDetalleAll()
    {

        $db = \Config\Database::connect();
        $builder = $db->table('modulo_detalle');
        $builder->select('modulo_detalle.id, modulo.nombre as nombreModulo, modulo_detalle.descripcion, modulo_detalle.ruta, modulo_detalle.accion, modulo_detalle.estado, modulo_detalle.mostrar, modulo_detalle.orden');
        $builder->join('modulo', 'modulo_detalle.modulo_id = modulo.id');
        $builder->orderBy('modulo.id', 'asc');
        $builder->orderBy('orden', 'asc');
        $query = $builder->get();
        return $query->getResult('object');
    }

    /**
     * Obtiene el menú de módulos para un perfil, filtrando por el paquete de la empresa del usuario
     * 
     * Lógica:
     * 1. Obtiene el paquete_id de la empresa del usuario
     * 2. Consulta módulos del paquete (paquete_modulo)
     * 3. Filtra por permisos del perfil (perfil_modulo)
     * 4. Solo muestra módulos que estén en AMBOS (paquete Y perfil)
     * 
     * @param int $perfilId ID del perfil del usuario
     * @param int|null $empresaId ID de la empresa del usuario (opcional, se obtiene de la sesión si no se proporciona)
     * @return array Array de módulos con sus permisos
     */
    public function getMenu($perfil, $empresaId = null)
    {
        $db = \Config\Database::connect();
        
        // Si no se proporciona empresa_id, intentar obtenerlo de la sesión
        if ($empresaId === null) {
            $usuario = session()->get('usuario');
            $empresaId = $usuario['empresa_id'] ?? null;
        }
        
        // Si es Super Admin (poder=3) o no tiene empresa, mostrar todos los módulos del perfil
        $usuario = session()->get('usuario');
        $poder = $usuario['poder'] ?? 0;
        
        if ($poder == 3 || $empresaId === null) {
            // Super Admin o sin empresa: mostrar todos los módulos del perfil
            $sql = "SELECT 
                        pe.modulo_id AS id, 
                        mo.nombre, 
                        mo.ruta, 
                        pe.ver, 
                        pe.registrar, 
                        pe.editar, 
                        pe.eliminar 
                    FROM perfil_modulo pe
                    INNER JOIN perfil per ON pe.perfil_id = per.id
                    INNER JOIN modulo mo ON pe.modulo_id = mo.id
                    WHERE pe.perfil_id = :perfil:
                      AND mo.estado = 'A'
                      AND mo.mostrar = 'S'
                      AND pe.estado = 'A'
                    ORDER BY pe.orden ASC";
            return $db->query($sql, ['perfil' => $perfil])->getResult('array');
        }
        
        // Usuario normal: filtrar por paquete Y perfil
        // Primero verificar que la empresa tenga un paquete asignado
        $empresaData = $db->table('empresa')
            ->select('paquete_id')
            ->where('id', $empresaId)
            ->get()
            ->getRowArray();
        
        if (!$empresaData || empty($empresaData['paquete_id'])) {
            // Si la empresa no tiene paquete, no mostrar módulos
            log_message('warning', "Empresa ID {$empresaId} no tiene paquete asignado");
            return [];
        }
        
        $paqueteId = $empresaData['paquete_id'];
        
        $sql = "SELECT 
                    pe.modulo_id AS id, 
                    mo.nombre, 
                    mo.ruta, 
                    pe.ver, 
                    pe.registrar, 
                    pe.editar, 
                    pe.eliminar 
                FROM perfil_modulo pe
                INNER JOIN perfil per ON pe.perfil_id = per.id
                INNER JOIN modulo mo ON pe.modulo_id = mo.id
                INNER JOIN paquete_modulo pm ON pm.modulo_id = pe.modulo_id 
                    AND pm.paquete_id = :paquete_id:
                    AND pm.incluido = 'S'
                WHERE pe.perfil_id = :perfil:
                  AND mo.estado = 'A'
                  AND mo.mostrar = 'S'
                  AND pe.estado = 'A'
                  AND mo.sa = 'N'  -- Excluir módulos de Super Admin
                ORDER BY pe.orden ASC";
        
        $modulos = $db->query($sql, [
            'perfil' => $perfil,
            'paquete_id' => $paqueteId
        ])->getResult('array');
        
        // Log para depuración
        log_message('debug', "getMenu - Perfil: {$perfil}, Empresa: {$empresaId}, Paquete: {$paqueteId}, Módulos encontrados: " . count($modulos));
        
        return $modulos;
    }

    /**
     * Obtiene todos los módulos accesibles para un perfil (para permisos)
     * NO respeta el campo mostrar - solo estado y permisos
     * Filtra por paquete de la empresa del usuario
     * 
     * @param int $perfilId ID del perfil del usuario
     * @param int|null $empresaId ID de la empresa del usuario (opcional, se obtiene de la sesión si no se proporciona)
     * @return array Array de módulos con sus permisos
     */
    public function getMenuForPermissions($perfil, $empresaId = null)
    {
        $db = \Config\Database::connect();
        
        // Si no se proporciona empresa_id, intentar obtenerlo de la sesión
        if ($empresaId === null) {
            $usuario = session()->get('usuario');
            $empresaId = $usuario['empresa_id'] ?? null;
        }
        
        // Si es Super Admin (poder=3) o no tiene empresa, mostrar todos los módulos del perfil
        $usuario = session()->get('usuario');
        $poder = $usuario['poder'] ?? 0;
        
        if ($poder == 3 || $empresaId === null) {
            // Super Admin o sin empresa: mostrar todos los módulos del perfil
            $sql = "SELECT 
                        pe.modulo_id AS id, 
                        mo.nombre, 
                        mo.ruta, 
                        pe.ver, 
                        pe.registrar, 
                        pe.editar, 
                        pe.eliminar 
                    FROM perfil_modulo pe
                    INNER JOIN perfil per ON pe.perfil_id = per.id
                    INNER JOIN modulo mo ON pe.modulo_id = mo.id
                    WHERE pe.perfil_id = :perfil:
                      AND mo.estado = 'A'
                      AND pe.estado = 'A'
                    ORDER BY pe.orden ASC";
            return $db->query($sql, ['perfil' => $perfil])->getResult('array');
        }
        
        // Usuario normal: filtrar por paquete Y perfil
        $sql = "SELECT 
                    pe.modulo_id AS id, 
                    mo.nombre, 
                    mo.ruta, 
                    pe.ver, 
                    pe.registrar, 
                    pe.editar, 
                    pe.eliminar 
                FROM perfil_modulo pe
                INNER JOIN perfil per ON pe.perfil_id = per.id
                INNER JOIN modulo mo ON pe.modulo_id = mo.id
                INNER JOIN empresa e ON e.id = :empresa_id:
                INNER JOIN paquete_modulo pm ON pm.modulo_id = pe.modulo_id 
                    AND pm.paquete_id = e.paquete_id 
                    AND pm.incluido = 'S'
                WHERE pe.perfil_id = :perfil:
                  AND mo.estado = 'A'
                  AND pe.estado = 'A'
                  AND mo.sa = 'N'  -- Excluir módulos de Super Admin
                ORDER BY pe.orden ASC";
        
        $modulos = $db->query($sql, [
            'perfil' => $perfil,
            'empresa_id' => $empresaId
        ])->getResult('array');
        
        return $modulos;
    }

    public function getSubMenu($modulo)
    {
        $db = \Config\Database::connect();
        $sql = "select * from modulo_detalle where modulo_id = :modulo: and estado = 'A' order by orden asc";
        $modulos = $db->query($sql, ['modulo' => $modulo])->getResult('array');
        return $modulos;
    }

    public function getDetalleModulo($id)
    {
        $db = \Config\Database::connect();
        $sql = "select * from modulo_detalle where id = :id: ";
        return $db->query($sql, ['id' => $id])->getRowArray();
    }


    /**
     * NUEVO: Trae en una sola consulta todos los patrones de rutas accesibles
     * para un perfil (módulo y submódulos) junto a sus acciones y flags efectivos.
     *
     * IMPORTANTE: Este método NO respeta el campo 'mostrar' del módulo.
     * El campo 'mostrar' solo controla la visibilidad en el menú, NO los permisos de acceso.
     * Los permisos se basan únicamente en el estado del módulo y los permisos del perfil.
     *
     * Retorna un array de items con:
     * - modulo_ruta (string)
     * - detalle_ruta (string|null)
     * - acciones_csv (string|null)  // de modulo_detalle.accion
     * - permisos (array)            // ['ver'=>0/1, 'registrar'=>0/1, 'editar'=>0/1, 'eliminar'=>0/1]
     */
    /**
     * Obtiene todos los patrones de rutas accesibles para un perfil
     * Filtra por paquete de la empresa del usuario
     * 
     * @param int $perfilId ID del perfil del usuario
     * @param int|null $empresaId ID de la empresa del usuario (opcional, se obtiene de la sesión si no se proporciona)
     * @return array Array de rutas con sus permisos
     */
    public function getAllowedByPerfil(int $perfilId, $empresaId = null): array
    {
        $db = $this->db;
        
        // Si no se proporciona empresa_id, intentar obtenerlo de la sesión
        if ($empresaId === null) {
            $usuario = session()->get('usuario');
            $empresaId = $usuario['empresa_id'] ?? null;
        }
        
        // Si es Super Admin (poder=3) o no tiene empresa, mostrar todos los módulos del perfil
        $usuario = session()->get('usuario');
        $poder = $usuario['poder'] ?? 0;
        
        if ($poder == 3 || $empresaId === null) {
            // Super Admin o sin empresa: mostrar todos los módulos del perfil
            $sql = "
                SELECT
                    m.ruta            AS modulo_ruta,
                    md.ruta           AS detalle_ruta,
                    md.accion         AS acciones_csv,
                    pm.ver,
                    pm.registrar,
                    pm.editar,
                    pm.eliminar
                FROM perfil_modulo pm
                JOIN modulo m
                  ON m.id = pm.modulo_id
                 AND m.estado = 'A'
                LEFT JOIN modulo_detalle md
                  ON md.modulo_id = m.id
                 AND md.estado = 'A'
                WHERE pm.perfil_id = :pid:
                  AND pm.estado = 'A'
                ORDER BY pm.orden ASC, md.orden ASC
            ";
            $rows = $db->query($sql, ['pid' => $perfilId])->getResultArray();
        } else {
            // Usuario normal: filtrar por paquete Y perfil
            $sql = "
                SELECT
                    m.ruta            AS modulo_ruta,
                    md.ruta           AS detalle_ruta,
                    md.accion         AS acciones_csv,
                    pm.ver,
                    pm.registrar,
                    pm.editar,
                    pm.eliminar
                FROM perfil_modulo pm
                JOIN modulo m
                  ON m.id = pm.modulo_id
                 AND m.estado = 'A'
                INNER JOIN empresa e ON e.id = :empresa_id:
                INNER JOIN paquete_modulo pkm ON pkm.modulo_id = pm.modulo_id 
                    AND pkm.paquete_id = e.paquete_id 
                    AND pkm.incluido = 'S'
                LEFT JOIN modulo_detalle md
                  ON md.modulo_id = m.id
                 AND md.estado = 'A'
                WHERE pm.perfil_id = :pid:
                  AND pm.estado = 'A'
                  AND m.sa = 'N'  -- Excluir módulos de Super Admin
                ORDER BY pm.orden ASC, md.orden ASC
            ";
            $rows = $db->query($sql, [
                'pid' => $perfilId,
                'empresa_id' => $empresaId
            ])->getResultArray();
        }
        //echo "<pre>";
        //print_r($rows);
        //echo "</pre>";
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'modulo_ruta'  => (string) $r['modulo_ruta'],
                'detalle_ruta' => $r['detalle_ruta'] !== null ? (string) $r['detalle_ruta'] : null,
                'acciones_csv' => $r['acciones_csv'] !== null ? (string) $r['acciones_csv'] : null,
                'permisos'     => [
                    'ver'       => (int) $r['ver'] === 1,
                    'registrar' => (int) $r['registrar'] === 1,
                    'editar'    => (int) $r['editar'] === 1,
                    'eliminar'  => (int) $r['eliminar'] === 1,
                ],
            ];
        }
        return $out;
    }

        /** Builder base con join a modulo */
    private function baseMD()
    {
        return $this->db->table('modulo_detalle md')
            ->select([
                'md.id',
                'm.id   AS modulo_id',
                'm.nombre AS modulo_nombre',
                'md.descripcion',
                'md.ruta',
                'md.accion',
                'md.estado',
                'md.mostrar',
                'md.orden',
            ])
            ->join('modulo m', 'm.id = md.modulo_id');
            // ->where('md.estado', 'A'); // descomenta si quieres solo activos
    }

    /** Total sin filtros (solo por estado si se activa arriba) */
    public function countAllMD(): int
    {
        $b = $this->baseMD();
        return (int) $b->countAllResults(false);
    }

    /** Total con filtros de módulo y búsqueda */
    public function countFilteredMD(?int $moduloId, string $search): int
    {
        $b = $this->baseMD();
        if ($moduloId) $b->where('m.id', $moduloId);
        if ($search !== '') {
            $b->groupStart()
                ->like('m.nombre', $search)
                ->orLike('md.descripcion', $search)
                ->orLike('md.ruta', $search)
                ->orLike('md.accion', $search)
            ->groupEnd();
        }
        return (int) $b->countAllResults();
    }

    /** Página de datos con filtros, orden y límites */
    public function fetchPageMD(
        ?int $moduloId,
        string $search,
        string $orderBy,
        string $orderDir,
        int $start,
        int $length
    ): array {
        $b = $this->baseMD();
        if ($moduloId) $b->where('m.id', $moduloId);
        if ($search !== '') {
            $b->groupStart()
                ->like('m.nombre', $search)
                ->orLike('md.descripcion', $search)
                ->orLike('md.ruta', $search)
                ->orLike('md.accion', $search)
            ->groupEnd();
        }

        $b->orderBy($orderBy, strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC')
          ->orderBy('md.orden', 'ASC')
          ->limit($length > 0 ? $length : 10, $start > 0 ? $start : 0);

        return $b->get()->getResultArray();
    }
}
