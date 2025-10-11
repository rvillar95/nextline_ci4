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

    protected $allowedFields = ['id', 'nombre', 'fcreacion', 'estado'];

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

    public function getPerfilAll()
    {
        $db = \Config\Database::connect();
        $poderUsuario = session()->get('usuario')['poder'];
        
        // Si es Super Admin (poder=3), mostrar TODOS los perfiles
        // Si no, mostrar solo perfiles con poder MENOR
        if ($poderUsuario >= 3) {
            $sql = "select * from perfil";
            $perfil = $db->query($sql)->getResult('object');
        } else {
            $sql = "select * from perfil where poder <= :poder:";
            $perfil = $db->query($sql, ['poder' => $poderUsuario])->getResult('object');
        }
        
        return $perfil;
    }

    public function getNombresPerfil()
    {
        $db = \Config\Database::connect();
        $poderUsuario = session()->get('usuario')['poder'];
        
        // Si es Super Admin (poder=3), mostrar TODOS los perfiles
        // Si no, mostrar solo perfiles con poder MENOR
        if ($poderUsuario >= 3) {
            $sql = "select nombre from perfil";
            $perfil = $db->query($sql)->getResult('array');
        } else {
            $sql = "select nombre from perfil where poder <= :poder:";
            $perfil = $db->query($sql, ['poder' => $poderUsuario])->getResult('array');
        }
        
        return $perfil;
    }

    public function getPerfil($perfil)
    {
        $db = \Config\Database::connect();
        $sql = "select * from perfil where id = :perfil: ";
        $perfil = $db->query($sql, ['perfil' => $perfil])->getResult('object');
        return $perfil;
    }

    public function getActivePerfil(int $maxPoder): array
    {
        $builder = $this->db->table($this->table)
            ->select('id, nombre, poder')
            ->where('estado', 'A');
        
        // Si es Super Admin (poder=3), mostrar TODOS los perfiles
        // Si no, mostrar solo perfiles con poder MENOR
        if ($maxPoder < 3) {
            $builder->where('poder <=', $maxPoder);
        }
        
        $builder->orderBy('poder', 'DESC')
                ->orderBy('nombre', 'ASC');

        return $builder->get()->getResultArray();
    }
}
