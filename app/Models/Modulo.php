<?php

namespace App\Models;

use CodeIgniter\Model;

class Modulo extends Model
{
    protected $table      = 'modulo';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['id','nombre','ruta','mostrar'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'descripcion' => 'string|max_length[500]',
        'estado' => 'required|in_list[A,I]'
    ];
}
