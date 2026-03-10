<?php

namespace App\Models;

use CodeIgniter\Model;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'cliente_id', 'numero_cotizacion', 'proyecto_nombre', 'proyecto_descripcion', 'proyecto_tipo',
        'proyecto_area', 'proyecto_ubicacion', 'proyecto_direccion', 'fecha_cotizacion', 'fecha_validez',
        'vigencia_dias', 'estado', 'prioridad', 'subtotal_materiales', 'subtotal_mano_obra', 'subtotal_servicios',
        'descuento_porcentaje', 'descuento_monto', 'subtotal_sin_iva', 'iva_porcentaje', 'iva_monto', 'total_general',
        'forma_pago', 'plazo_pago_dias', 'anticipo_porcentaje', 'anticipo_monto', 'tiempo_ejecucion_dias',
        'fecha_inicio_estimada', 'fecha_fin_estimada', 'condiciones_generales', 'observaciones_especiales',
        'garantia_meses', 'slug', 'meta_titulo', 'meta_descripcion', 'creado_por', 'subtotal_equipos', 'subtotal_otros'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';
    protected $deletedField = 'feliminacion';

    protected $validationRules = [
        'cliente_id' => 'required|integer|is_natural_no_zero',
        'numero_cotizacion' => 'permit_empty|string|max_length[50]|is_unique[cotizaciones.numero_cotizacion,id,{id}]',
        'proyecto_nombre' => 'required|string|max_length[200]',
        'proyecto_descripcion' => 'permit_empty|string|max_length[20000]',
        'proyecto_tipo' => 'required|in_list[residencial,comercial,industrial,mantenimiento,otro]',
        'proyecto_area' => 'permit_empty|decimal',
        'fecha_cotizacion' => 'required|valid_date',
        'fecha_validez' => 'permit_empty|valid_date',
        'vigencia_dias' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[365]',
        'estado' => 'required|in_list[borrador,enviada,revisada,aprobada,rechazada,expirada]',
        'prioridad' => 'required|in_list[baja,media,alta,urgente]',
        'forma_pago' => 'required|in_list[contado,credito,mixto]',
        'iva_porcentaje' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'garantia_meses' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[120]'
    ];

    protected $validationMessages = [
        'cliente_id' => [
            'required' => 'El cliente es obligatorio.',
            'integer' => 'El cliente debe ser un número válido.',
            'is_natural_no_zero' => 'El cliente debe ser un número válido.'
        ],
        'numero_cotizacion' => [
            'is_unique' => 'El número de cotización ya existe.',
            'max_length' => 'El número de cotización no puede exceder de 50 caracteres.'
        ],
        'proyecto_nombre' => [
            'required' => 'El nombre del proyecto es obligatorio.',
            'string' => 'El nombre del proyecto debe ser una cadena de texto.',
            'max_length' => 'El nombre del proyecto no puede exceder de 200 caracteres.'
        ],
        'proyecto_tipo' => [
            'required' => 'El tipo de proyecto es obligatorio.',
            'in_list' => 'El tipo de proyecto debe ser: residencial, comercial, industrial, mantenimiento u otro.'
        ],
        'fecha_cotizacion' => [
            'required' => 'La fecha de cotización es obligatoria.',
            'valid_date' => 'La fecha de cotización debe ser válida.'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio.',
            'in_list' => 'El estado debe ser: borrador, enviada, revisada, aprobada, rechazada o expirada.'
        ],
        'prioridad' => [
            'required' => 'La prioridad es obligatoria.',
            'in_list' => 'La prioridad debe ser: baja, media, alta o urgente.'
        ],
        'forma_pago' => [
            'required' => 'La forma de pago es obligatoria.',
            'in_list' => 'La forma de pago debe ser: contado, crédito o mixto.'
        ]
    ];

    protected $skipValidation = true;
    protected $cleanValidationRules = true;

    /**
     * Generar número de cotización único
     */
    public function generarNumeroCotizacion()
    {
        $year = date('Y');
        $prefix = "COT-{$year}-";
        
        // Obtener el último número del año
        $lastCotizacion = $this->where('numero_cotizacion LIKE', $prefix . '%')
                              ->orderBy('numero_cotizacion', 'DESC')
                              ->first();
        
        if ($lastCotizacion) {
            // Extraer el número del último
            $lastNumber = (int) str_replace($prefix, '', $lastCotizacion->numero_cotizacion);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generar slug único para la cotización
     */
    public function generarSlug($titulo, $excludeId = null)
    {
        // Reemplazar acentos y caracteres especiales
        $slug = strtr($titulo, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ñ' => 'n', 'Ñ' => 'N', 'ü' => 'u', 'Ü' => 'U'
        ]);
        
        // Convertir a minúsculas y reemplazar espacios y caracteres especiales
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Verificar unicidad
        $originalSlug = $slug;
        $counter = 1;
        
        do {
            $query = $this->where('slug', $slug);
            if ($excludeId) {
                $query->where('id !=', $excludeId);
            }
            $exists = $query->first();
            
            if ($exists) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        } while ($exists);
        
        return $slug;
    }

    /**
     * Obtener cotización completa con cliente e items
     */
    public function getCotizacionCompleta($id)
    {
        $cotizacion = $this->select('cotizaciones.*, c.nombre_razon_social as cliente_nombre, c.tipo_cliente, c.contacto_nombre, c.telefono, c.email, c.direccion, c.comuna, c.region')
                          ->join('clientes c', 'c.id = cotizaciones.cliente_id', 'left')
                          ->where('cotizaciones.id', $id)
                          ->first();
        
        if (!$cotizacion) {
            return null;
        }
        
        // Obtener items
        $cotizacionItem = new CotizacionItem();
        $cotizacion->items = $cotizacionItem->where('cotizacion_id', $id)
                                          ->orderBy('orden', 'ASC')
                                          ->findAll();
        
        // Obtener archivos
        $cotizacionArchivo = new CotizacionArchivo();
        $cotizacion->archivos = $cotizacionArchivo->where('cotizacion_id', $id)
                                                 ->orderBy('es_principal', 'DESC')
                                                 ->orderBy('fcreacion', 'ASC')
                                                 ->findAll();
        
        return $cotizacion;
    }

    /**
     * Obtener estadísticas de cotizaciones
     */
    public function getEstadisticasCotizaciones()
    {
        $stats = [];
        
        $stats['total'] = $this->where('estado', 'A')->countAllResults();
        $stats['borrador'] = $this->where('estado', 'A')->where('estado_cotizacion', 'borrador')->countAllResults();
        $stats['enviada'] = $this->where('estado', 'A')->where('estado_cotizacion', 'enviada')->countAllResults();
        $stats['aceptada'] = $this->where('estado', 'A')->where('estado_cotizacion', 'aceptada')->countAllResults();
        $stats['rechazada'] = $this->where('estado', 'A')->where('estado_cotizacion', 'rechazada')->countAllResults();
        $stats['expirada'] = $this->where('estado', 'A')->where('estado_cotizacion', 'expirada')->countAllResults();
        
        // Calcular totales por estado
        $stats['total_valor_borrador'] = $this->where('estado', 'A')->where('estado_cotizacion', 'borrador')->selectSum('total_general')->first()->total_general ?? 0;
        $stats['total_valor_enviada'] = $this->where('estado', 'A')->where('estado_cotizacion', 'enviada')->selectSum('total_general')->first()->total_general ?? 0;
        $stats['total_valor_aceptada'] = $this->where('estado', 'A')->where('estado_cotizacion', 'aceptada')->selectSum('total_general')->first()->total_general ?? 0;
        
        return $stats;
    }

    /**
     * Obtener cotizaciones con información del cliente
     */
    public function getCotizacionesConCliente($limit = null, $offset = 0)
    {
        $builder = $this->select('cotizaciones.*, clientes.nombre_razon_social, clientes.tipo_cliente, clientes.contacto_nombre')
                       ->join('clientes', 'clientes.id = cotizaciones.cliente_id', 'left')
                       ->orderBy('cotizaciones.fecha_cotizacion', 'DESC')
                       ->orderBy('cotizaciones.id', 'DESC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }



    /**
     * Calcular totales de la cotización
     */
    public function calcularTotales($cotizacionId)
    {
        $itemModel = new CotizacionItem();
        $items = $itemModel->where('cotizacion_id', $cotizacionId)->findAll();

        $subtotal_materiales = 0;
        $subtotal_mano_obra = 0;
        $subtotal_servicios = 0;

        foreach ($items as $item) {
            switch ($item->categoria) {
                case 'material':
                    $subtotal_materiales += $item->subtotal;
                    break;
                case 'mano_obra':
                    $subtotal_mano_obra += $item->subtotal;
                    break;
                case 'servicio':
                case 'equipo':
                case 'transporte':
                case 'otros':
                    $subtotal_servicios += $item->subtotal;
                    break;
            }
        }

        $subtotal_general = $subtotal_materiales + $subtotal_mano_obra + $subtotal_servicios;

        // Obtener descuentos e IVA de la cotización
        $cotizacion = $this->find($cotizacionId);
        $descuento_monto = $cotizacion->descuento_monto ?? 0;
        $descuento_porcentaje = $cotizacion->descuento_porcentaje ?? 0;
        
        // Calcular descuento por porcentaje
        if ($descuento_porcentaje > 0) {
            $descuento_monto += ($subtotal_general * $descuento_porcentaje / 100);
        }

        $subtotal_con_descuento = $subtotal_general - $descuento_monto;
        $iva_porcentaje = $cotizacion->iva_porcentaje ?? 19;
        $iva_monto = $subtotal_con_descuento * $iva_porcentaje / 100;
        $total_general = $subtotal_con_descuento + $iva_monto;

        // Actualizar la cotización
        $this->update($cotizacionId, [
            'subtotal_materiales' => $subtotal_materiales,
            'subtotal_mano_obra' => $subtotal_mano_obra,
            'subtotal_servicios' => $subtotal_servicios,
            'descuento_monto' => $descuento_monto,
            'iva_monto' => $iva_monto,
            'total_general' => $total_general
        ]);

        return [
            'subtotal_materiales' => $subtotal_materiales,
            'subtotal_mano_obra' => $subtotal_mano_obra,
            'subtotal_servicios' => $subtotal_servicios,
            'descuento_monto' => $descuento_monto,
            'iva_monto' => $iva_monto,
            'total_general' => $total_general
        ];
    }


    /**
     * Generar campos SEO automáticamente
     */
    public function generarSEO($proyectoNombre, $clienteNombre, $proyectoDescripcion = null)
    {
        $metaTitulo = "Cotización: {$proyectoNombre} - {$clienteNombre}";
        
        $metaDescripcion = "Cotización detallada para el proyecto {$proyectoNombre}";
        if ($proyectoDescripcion) {
            $metaDescripcion .= ". " . substr(strip_tags($proyectoDescripcion), 0, 100);
        }
        
        $metaKeywords = "cotización, presupuesto, construcción, {$proyectoNombre}, {$clienteNombre}";

        return [
            'meta_titulo' => $metaTitulo,
            'meta_descripcion' => $metaDescripcion,
            'meta_keywords' => $metaKeywords
        ];
    }
}
