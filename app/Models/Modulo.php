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

    protected $allowedFields = ['id','nombre','descripcion','ruta','estado','mostrar'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'ruta' => 'string|max_length[100]',
        'estado' => 'required|in_list[A,I]',
        'mostrar' => 'required|in_list[S,N]'
    ];

    public function getModuloAll()
    {
        $db = \Config\Database::connect();
        $sql = "select * from modulo";
        $modulo = $db->query($sql)->getResult('object');
        return $modulo;   
    }

    public function getModuloLike($ruta)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('modulo');
        $builder->like('ruta', $ruta);
        $modulo = $builder->get()->getResultArray();
        return $modulo;
    }

    public function getActiveModulo()
    {
        $db = \Config\Database::connect();
        $sql = "select * from modulo where estado = 'A'";
        $perfil = $db->query($sql)->getResult('array');
        return $perfil;   
    }

}
