<?php

namespace App\Models;

use CodeIgniter\Model;

class Perfil extends Model
{
    protected $table      = 'perfil';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id', 'nombre', 'fcreacion', 'estado', 'empresa_id', 'poder'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'estado' => 'required|in_list[A,I]'
    ];

    public function getPerfilAll($empresaId = null)
    {
        $db = \Config\Database::connect();
        $usuario = session()->get('usuario');
        $poder = $usuario['poder'] ?? 0;
        
        // Si es Super Admin (poder=3), ver todos los perfiles (globales y de todas las empresas)
        if ($poder == 3) {
            $sql = "SELECT * FROM perfil WHERE poder <= :poder: ORDER BY empresa_id ASC, nombre ASC";
            return $db->query($sql, ['poder' => $poder])->getResult('object');
        }
        
        // Si no se proporciona empresa_id, obtenerlo de la sesión
        if ($empresaId === null) {
            $empresaId = $usuario['empresa_id'] ?? null;
        }
        
        // Si tiene empresa, filtrar por empresa_id
        if ($empresaId !== null) {
            $sql = "SELECT * FROM perfil WHERE empresa_id = :empresa_id: AND poder <= :poder: ORDER BY nombre ASC";
            return $db->query($sql, ['empresa_id' => $empresaId, 'poder' => $poder])->getResult('object');
        }
        
        // Si no tiene empresa, ver solo perfiles globales (empresa_id IS NULL)
        $sql = "SELECT * FROM perfil WHERE empresa_id IS NULL AND poder <= :poder: ORDER BY nombre ASC";
        return $db->query($sql, ['poder' => $poder])->getResult('object');
    }

    public function getNombresPerfil()
    {
        $db = \Config\Database::connect();
        $sql = "select nombre from perfil where poder <= :poder:";
        $perfil = $db->query($sql, ['poder' => session()->get('usuario')['poder']])->getResult('array');
        return $perfil;
    }

    public function getPerfil($perfil)
    {
        $db = \Config\Database::connect();
        $sql = "select * from perfil where id = :perfil: ";
        $perfil = $db->query($sql, ['perfil' => $perfil])->getResult('object');
        return $perfil;
    }

    public function getActivePerfil(int $maxPoder, $empresaId = null): array
    {
        $usuario = session()->get('usuario');
        $poder = $usuario['poder'] ?? 0;
        
        // Si no se proporciona empresa_id, obtenerlo de la sesión
        if ($empresaId === null) {
            $empresaId = $usuario['empresa_id'] ?? null;
        }
        
        $builder = $this->db->table($this->table)
            ->select('id, nombre, poder, empresa_id')
            ->where('estado', 'A')
            ->where('poder <=', $maxPoder);
        
        // Si es Super Admin (poder=3), ver todos los perfiles
        if ($poder == 3) {
            // Ver todos (globales y de todas las empresas)
        } elseif ($empresaId !== null) {
            // Filtrar por empresa_id
            $builder->where('empresa_id', $empresaId);
        } else {
            // Solo perfiles globales
            $builder->where('empresa_id IS NULL');
        }
        
        $builder->orderBy('poder', 'DESC')
            ->orderBy('nombre', 'ASC');

        return $builder->get()->getResultArray();
    }
    
    /**
     * Obtener perfiles por empresa
     */
    public function getPerfilesPorEmpresa($empresaId): array
    {
        return $this->db->table($this->table)
            ->select('id, nombre, poder, estado')
            ->where('empresa_id', $empresaId)
            ->where('estado', 'A')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
    }
}
