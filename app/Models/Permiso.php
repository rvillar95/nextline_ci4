<?php

namespace App\Models;

use CodeIgniter\Model;

class Permiso extends Model
{
    protected $table      = 'permiso';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['id','nombre'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]'
    ];

    public function getNombresPermiso()
    {
        $db = \Config\Database::connect();
        $sql = "select nombre from permiso";
        $perfil = $db->query($sql)->getResult('array');
        return $perfil;   
    }
}
