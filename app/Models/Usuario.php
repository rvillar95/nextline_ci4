<?php

namespace App\Models;

use CodeIgniter\Model;

class Usuario extends Model
{
    protected $table      = 'usuario';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['id', 'nombre', 'apellido', 'correo', 'telefono', 'clave', 'perfil_id', 'estado', 'fcreacion', 'factualizacion', 'feliminacion', 'perfil_nombre'];

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

    public function validateUser($correo, $clave)
    {
        $db = \Config\Database::connect();
        $sql = "select u.*, f.nombre as perfil_nombre from usuario u, perfil f 
                         where u.perfil_id = f.id and 
                               u.correo = :correo: and
                               u.estado = 'A' ";

        // Ejecuta la consulta con los parámetros
        $user = $db->query($sql, ['correo' => $correo, 'clave' => $clave])->getResult('array');
        if ($user && password_verify($clave, $user[0]['clave'])) {
            return $user[0];
        }
        return null;
    }

    public function contrasenaHash($contrasenaHash)
    {
        return password_hash($contrasenaHash, PASSWORD_DEFAULT);
    }
}
