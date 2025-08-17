<?php

namespace App\Models;

use CodeIgniter\Model;

class PerfilModulo extends Model
{
    protected $table      = 'perfil_modulo';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id','perfil_id','modulo_id','ver','registrar','editar','eliminar','estado','orden'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'perfil_id' => 'required|integer|greater_than[0]',
        'modulo_id' => 'required|integer|greater_than[0]',
        'ver' => 'required|integer',
        'registrar' => 'required|integer',
        'editar' => 'required|integer',
        'eliminar' => 'required|integer',
        'estado' => 'required|in_list[A,I]',
        'orden' => 'required|integer|greater_than[0]'
    ];

    public function getPerfilModuloAll($id, $poder)
    {
        
        $db = \Config\Database::connect();
        $builder = $db->table('perfil_modulo');
        $builder->select('perfil_modulo.id, perfil.nombre as nombrePerfil, modulo.nombre as nombreModulo, perfil_modulo.ver, perfil_modulo.editar, perfil_modulo.registrar, perfil_modulo.eliminar, perfil_modulo.estado, perfil_modulo.orden');
        $builder->join('modulo', 'perfil_modulo.modulo_id = modulo.id');
        $builder->join('perfil', 'perfil_modulo.perfil_id = perfil.id');
        $builder->where('perfil.poder <=', $poder); 
        if ($id > 0) {
            $builder->where('perfil.id =', $id); 
        }
        //$builder->where('perfil_modulo.feliminacion', null); 
        $builder->orderBy('modulo.id', 'asc');
        $builder->orderBy('orden', 'asc');
        $query = $builder->get();
        return $query->getResult('object');
    }

    public function getNombresPerfil()
    {
        $db = \Config\Database::connect();
        $sql = "select nombre from perfil";
        $perfil = $db->query($sql)->getResult('array');
        return $perfil;   
    }

    public function getPerfilModulo($id)
    {
        $db = \Config\Database::connect();
        $sql = "select * from perfil_modulo where id = :id: ";
        return $db->query($sql, ['id' => $id])->getRowArray();   
    }

    /**
     * Lista de perfil_modulo con joins y filtros comunes.
     * @param array{perfil_id?:int, q?:string} $filters
     */
    public function listWithJoins(array $filters = [])
    {
        $builder = $this->db->table('perfil_modulo pm')
            ->select('pm.*, p.nombre AS perfil_nombre, m.nombre AS modulo_nombre')
            ->join('perfil p', 'p.id = pm.perfil_id')
            ->join('modulo m', 'm.id = pm.modulo_id');

        if (!empty($filters['perfil_id'])) {
            $builder->where('pm.perfil_id', (int) $filters['perfil_id']);
        }

        if (!empty($filters['q'])) {
            $q = trim($filters['q']);
            $builder->groupStart()
                ->like('p.nombre', $q)
                ->orLike('m.nombre', $q)
            ->groupEnd();
        }

        // Orden por perfil, luego orden definido
        $builder->orderBy('p.nombre', 'ASC')->orderBy('pm.orden', 'ASC');

        return $builder;
    }

    /** Builder base con joins y poder */
    private function baseBuilder(int $maxPoder)
    {
        return $this->db->table('perfil_modulo pm')
            ->select([
                'pm.id',
                'p.id AS perfil_id',
                'm.id AS modulo_id',
                'p.nombre AS perfil_nombre',
                'm.nombre AS modulo_nombre',
                'pm.ver','pm.registrar','pm.editar','pm.eliminar',
                'pm.estado','pm.orden',
            ])
            ->join('perfil p', 'p.id = pm.perfil_id')
            ->join('modulo m', 'm.id = pm.modulo_id')
            ->where('p.poder <=', $maxPoder);
            // ->where('pm.estado', 'A'); // si quieres sólo activos
    }

    /** Total sin filtros de búsqueda (sólo por poder) */
    public function countAllByPower(int $maxPoder): int
    {
        $b = $this->baseBuilder($maxPoder);
        return (int) $b->countAllResults(false); // false = no resetea
    }

    /** Total con filtros de perfil y búsqueda */
    public function countFiltered(int $maxPoder, ?int $perfilId, string $search): int
    {
        $b = $this->baseBuilder($maxPoder);
        if ($perfilId) $b->where('p.id', $perfilId);
        if ($search !== '') {
            $b->groupStart()
                ->like('p.nombre', $search)
                ->orLike('m.nombre', $search)
            ->groupEnd();
        }
        return (int) $b->countAllResults();
    }

    /** Página de datos */
    public function fetchPage(
        int $maxPoder,
        ?int $perfilId,
        string $search,
        string $orderBy,
        string $orderDir,
        int $start,
        int $length
    ): array {
        $b = $this->baseBuilder($maxPoder);
        if ($perfilId) $b->where('p.id', $perfilId);
        if ($search !== '') {
            $b->groupStart()
                ->like('p.nombre', $search)
                ->orLike('m.nombre', $search)
            ->groupEnd();
        }
        $b->orderBy($orderBy, strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC')
          ->orderBy('pm.orden', 'ASC')
          ->limit($length > 0 ? $length : 10, $start > 0 ? $start : 0);

        return $b->get()->getResultArray();
    }
}
