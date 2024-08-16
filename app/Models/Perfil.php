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

    protected $allowedFields = ['id','nombre','fcreacion','estado'];

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
        //$sql = "select * from perfil where poder < :poder: AND feliminacion IS NULL";
        $sql = "select * from perfil where poder < :poder: ";
        $perfil = $db->query($sql,['poder' => session()->get('usuario')['poder']])->getResult('object');
        return $perfil;   
    }

    public function getNombresPerfil()
    {
        $db = \Config\Database::connect();
        $sql = "select nombre from perfil where poder <= :poder:";
        $perfil = $db->query($sql,['poder' => session()->get('usuario')['poder']])->getResult('array');
        return $perfil;   
    }

    public function getPerfil($perfil)
    {
        $db = \Config\Database::connect();
        $sql = "select * from perfil where id = :perfil: ";
        $perfil = $db->query($sql, ['perfil' => $perfil])->getResult('object');
        return $perfil;   
    }

    public function getActivePerfil()
    {
        $db = \Config\Database::connect();
        $sql = "select * from perfil where estado = 'A' and poder <= :poder:";
        $perfil = $db->query($sql,['poder' => session()->get('usuario')['poder']])->getResult('array');
        return $perfil;   
    }
}
