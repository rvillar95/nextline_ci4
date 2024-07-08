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

    protected $allowedFields = ['id','modulo_id','permiso_id','perfil_id','descripcion','ruta','orden'];

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
        $sql = "SELECT perf.nombre as perfil, per.nombre as permiso, modu.nombre as modulo ,det.ruta FROM modulo modu, modulo_detalle det, permiso per, perfil perf WHERE modu.id = det.modulo_id and det.permiso_id = per.id AND det.perfil_id = perf.id and det.perfil_id = :perfil: group by det.modulo_id order by det.orden asc;";
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
 
}
