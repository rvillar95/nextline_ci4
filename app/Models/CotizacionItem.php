<?php

namespace App\Models;

use CodeIgniter\Model;

class CotizacionItem extends Model
{
    protected $table = 'cotizacion_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'cotizacion_id', 'categoria', 'subcategoria', 'codigo_item', 'descripcion', 'especificaciones',
        'cantidad', 'unidad', 'precio_unitario', 'descuento_porcentaje', 'descuento_monto',
        'subtotal', 'orden', 'es_opcional', 'observaciones'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = null; // No hay campo de actualización

    protected $validationRules = [
        'cotizacion_id' => 'required|integer|is_natural_no_zero',
        'categoria' => 'required|in_list[material,mano_obra,servicio,equipo,transporte,otros]',
        'subcategoria' => 'permit_empty|string|max_length[100]',
        'codigo_item' => 'permit_empty|string|max_length[50]',
        'descripcion' => 'required|string',
        'especificaciones' => 'permit_empty|string',
        'cantidad' => 'required|decimal|greater_than[0]',
        'unidad' => 'required|string|max_length[50]',
        'precio_unitario' => 'required|decimal|greater_than_equal_to[0]',
        'descuento_porcentaje' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'descuento_monto' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'orden' => 'permit_empty|integer|greater_than_equal_to[0]',
        'es_opcional' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'cotizacion_id' => [
            'required' => 'La cotización es obligatoria.',
            'integer' => 'La cotización debe ser un número válido.',
            'is_natural_no_zero' => 'La cotización debe ser un número válido.'
        ],
        'categoria' => [
            'required' => 'La categoría es obligatoria.',
            'in_list' => 'La categoría debe ser: material, mano_obra, servicio, equipo, transporte u otros.'
        ],
        'descripcion' => [
            'required' => 'La descripción es obligatoria.',
            'string' => 'La descripción debe ser una cadena de texto.'
        ],
        'cantidad' => [
            'required' => 'La cantidad es obligatoria.',
            'decimal' => 'La cantidad debe ser un número decimal.',
            'greater_than' => 'La cantidad debe ser mayor a 0.'
        ],
        'unidad' => [
            'required' => 'La unidad es obligatoria.',
            'string' => 'La unidad debe ser una cadena de texto.',
            'max_length' => 'La unidad no puede exceder de 50 caracteres.'
        ],
        'precio_unitario' => [
            'required' => 'El precio unitario es obligatorio.',
            'decimal' => 'El precio unitario debe ser un número decimal.',
            'greater_than_equal_to' => 'El precio unitario debe ser mayor o igual a 0.'
        ]
    ];

    protected $skipValidation = true;
    protected $cleanValidationRules = true;

    /**
     * Obtener items de una cotización agrupados por categoría
     */
    public function getItemsPorCotizacion($cotizacionId)
    {
        return $this->where('cotizacion_id', $cotizacionId)
                   ->orderBy('categoria', 'ASC')
                   ->orderBy('subcategoria', 'ASC')
                   ->orderBy('orden', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener items agrupados por categoría
     */
    public function getItemsAgrupadosPorCategoria($cotizacionId)
    {
        $items = $this->getItemsPorCotizacion($cotizacionId);
        
        $agrupados = [];
        foreach ($items as $item) {
            $categoria = $item->categoria;
            if (!isset($agrupados[$categoria])) {
                $agrupados[$categoria] = [];
            }
            $agrupados[$categoria][] = $item;
        }
        
        return $agrupados;
    }

    /**
     * Calcular subtotal del item
     */
    public function calcularSubtotal($cantidad, $precioUnitario, $descuentoPorcentaje = 0, $descuentoMonto = 0)
    {
        $subtotal = $cantidad * $precioUnitario;
        
        // Aplicar descuento por porcentaje
        if ($descuentoPorcentaje > 0) {
            $descuentoMonto += ($subtotal * $descuentoPorcentaje / 100);
        }
        
        return $subtotal - $descuentoMonto;
    }

    /**
     * Guardar item con cálculo automático de subtotal
     */
    public function guardarItem($data)
    {
        // Calcular subtotal automáticamente
        $data['subtotal'] = $this->calcularSubtotal(
            $data['cantidad'],
            $data['precio_unitario'],
            $data['descuento_porcentaje'] ?? 0,
            $data['descuento_monto'] ?? 0
        );

        return $this->insert($data);
    }

    /**
     * Actualizar item con cálculo automático de subtotal
     */
    public function actualizarItem($id, $data)
    {
        // Calcular subtotal automáticamente
        $data['subtotal'] = $this->calcularSubtotal(
            $data['cantidad'],
            $data['precio_unitario'],
            $data['descuento_porcentaje'] ?? 0,
            $data['descuento_monto'] ?? 0
        );

        return $this->update($id, $data);
    }

    /**
     * Obtener totales por categoría para una cotización
     */
    public function getTotalesPorCategoria($cotizacionId)
    {
        $totales = [];
        
        $categorias = ['material', 'mano_obra', 'servicio', 'equipo', 'transporte', 'otros'];
        
        foreach ($categorias as $categoria) {
            $total = $this->selectSum('subtotal')
                         ->where('cotizacion_id', $cotizacionId)
                         ->where('categoria', $categoria)
                         ->first();
            
            $totales[$categoria] = $total->subtotal ?? 0;
        }
        
        return $totales;
    }

    /**
     * Obtener unidades más comunes
     */
    public function getUnidadesComunes()
    {
        return [
            'unidad' => 'Unidad',
            'm2' => 'Metro cuadrado (m²)',
            'm3' => 'Metro cúbico (m³)',
            'ml' => 'Metro lineal (ml)',
            'kg' => 'Kilogramo (kg)',
            'ton' => 'Tonelada (ton)',
            'hr' => 'Hora (hr)',
            'dia' => 'Día',
            'semana' => 'Semana',
            'mes' => 'Mes',
            'litro' => 'Litro',
            'galon' => 'Galón',
            'bolsa' => 'Bolsa',
            'saco' => 'Saco',
            'pieza' => 'Pieza',
            'juego' => 'Juego',
            'par' => 'Par',
            'docena' => 'Docena',
            'metro' => 'Metro (m)',
            'centimetro' => 'Centímetro (cm)'
        ];
    }

    /**
     * Obtener subcategorías comunes por tipo
     */
    public function getSubcategoriasComunes()
    {
        return [
            'material' => [
                'estructura' => 'Estructura',
                'acabados' => 'Acabados',
                'instalaciones' => 'Instalaciones',
                'herramientas' => 'Herramientas',
                'consumibles' => 'Consumibles',
                'otros' => 'Otros'
            ],
            'mano_obra' => [
                'estructura' => 'Estructura',
                'acabados' => 'Acabados',
                'instalaciones' => 'Instalaciones',
                'demolicion' => 'Demolición',
                'preparacion' => 'Preparación',
                'otros' => 'Otros'
            ],
            'servicio' => [
                'diseno' => 'Diseño',
                'supervision' => 'Supervisión',
                'consultoria' => 'Consultoría',
                'mantenimiento' => 'Mantenimiento',
                'otros' => 'Otros'
            ],
            'equipo' => [
                'maquinaria' => 'Maquinaria',
                'herramientas' => 'Herramientas',
                'equipos' => 'Equipos',
                'otros' => 'Otros'
            ],
            'transporte' => [
                'materiales' => 'Materiales',
                'equipos' => 'Equipos',
                'personal' => 'Personal',
                'otros' => 'Otros'
            ],
            'otros' => [
                'gastos' => 'Gastos Generales',
                'imprevistos' => 'Imprevistos',
                'otros' => 'Otros'
            ]
        ];
    }

    /**
     * Duplicar items de una cotización a otra
     */
    public function duplicarItems($cotizacionOrigenId, $cotizacionDestinoId)
    {
        $items = $this->where('cotizacion_id', $cotizacionOrigenId)->findAll();
        
        $itemsDuplicados = [];
        foreach ($items as $item) {
            $nuevoItem = [
                'cotizacion_id' => $cotizacionDestinoId,
                'categoria' => $item->categoria,
                'subcategoria' => $item->subcategoria,
                'codigo_item' => $item->codigo_item,
                'descripcion' => $item->descripcion,
                'especificaciones' => $item->especificaciones,
                'cantidad' => $item->cantidad,
                'unidad' => $item->unidad,
                'precio_unitario' => $item->precio_unitario,
                'descuento_porcentaje' => $item->descuento_porcentaje,
                'descuento_monto' => $item->descuento_monto,
                'subtotal' => $item->subtotal,
                'orden' => $item->orden,
                'es_opcional' => $item->es_opcional,
                'observaciones' => $item->observaciones
            ];
            
            $itemsDuplicados[] = $nuevoItem;
        }
        
        if (!empty($itemsDuplicados)) {
            return $this->insertBatch($itemsDuplicados);
        }
        
        return true;
    }
}
