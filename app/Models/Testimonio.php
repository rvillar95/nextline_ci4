<?php

namespace App\Models;

use CodeIgniter\Model;

class Testimonio extends Model
{
    protected $table = 'testimonios';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nombre', 'cargo', 'empresa', 'testimonio', 'calificacion', 
        'proyecto_id', 'servicio_id', 'imagen', 'estado', 'destacado',
        'fecha_proyecto', 'slug', 'meta_titulo', 'meta_descripcion', 'meta_keywords'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';
    protected $deletedField = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'cargo' => 'permit_empty|string|max_length[100]',
        'empresa' => 'permit_empty|string|max_length[100]',
        'testimonio' => 'required|string|max_length[2000]',
        'calificacion' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'proyecto_id' => 'permit_empty|integer',
        'servicio_id' => 'permit_empty|integer',
        'imagen' => 'permit_empty|string|max_length[255]',
        'estado' => 'required|in_list[A,I]',
        'destacado' => 'required|in_list[S,N]',
        'fecha_proyecto' => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El campo nombre es obligatorio.',
            'string' => 'El campo nombre debe ser una cadena de texto.',
            'max_length' => 'El campo nombre no puede exceder de 100 caracteres.'
        ],
        'testimonio' => [
            'required' => 'El campo testimonio es obligatorio.',
            'string' => 'El campo testimonio debe ser una cadena de texto.',
            'max_length' => 'El campo testimonio no puede exceder de 2000 caracteres.'
        ],
        'calificacion' => [
            'required' => 'El campo calificación es obligatorio.',
            'integer' => 'El campo calificación debe ser un número entero.',
            'greater_than_equal_to' => 'La calificación debe ser entre 1 y 5.',
            'less_than_equal_to' => 'La calificación debe ser entre 1 y 5.'
        ],
        'estado' => [
            'required' => 'El campo estado es obligatorio.',
            'in_list' => 'El campo estado debe ser A o I.'
        ],
        'destacado' => [
            'required' => 'El campo destacado es obligatorio.',
            'in_list' => 'El campo destacado debe ser S o N.'
        ]
    ];

    protected $skipValidation = true;
    protected $cleanValidationRules = true;

    /**
     * Generar slug único para el testimonio
     */
    public function generarSlug($nombre, $id = null)
    {
        $slug = strtolower(trim($nombre));
        $slug = strtr($slug, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ñ' => 'n', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ä' => 'a',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u'
        ]);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Verificar unicidad
        $originalSlug = $slug;
        $counter = 1;
        
        do {
            $where = ['slug' => $slug];
            if ($id) {
                $where['id !='] = $id;
            }
            
            $exists = $this->where($where)->first();
            if (!$exists) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        } while ($counter < 100);

        return $slug;
    }

    /**
     * Generar campos SEO automáticamente
     */
    public function generarSEO($nombre, $testimonio, $empresa = null)
    {
        $metaTitulo = "Testimonio de " . $nombre;
        if ($empresa) {
            $metaTitulo .= " - " . $empresa;
        }
        
        $metaDescripcion = substr(strip_tags($testimonio), 0, 150);
        if (strlen($testimonio) > 150) {
            $metaDescripcion .= "...";
        }
        
        $metaKeywords = "testimonio, " . strtolower($nombre);
        if ($empresa) {
            $metaKeywords .= ", " . strtolower($empresa);
        }
        $metaKeywords .= ", construcción, cliente satisfecho";

        return [
            'meta_titulo' => $metaTitulo,
            'meta_descripcion' => $metaDescripcion,
            'meta_keywords' => $metaKeywords
        ];
    }

    /**
     * Obtener testimonios activos
     */
    public function getTestimoniosActivos($limit = null)
    {
        $query = $this->where('estado', 'A')->orderBy('destacado', 'DESC')->orderBy('fcreacion', 'DESC');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->findAll();
    }

    /**
     * Obtener testimonios destacados
     */
    public function getTestimoniosDestacados($limit = 3)
    {
        return $this->where('estado', 'A')
                   ->where('destacado', 'S')
                   ->orderBy('fcreacion', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Obtener testimonios por proyecto
     */
    public function getTestimoniosPorProyecto($proyectoId)
    {
        return $this->where('estado', 'A')
                   ->where('proyecto_id', $proyectoId)
                   ->orderBy('fcreacion', 'DESC')
                   ->findAll();
    }

    /**
     * Obtener testimonios por servicio
     */
    public function getTestimoniosPorServicio($servicioId)
    {
        return $this->where('estado', 'A')
                   ->where('servicio_id', $servicioId)
                   ->orderBy('fcreacion', 'DESC')
                   ->findAll();
    }

    /**
     * Obtener estadísticas de testimonios
     */
    public function getEstadisticasTestimonios()
    {
        $total = $this->where('estado', 'A')->countAllResults();
        $destacados = $this->where('estado', 'A')->where('destacado', 'S')->countAllResults();
        $promedio = $this->selectAvg('calificacion')->where('estado', 'A')->first();
        
        return [
            'total' => $total,
            'destacados' => $destacados,
            'promedio_calificacion' => round($promedio['calificacion'] ?? 0, 1)
        ];
    }
}
