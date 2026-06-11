<?php

namespace App\Models;

use CodeIgniter\Model;

class Documento extends Model
{
    protected $table = 'documentos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'paciente_id', 'nutricionista_id', 'tipo_documento', 'titulo', 'descripcion', 'contenido',
        'archivo_ruta', 'archivo_nombre', 'fecha_documento', 'fecha_vencimiento', 'enviado',
        'fecha_envio', 'metodo_envio', 'estado'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';
    protected $deletedField = 'feliminacion';

    protected $validationRules = [
        'paciente_id' => 'required|integer|greater_than[0]',
        'tipo_documento' => 'required|in_list[pauta_nutricional,receta,informe,consentimiento,otro]',
        'titulo' => 'required|string|max_length[200]',
        'fecha_documento' => 'required|valid_date',
        'estado' => 'required|in_list[A,I]'
    ];

    protected $validationMessages = [
        'paciente_id' => [
            'required' => 'El paciente es obligatorio.'
        ],
        'titulo' => [
            'required' => 'El título del documento es obligatorio.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener documento completo con relaciones
     */
    public function getDocumentoCompleto($id)
    {
        $documento = $this->find($id);
        if (!$documento) {
            return null;
        }

        // Cargar paciente
        $pacienteModel = new Paciente();
        $documento->paciente = $pacienteModel->find($documento->paciente_id);

        // Cargar nutricionista
        if ($documento->nutricionista_id) {
            $usuarioModel = new Usuario();
            $documento->nutricionista = $usuarioModel->find($documento->nutricionista_id);
        }

        return $documento;
    }

    /**
     * Obtener documentos por paciente
     */
    public function getDocumentosPorPaciente($pacienteId, $tipo = null, $nutricionistaId = null)
    {
        $builder = $this->where('paciente_id', $pacienteId)
            ->where('estado', 'A');

        if ($tipo) {
            $builder->where('tipo_documento', $tipo);
        }

        if ($nutricionistaId !== null && (int) $nutricionistaId > 0) {
            $builder->groupStart()
                ->where('nutricionista_id', (int) $nutricionistaId)
                ->orWhere('nutricionista_id', null)
                ->orWhere('nutricionista_id', 0)
                ->groupEnd();
        }

        return $builder->orderBy('fecha_documento', 'DESC')
            ->findAll();
    }

    /**
     * Obtener documentos por nutricionista
     */
    public function getDocumentosPorNutricionista($nutricionistaId, $estado = 'A')
    {
        return $this->where('nutricionista_id', $nutricionistaId)
            ->where('estado', $estado)
            ->orderBy('fecha_documento', 'DESC')
            ->findAll();
    }

    /**
     * Marcar documento como enviado
     */
    public function marcarComoEnviado($id, $metodo = 'sistema')
    {
        return $this->update($id, [
            'enviado' => 1,
            'fecha_envio' => date('Y-m-d H:i:s'),
            'metodo_envio' => $metodo
        ]);
    }
}
