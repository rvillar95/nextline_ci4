<?php

namespace App\Models;

use CodeIgniter\Model;

class Galeria extends Model
{
    protected $table      = 'galeria';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id','nombre','descripcion','portada','fecha','foto','estado'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'descripcion' => 'string|max_length[500]',
        'portada' => 'string|max_length[2000]',
        'fecha' => 'required|valid_date[Y-m-d H:i:s]',
        'estado' => 'required|in_list[A,I]',
    ];

    public function getGaleriaAll()
    {
        $db = \Config\Database::connect();
        $sql = "select * from galeria";
        $modulo = $db->query($sql)->getResult('object');
        return $modulo;   
    }

}
