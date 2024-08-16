<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuloDetalle extends Model
{
    protected $table      = 'modulo_detalle';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id','modulo_id','descripcion','ruta','accion','estado','mostrar','orden'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'modulo_id' => 'required|integer|greater_than[0]',
        'descripcion' => 'string|max_length[500]',
        'ruta' => 'string|max_length[100]',
        'accion' => 'string|max_length[50]',
        'estado' => 'required|in_list[A,I]',
        'mostrar' => 'required|in_list[S,N]',
        'orden' => 'required|integer'
    ];

    public function getModuloDetalleAll()
    {

        $db = \Config\Database::connect();
        $builder = $db->table('modulo_detalle');
        $builder->select('modulo_detalle.id, modulo.nombre as nombreModulo, modulo_detalle.descripcion, modulo_detalle.ruta, modulo_detalle.accion, modulo_detalle.estado, modulo_detalle.mostrar, modulo_detalle.orden');
        $builder->join('modulo', 'modulo_detalle.modulo_id = modulo.id');
        $builder->orderBy('modulo.id', 'asc');
        $builder->orderBy('orden', 'asc');
        $query = $builder->get();
        return $query->getResult('object');
    }
 
    public function getMenu($perfil){
        $db = \Config\Database::connect();
        $sql = "select pe.modulo_id id, mo.nombre, mo.ruta, pe.ver, pe.registrar, pe.editar, pe.eliminar from perfil_modulo pe, perfil per, modulo mo where pe.perfil_id = per.id and pe.modulo_id = mo.id and pe.perfil_id = :perfil: and mo.estado = 'A' and mo.mostrar = 'S' and pe.estado = 'A' order by pe.orden asc";
        $modulos = $db->query($sql, ['perfil' => $perfil])->getResult('array');
        return $modulos;   
    }

    public function getSubMenu($modulo){
        $db = \Config\Database::connect();
        $sql = "select * from modulo_detalle where modulo_id = :modulo: and estado = 'A' order by orden asc";
        $modulos = $db->query($sql, ['modulo' => $modulo])->getResult('array');
        return $modulos;   
    }

    public function getDetalleModulo($id)
    {
        $db = \Config\Database::connect();
        $sql = "select * from modulo_detalle where id = :id: ";
        return $db->query($sql, ['id' => $id])->getRowArray();   
    }
}
