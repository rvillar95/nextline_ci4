<?php

namespace App\Models;

use CodeIgniter\Model;

class Modulo extends Model
{
    protected $table      = 'modulo';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id','nombre','descripcion','sa','ruta','estado','mostrar'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
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
        $poder = session()->get('usuario')['poder'];
        if ($poder <= 2) {
            $sql = "select * from modulo where id != 13";
        }else{
            $sql = "select * from modulo";
        }
   
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
        $poder = session()->get('usuario')['poder'];
        
        if ($poder <= 2) {
            $sql = "select * from modulo where estado = 'A' and sa = 'N' ";
        }else{
            $sql = "select * from modulo where estado = 'A'";
        }
        $perfil = $db->query($sql)->getResult('array');
        return $perfil;   
    }

    /**
     * Obtiene los módulos activos filtrados por el paquete de la empresa activa del sistema
     * 
     * @param int $poderUsuario El poder del usuario actual
     * @return array Lista de módulos disponibles
     */
    public function getModulosByPaqueteEmpresa(int $poderUsuario): array
    {
        $db = \Config\Database::connect();
        
        // Super Admin (poder >= 3) ve todos los módulos activos
        if ($poderUsuario >= 3) {
            $sql = "SELECT * FROM modulo WHERE estado = 'A' ORDER BY nombre";
            return $db->query($sql)->getResultArray();
        }
        
        // Admin y otros usuarios ven solo módulos del paquete de la empresa activa
        // Obtener el paquete de la única empresa activa del sistema
        $sql = "
            SELECT DISTINCT m.*
            FROM modulo m
            INNER JOIN paquete_modulo pm ON m.id = pm.modulo_id
            INNER JOIN empresa e ON e.paquete_id = pm.paquete_id
            WHERE e.estado = 'A'
              AND m.estado = 'A'
              AND m.sa = 'N'
            ORDER BY m.nombre
            LIMIT 100
        ";
        
        return $db->query($sql)->getResultArray();
    }

}
