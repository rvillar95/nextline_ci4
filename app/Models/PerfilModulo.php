<?php

namespace App\Models;

use CodeIgniter\Model;

class PerfilModulo extends Model
{
    protected $table      = 'perfil_modulo';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['id','perfil_id','modulo_id','ver','registrar','editar','eliminar','estado','orden'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'perfil_id' => 'required|integer|greater_than[0]',
        'modulo_id' => 'required|integer|greater_than[0]',
        'ver' => 'required|integer',
        'registrar' => 'required|integer',
        'editar' => 'required|integer',
        'eliminar' => 'required|integer',
        'estado' => 'required|in_list[A,I]',
        'orden' => 'required|integer|greater_than[0]'
    ];

    public function getPerfilModuloAll()
    {

        $db = \Config\Database::connect();
        $builder = $db->table('perfil_modulo');
        $builder->select('perfil_modulo.id, perfil.nombre as nombrePerfil, modulo.nombre as nombreModulo, perfil_modulo.ver, perfil_modulo.editar, perfil_modulo.registrar, perfil_modulo.eliminar, perfil_modulo.estado, perfil_modulo.orden');
        $builder->join('modulo', 'perfil_modulo.modulo_id = modulo.id');
        $builder->join('perfil', 'perfil_modulo.perfil_id = perfil.id');
        $builder->where('perfil.poder <=', session()->get('usuario')['poder']); 
        $builder->orderBy('modulo.id', 'asc');
        $builder->orderBy('orden', 'asc');
        $query = $builder->get();
        return $query->getResult('object');
    }

    public function getNombresPerfil()
    {
        $db = \Config\Database::connect();
        $sql = "select nombre from perfil";
        $perfil = $db->query($sql)->getResult('array');
        return $perfil;   
    }

    public function getPerfilModulo($id)
    {
        $db = \Config\Database::connect();
        $sql = "select * from perfil_modulo where id = :id: ";
        return $db->query($sql, ['id' => $id])->getRowArray();   
    }
}
