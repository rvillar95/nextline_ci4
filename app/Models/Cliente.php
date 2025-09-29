<?php

namespace App\Models;

use CodeIgniter\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tipo_cliente', 'nombre_razon_social', 'rut_dni', 'contacto_nombre', 'contacto_cargo',
        'telefono', 'email', 'direccion', 'region_id', 'comuna_id', 'sitio_web', 'observaciones', 'estado'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'tipo_cliente' => 'required|in_list[particular,empresa,organizacion]',
        'nombre_razon_social' => 'required|string|max_length[200]',
        'rut_dni' => 'permit_empty|chilean_rut',
        'contacto_nombre' => 'permit_empty|string|max_length[100]',
        'contacto_cargo' => 'permit_empty|string|max_length[100]',
        'telefono' => 'permit_empty|chilean_phone',
        'email' => 'permit_empty|valid_email|max_length[150]',
        'direccion' => 'permit_empty|string|max_length[300]',
        'region_id' => 'permit_empty|integer|greater_than[0]',
        'comuna_id' => 'permit_empty|integer|greater_than[0]',
        'sitio_web' => 'permit_empty|valid_url|max_length[200]',
        'estado' => 'required|in_list[A,I]'
    ];

    protected $validationMessages = [
        'tipo_cliente' => [
            'required' => 'El tipo de cliente es obligatorio.',
            'in_list' => 'El tipo de cliente debe ser: particular, empresa u organización.'
        ],
        'nombre_razon_social' => [
            'required' => 'El nombre o razón social es obligatorio.',
            'string' => 'El nombre debe ser una cadena de texto.',
            'max_length' => 'El nombre no puede exceder de 200 caracteres.'
        ],
        'rut_dni' => [
            'chilean_rut' => 'El RUT ingresado no es válido. Formato: 12.345.678-9'
        ],
        'telefono' => [
            'chilean_phone' => 'El teléfono ingresado no es válido. Formato: +56912345678'
        ],
        'email' => [
            'valid_email' => 'El correo electrónico debe ser válido.',
            'max_length' => 'El correo no puede exceder de 150 caracteres.'
        ],
        'region_id' => [
            'integer' => 'La región debe ser un número válido.',
            'greater_than' => 'Debe seleccionar una región válida.'
        ],
        'comuna_id' => [
            'integer' => 'La comuna debe ser un número válido.',
            'greater_than' => 'Debe seleccionar una comuna válida.'
        ],
        'sitio_web' => [
            'valid_url' => 'El sitio web debe ser una URL válida.',
            'max_length' => 'El sitio web no puede exceder de 200 caracteres.'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio.',
            'in_list' => 'El estado debe ser A (Activo) o I (Inactivo).'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener clientes activos
     */
    public function getClientesActivos()
    {
        $clientes = $this->where('estado', 'A')
                   ->orderBy('tipo_cliente', 'ASC')
                   ->orderBy('nombre_razon_social', 'ASC')
                   ->findAll();
        
        // Asegurar que cada cliente tenga los métodos necesarios
        foreach ($clientes as $cliente) {
            $cliente->display_name = $this->getDisplayName($cliente);
            $cliente->contacto_completo = $this->getContactoCompleto($cliente);
        }
        
        return $clientes;
    }

    /**
     * Obtener clientes por tipo
     */
    public function getClientesPorTipo($tipo)
    {
        return $this->where('estado', 'A')
                   ->where('tipo_cliente', $tipo)
                   ->orderBy('nombre_razon_social', 'ASC')
                   ->findAll();
    }

    /**
     * Buscar clientes por nombre o razón social
     */
    public function buscarClientes($termino)
    {
        return $this->where('estado', 'A')
                   ->groupStart()
                   ->like('nombre_razon_social', $termino)
                   ->orLike('contacto_nombre', $termino)
                   ->orLike('rut_dni', $termino)
                   ->groupEnd()
                   ->orderBy('nombre_razon_social', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener información completa del cliente para cotizaciones
     */
    public function getClienteParaCotizacion($id)
    {
        $cliente = $this->find($id);
        
        if (!$cliente) {
            return null;
        }

        // Agregar información adicional según el tipo
        $cliente->display_name = $this->getDisplayName($cliente);
        $cliente->contacto_completo = $this->getContactoCompleto($cliente);
        
        return $cliente;
    }

    /**
     * Obtener nombre para mostrar
     */
    public function getDisplayName($cliente)
    {
        switch ($cliente->tipo_cliente) {
            case 'empresa':
                return $cliente->nombre_razon_social . 
                       ($cliente->contacto_nombre ? " (Contacto: {$cliente->contacto_nombre})" : '');
            case 'organizacion':
                return $cliente->nombre_razon_social . 
                       ($cliente->contacto_nombre ? " (Contacto: {$cliente->contacto_nombre})" : '');
            default:
                return $cliente->nombre_razon_social;
        }
    }

    /**
     * Obtener información de contacto completa
     */
    public function getContactoCompleto($cliente)
    {
        $contacto = [];
        
        if ($cliente->telefono) {
            $contacto[] = "Tel: {$cliente->telefono}";
        }
        
        if ($cliente->email) {
            $contacto[] = "Email: {$cliente->email}";
        }
        
        if ($cliente->direccion) {
            $contacto[] = "Dirección: {$cliente->direccion}";
        }
        
        if ($cliente->comuna && $cliente->region) {
            $contacto[] = "{$cliente->comuna}, {$cliente->region}";
        }
        
        return implode(' | ', $contacto);
    }

    /**
     * Obtener estadísticas de clientes
     */
    public function getEstadisticasClientes()
    {
        $stats = [];
        
        $stats['total'] = $this->where('estado', 'A')->countAllResults();
        $stats['particulares'] = $this->where('estado', 'A')->where('tipo_cliente', 'particular')->countAllResults();
        $stats['empresas'] = $this->where('estado', 'A')->where('tipo_cliente', 'empresa')->countAllResults();
        $stats['organizaciones'] = $this->where('estado', 'A')->where('tipo_cliente', 'organizacion')->countAllResults();
        
        return $stats;
    }

}
