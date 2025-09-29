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

    public function getMenu($perfil)
    {
        $db = \Config\Database::connect();
        // Este método es para mostrar en el menú, SÍ debe respetar el campo mostrar
        $sql = "select pe.modulo_id id, mo.nombre, mo.ruta, pe.ver, pe.registrar, pe.editar, pe.eliminar from perfil_modulo pe, perfil per, modulo mo where pe.perfil_id = per.id and pe.modulo_id = mo.id and pe.perfil_id = :perfil: and mo.estado = 'A' and mo.mostrar = 'S' and pe.estado = 'A' order by pe.orden asc";
        $modulos = $db->query($sql, ['perfil' => $perfil])->getResult('array');
        return $modulos;
    }

    /**
     * Obtiene todos los módulos accesibles para un perfil (para permisos)
     * NO respeta el campo mostrar - solo estado y permisos
     */
    public function getMenuForPermissions($perfil)
    {
        $db = \Config\Database::connect();
        $sql = "select pe.modulo_id id, mo.nombre, mo.ruta, pe.ver, pe.registrar, pe.editar, pe.eliminar from perfil_modulo pe, perfil per, modulo mo where pe.perfil_id = per.id and pe.modulo_id = mo.id and pe.perfil_id = :perfil: and mo.estado = 'A' and pe.estado = 'A' order by pe.orden asc";
        $modulos = $db->query($sql, ['perfil' => $perfil])->getResult('array');
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
    public function getAllowedByPerfil(int $perfilId): array
    {
        
        $db = $this->db;
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
