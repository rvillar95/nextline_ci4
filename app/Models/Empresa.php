<?php

namespace App\Models;

use CodeIgniter\Model;

class Empresa extends Model
{
    protected $table = 'empresa';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nombre',
        'nombre_comercial',
        'rut',
        'direccion',
        'telefono',
        'email',
        'sitio_web',
        'logo_path',
        'descripcion',
        'mision',
        'vision',
        'valores',
        'estado',
        'paquete_id'  // Agregado para gestión de paquetes
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'fmodificacion';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[255]',
        'email' => 'required|valid_email',
        'telefono' => 'required|min_length[8]|max_length[50]',
        'direccion' => 'required|min_length[10]|max_length[500]'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre de la empresa es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder 255 caracteres'
        ],
        'email' => [
            'required' => 'El email es obligatorio',
            'valid_email' => 'Debe proporcionar un email válido'
        ],
        'telefono' => [
            'required' => 'El teléfono es obligatorio',
            'min_length' => 'El teléfono debe tener al menos 8 caracteres',
            'max_length' => 'El teléfono no puede exceder 50 caracteres'
        ],
        'direccion' => [
            'required' => 'La dirección es obligatoria',
            'min_length' => 'La dirección debe tener al menos 10 caracteres',
            'max_length' => 'La dirección no puede exceder 500 caracteres'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Obtener datos de la empresa activa
     */
    public function getEmpresaActiva()
    {
        return $this->where('estado', 'A')->first();
    }

    /**
     * Obtener datos de la empresa por ID
     */
    public function getEmpresaById($id)
    {
        return $this->find($id);
    }

    /**
     * Actualizar datos de la empresa
     */
    public function actualizarEmpresa($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Obtener logo de la empresa
     */
    public function getLogoPath()
    {
        $empresa = $this->getEmpresaActiva();
        return $empresa ? $empresa->logo_path : null;
    }

    /**
     * Obtener información completa de la empresa para PDF
     */
    public function getDatosParaPDF()
    {
        $empresa = $this->getEmpresaActiva();
        
        if (!$empresa) {
            return [
                'nombre' => 'MANSANCHEZ',
                'nombre_comercial' => 'MANSANCHEZ Construcciones',
                'direccion' => 'Santiago, Chile',
                'telefono' => '+56 9 1234 5678',
                'email' => 'info@mansanchez.cl',
                'sitio_web' => 'www.mansanchez.cl',
                'logo_path' => 'lib/images/logo-min.jpg',
                'descripcion' => 'Construcciones y Remodelaciones'
            ];
        }

        return [
            'nombre' => $empresa->nombre,
            'nombre_comercial' => $empresa->nombre_comercial ?? $empresa->nombre,
            'direccion' => $empresa->direccion,
            'telefono' => $empresa->telefono,
            'email' => $empresa->email,
            'sitio_web' => $empresa->sitio_web,
            'logo_path' => $empresa->logo_path,
            'descripcion' => $empresa->descripcion ?? 'Construcciones y Remodelaciones'
        ];
    }

    /**
     * Obtener empresa por ID del usuario
     */
    public function getEmpresaByUsuarioId($usuarioId)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT e.* 
                FROM empresa e
                INNER JOIN usuario u ON u.empresa_id = e.id
                WHERE u.id = :usuario_id:
                LIMIT 1";
        $result = $db->query($sql, ['usuario_id' => $usuarioId])->getRow();
        return $result;
    }

    /**
     * Obtener todas las empresas (para Super Admin)
     * Con información del paquete
     */
    public function getEmpresasCompletas($filtros = [])
    {
        $db = \Config\Database::connect();
        
        $sql = "SELECT 
                    e.id,
                    e.nombre,
                    e.nombre_comercial,
                    e.rut,
                    e.email,
                    e.telefono,
                    e.estado,
                    e.paquete_id,
                    p.nombre AS paquete_nombre,
                    p.slug AS paquete_slug,
                    COUNT(DISTINCT u.id) AS cantidad_usuarios,
                    e.fcreacion,
                    e.fmodificacion
                FROM empresa e
                LEFT JOIN paquetes p ON p.id = e.paquete_id
                LEFT JOIN usuario u ON u.empresa_id = e.id AND u.estado = 'A'
                WHERE 1=1";
        
        $params = [];
        
        // Filtros
        if (!empty($filtros['estado'])) {
            $sql .= " AND e.estado = :estado:";
            $params['estado'] = $filtros['estado'];
        }
        
        if (!empty($filtros['paquete_id'])) {
            $sql .= " AND e.paquete_id = :paquete_id:";
            $params['paquete_id'] = $filtros['paquete_id'];
        }
        
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (e.nombre LIKE :busqueda: OR e.email LIKE :busqueda: OR e.rut LIKE :busqueda:)";
            $params['busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        
        $sql .= " GROUP BY e.id, e.nombre, e.nombre_comercial, e.rut, e.email, e.telefono, e.estado, e.paquete_id, p.nombre, p.slug, e.fcreacion, e.fmodificacion";
        $sql .= " ORDER BY e.fcreacion DESC";
        
        return $db->query($sql, $params)->getResult('object');
    }
}
