<?php

namespace App\Models;

use CodeIgniter\Model;

class Servicio extends Model
{
    protected $table      = 'servicio';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id','nombre','descripcionCorta','descripcionLarga','valor','foto','estado'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'descripcionCorta' => 'string|max_length[500]',
        'descripcionLarga' => 'string|max_length[2000]',
        'valor' => 'required|integer|greater_than[0]',
        'estado' => 'required|in_list[A,I]',
    ];

    public function getServicioAll()
    {
        $db = \Config\Database::connect();
        $sql = "select * from servicio";
        $modulo = $db->query($sql)->getResult('object');
        return $modulo;   
    }

}
