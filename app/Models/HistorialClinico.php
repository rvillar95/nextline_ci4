<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialClinico extends Model
{
    protected $table = 'historial_clinico';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'paciente_id', 'nutricionista_id', 'agenda_id', 'detalle_agenda_id', 'tipo_registro', 'fecha_consulta', 'hora_consulta',
        'peso_actual', 'altura_actual', 'altura_sentado', 'imc_actual', 
        'circunferencia_cintura', 'circunferencia_cadera', 'circunferencia_brazo_relajado', 'circunferencia_brazo_contraido', 'circunferencia_antebrazo_maximo',
        'circunferencia_muslo_medio', 'circunferencia_muslo_maximo', 'circunferencia_pantorrilla', 'circunferencia_cuello', 'circunferencia_torax', 'circunferencia_cabeza',
        'diametro_biacromial', 'diametro_bi_iliocristal', 'diametro_torax_transverso', 'diametro_torax_anteroposterior', 'diametro_humero', 'diametro_femur', 'diametro_muneca', 'circunferencia_muneca', 'diametro_tobillo',
        'grasa_corporal', 'masa_muscular', 'masa_osea', 
        'pliegue_tricipital', 'pliegue_bicipital', 'pliegue_subescapular', 'pliegue_suprailíaco', 'pliegue_supraespinal',
        'pliegue_abdominal', 'pliegue_muslo_anterior', 'pliegue_pantorrilla_medial',
        'pliegue_pectoral', 'pliegue_axilar_medio', 'pliegue_muslo_medial',
        'suma_pliegues', 'grasa_corporal_calculada',
        'motivo_consulta', 'anamnesis', 'anamnesis_clinica', 'anamnesis_alimentaria',
        'tendencia_consumo', 'recordatorio_24h',
        'diagnostico', 'plan_tratamiento', 'recomendaciones', 'observaciones', 'tags', 'proxima_cita', 'estado'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';
    protected $deletedField = 'feliminacion';

    protected $validationRules = [
        'paciente_id' => 'required|integer|greater_than[0]',
        'tipo_registro' => 'required|in_list[consulta,seguimiento,control,emergencia]',
        'fecha_consulta' => 'required|valid_date',
        'estado' => 'required|in_list[A,I]'
    ];

    protected $validationMessages = [
        'paciente_id' => [
            'required' => 'El paciente es obligatorio.'
        ],
        'fecha_consulta' => [
            'required' => 'La fecha de consulta es obligatoria.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener historial completo con relaciones
     */
    public function getHistorialCompleto($id)
    {
        $historial = $this->find($id);
        if (!$historial) {
            return null;
        }

        // Cargar paciente
        $pacienteModel = new Paciente();
        $historial->paciente = $pacienteModel->find($historial->paciente_id);

        // Cargar nutricionista
        if ($historial->nutricionista_id) {
            $usuarioModel = new Usuario();
            $historial->nutricionista = $usuarioModel->find($historial->nutricionista_id);
        }

        return $historial;
    }

    /**
     * Campos del historial que se muestran como referencia en el formulario de consulta.
     */
    public static function camposReferenciaUltimaConsulta(): array
    {
        return [
            'peso_actual', 'altura_actual', 'altura_sentado', 'imc_actual',
            'circunferencia_cintura', 'circunferencia_cadera', 'circunferencia_brazo_relajado',
            'circunferencia_brazo_contraido', 'circunferencia_antebrazo_maximo', 'circunferencia_muslo_medio',
            'circunferencia_muslo_maximo', 'circunferencia_pantorrilla', 'circunferencia_cuello',
            'circunferencia_torax', 'circunferencia_cabeza', 'circunferencia_muneca',
            'diametro_biacromial', 'diametro_bi_iliocristal', 'diametro_torax_transverso',
            'diametro_torax_anteroposterior', 'diametro_humero', 'diametro_femur', 'diametro_muneca',
            'diametro_tobillo',
            'pliegue_tricipital', 'pliegue_bicipital', 'pliegue_subescapular', 'pliegue_suprailíaco',
            'pliegue_supraespinal', 'pliegue_abdominal', 'pliegue_muslo_anterior', 'pliegue_pantorrilla_medial',
            'pliegue_pectoral', 'pliegue_axilar_medio', 'pliegue_muslo_medial',
            'suma_pliegues', 'grasa_corporal', 'grasa_corporal_calculada', 'masa_muscular', 'masa_osea',
            'motivo_consulta', 'plan_tratamiento', 'recomendaciones', 'observaciones', 'diagnostico',
            'anamnesis_clinica', 'anamnesis_alimentaria', 'recordatorio_24h',
        ];
    }

    /**
     * Obtener historial por paciente
     */
    public function getHistorialPorPaciente($pacienteId, $limit = null)
    {
        $builder = $this->where('paciente_id', $pacienteId)
            ->where('estado', 'A')
            ->orderBy('fecha_consulta', 'DESC')
            ->orderBy('hora_consulta', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Calcular IMC automáticamente
     */
    public function calcularIMC($peso, $altura)
    {
        if ($peso > 0 && $altura > 0) {
            $alturaMetros = $altura / 100;
            return round($peso / ($alturaMetros * $alturaMetros), 2);
        }
        return null;
    }

    /**
     * Obtener última consulta de un paciente
     */
    public function getUltimaConsulta($pacienteId)
    {
        return $this->where('paciente_id', $pacienteId)
            ->where('estado', 'A')
            ->orderBy('fecha_consulta', 'DESC')
            ->orderBy('hora_consulta', 'DESC')
            ->first();
    }

    /**
     * Obtener evolución de peso de un paciente
     */
    public function getEvolucionPeso($pacienteId, $fechaInicio = null, $fechaFin = null)
    {
        $builder = $this->select('fecha_consulta, peso_actual, imc_actual')
            ->where('paciente_id', $pacienteId)
            ->where('estado', 'A')
            ->where('peso_actual IS NOT NULL')
            ->orderBy('fecha_consulta', 'ASC');

        if ($fechaInicio) {
            $builder->where('fecha_consulta >=', $fechaInicio);
        }
        if ($fechaFin) {
            $builder->where('fecha_consulta <=', $fechaFin);
        }

        return $builder->findAll();
    }

    /**
     * Normalizar tag (minúsculas, sin espacios extras)
     */
    public function normalizarTag($tag)
    {
        return strtolower(trim($tag));
    }

    /**
     * Procesar tags: convertir string a JSON y actualizar tabla historial_tags
     */
    public function procesarTags($tagsString, $empresaId = null)
    {
        if (empty($tagsString)) {
            return json_encode([]);
        }

        // Separar tags por comas
        $tagsArray = array_map('trim', explode(',', $tagsString));
        $tagsArray = array_filter($tagsArray, function($tag) {
            return !empty($tag);
        });

        // Normalizar tags
        $tagsNormalizados = array_map([$this, 'normalizarTag'], $tagsArray);
        $tagsUnicos = array_unique($tagsNormalizados);

        // Actualizar tabla historial_tags
        $db = \Config\Database::connect();
        foreach ($tagsUnicos as $index => $tagNormalizado) {
            $tagDisplay = $tagsArray[$index] ?? $tagNormalizado;
            
            // Buscar si existe
            $existe = $db->table('historial_tags')
                ->where('tag', $tagNormalizado)
                ->where('empresa_id', $empresaId)
                ->get()
                ->getRow();

            if ($existe) {
                // Incrementar usos
                $db->table('historial_tags')
                    ->where('id', $existe->id)
                    ->update([
                        'usos' => $existe->usos + 1,
                        'factualizacion' => date('Y-m-d H:i:s')
                    ]);
            } else {
                // Crear nuevo tag
                $db->table('historial_tags')->insert([
                    'tag' => $tagNormalizado,
                    'tag_display' => $tagDisplay,
                    'usos' => 1,
                    'empresa_id' => $empresaId,
                    'fcreacion' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Retornar JSON
        return json_encode(array_values($tagsUnicos));
    }

    /**
     * Obtener tags como string (para mostrar en formularios)
     */
    public function getTagsAsString($historialId)
    {
        $historial = $this->find($historialId);
        if (!$historial || empty($historial->tags)) {
            return '';
        }

        $tags = json_decode($historial->tags, true);
        if (!is_array($tags)) {
            return '';
        }

        // Obtener tag_display desde historial_tags
        $db = \Config\Database::connect();
        $tagsDisplay = [];
        foreach ($tags as $tagNormalizado) {
            $tagInfo = $db->table('historial_tags')
                ->where('tag', $tagNormalizado)
                ->get()
                ->getRow();
            
            if ($tagInfo) {
                $tagsDisplay[] = $tagInfo->tag_display;
            } else {
                $tagsDisplay[] = $tagNormalizado;
            }
        }

        return implode(', ', $tagsDisplay);
    }

    /**
     * Obtener tags más usados (para autocompletado)
     */
    public function getTagsMasUsados($empresaId = null, $limit = 20)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('historial_tags')
            ->select('tag, tag_display, usos')
            ->orderBy('usos', 'DESC')
            ->limit($limit);

        if ($empresaId) {
            $builder->where('empresa_id', $empresaId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Buscar historiales por tags
     */
    public function buscarPorTags($tags, $empresaId = null)
    {
        if (empty($tags)) {
            return [];
        }

        // Normalizar tags de búsqueda
        $tagsBusqueda = array_map([$this, 'normalizarTag'], explode(',', $tags));
        $tagsBusqueda = array_filter($tagsBusqueda, function($tag) {
            return !empty($tag);
        });
        
        if (empty($tagsBusqueda)) {
            return [];
        }
        
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        // Especificar el nombre de la tabla para evitar ambigüedad cuando hay JOIN
        $builder->where($this->table . '.estado', 'A');
        
        // Buscar en JSON usando JSON_SEARCH - escapar cada tag
        $conditions = [];
        foreach ($tagsBusqueda as $tag) {
            // Escapar el tag y agregar comillas simples para JSON_SEARCH
            // escapeString() devuelve el string escapado pero sin comillas, así que las agregamos manualmente
            $tagEscapado = $db->escapeString($tag);
            // Remover las comillas que escapeString() puede agregar y agregar las nuestras
            $tagEscapado = trim($tagEscapado, "'\"");
            // JSON_SEARCH necesita el valor entre comillas simples
            $conditions[] = "JSON_SEARCH(" . $this->table . ".tags, 'one', '" . $tagEscapado . "') IS NOT NULL";
        }
        
        if (!empty($conditions)) {
            $builder->where('(' . implode(' OR ', $conditions) . ')');
        }

        if ($empresaId) {
            // Filtrar por empresa del nutricionista
            $builder->join('usuario', 'usuario.id = ' . $this->table . '.nutricionista_id', 'left')
                   ->where('usuario.empresa_id', $empresaId);
        }

        $result = $builder->get()->getResult();
        
        // Convertir a objetos del modelo
        $historiales = [];
        foreach ($result as $row) {
            $historiales[] = (object) $row;
        }
        
        return $historiales;
    }
}
