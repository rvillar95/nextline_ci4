<?php

namespace App\Models;

use CodeIgniter\Model;

class Perfil extends Model
{
    protected $table      = 'perfil';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

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
        $sql = "select * from perfil";
        $perfil = $db->query($sql)->getResult('object');
        return $perfil;   
    }

    public function getNombresPerfil()
    {
        $db = \Config\Database::connect();
        $sql = "select nombre from perfil";
        $perfil = $db->query($sql)->getResult('array');
        return $perfil;   
    }

    public function getPerfil($perfil)
    {
        $db = \Config\Database::connect();
        $sql = "select * from perfil where id = :perfil: ";
        $perfil = $db->query($sql, ['perfil' => $perfil])->getResult('object');
        return $perfil;   
    }
}
