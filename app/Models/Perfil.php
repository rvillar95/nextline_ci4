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

    protected $allowedFields = ['id','nombre','estado'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'estado' => 'required|in_list[A,I]'
    ];

    public function getNombresPerfil()
    {
        $db = \Config\Database::connect();
        $sql = "select nombre from perfil";
        $perfil = $db->query($sql)->getResult('array');
        return $perfil;   
    }
}
