<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuloDetalle extends Model
{
    protected $table      = 'modulo_detalle';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['id','modulo_id','descripcion','ruta','mostrar'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'modulo_id' => 'required|integer|greater_than[0]',
        'permiso_id' => 'required|integer|greater_than[0]',
        'perfil_id' => 'required|integer|greater_than[0]',
        'descripcion' => 'string|max_length[100]',
        'ruta' => 'string|max_length[100]',
        'orden' => 'required|integer|greater_than[0]'
    ];

    public function getModulos($perfil)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT perf.nombre as perfil, per.nombre as permiso, modu.nombre as modulo ,det.ruta FROM modulo modu, modulo_detalle det, permiso per, perfil perf WHERE modu.id = det.modulo_id and det.permiso_id = per.id AND det.perfil_id = perf.id and det.perfil_id = :perfil: and det.orden != 0 group by det.modulo_id order by det.orden asc";
        $modulos = $db->query($sql, ['perfil' => $perfil])->getResult('array');
        return $modulos;   
    }

    public function getPermisos($perfil)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT perf.nombre as perfil, per.nombre as permiso, modu.nombre as modulo ,det.ruta FROM modulo modu, modulo_detalle det, permiso per, perfil perf WHERE modu.id = det.modulo_id and det.permiso_id = per.id AND det.perfil_id = perf.id and det.perfil_id = :perfil: ";
        $modulos = $db->query($sql, ['perfil' => $perfil])->getResult('array');
        return $modulos;   
    }

    public function getPermisos2($perfilId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('perfil_modulo');
        $builder->select('modulo.nombre as modulo_nombre, modulo.ruta as modulo_ruta, perfil_modulo.ver, perfil_modulo.editar, perfil_modulo.eliminar');
        $builder->join('modulo', 'perfil_modulo.modulo_id = modulo.id');
        $builder->where('perfil_modulo.perfil_id', $perfilId);
        $builder->orderBy('perfil_modulo.orden', 'asc');
        $query = $builder->get();
        return $query->getResult('array');
    }
 
    public function getMenu($perfil){
        $db = \Config\Database::connect();
        $sql = "select pe.modulo_id id, mo.nombre, mo.ruta, pe.ver, pe.registrar, pe.editar, pe.eliminar from perfil_modulo pe, perfil per, modulo mo where pe.perfil_id = per.id and pe.modulo_id = mo.id and pe.perfil_id = :perfil: order by pe.orden asc";
        $modulos = $db->query($sql, ['perfil' => $perfil])->getResult('array');
        return $modulos;   
    }

    public function getSubMenu($modulo){
        $db = \Config\Database::connect();
        $sql = "select * from modulo_detalle where modulo_id = :modulo: order by orden asc";
        $modulos = $db->query($sql, ['modulo' => $modulo])->getResult('array');
        return $modulos;   
    }
}
