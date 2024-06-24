<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuario';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['id','nombre', 'apellido', 'correo', 'telefono', 'clave', 'perfil_id', 'estado', 'fcreacion', 'factualizacion', 'feliminacion'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'apellido' => 'required|string|max_length[100]',
        'correo' => 'required|valid_email|max_length[100]',
        'telefono' => 'required|string|max_length[100]',
        'clave' => 'required|string|max_length[100]',
        'perfil_id' => 'required|integer',
        'estado' => 'required|in_list[A,I]',
    ];

    public function validateUser2($correo, $clave){
        $user = $this->where(['correo' => $correo, 'estado' => 'A'])->first();
        if ($user && password_verify($clave, $user['clave'])) {
            return $user;
        }
        return null;
    }

    public function validateUser($correo, $clave)
    {
        // Obtén la instancia de la base de datos
        $db = \Config\Database::connect();

        $sql = "select u.*, f.nombre as perfil_nombre from usuario u, perfil f 
                         where u.perfil_id = f.id and 
                               u.correo = :correo: and
                               u.clave = :clave: and
                               u.estado = 'A'";

        // Ejecuta la consulta con los parámetros
        $query = $db->query($sql, ['correo' => $correo, 'clave' => $clave]);

        // Devuelve el resultado
        return $query->getResult('array');
    }
}
