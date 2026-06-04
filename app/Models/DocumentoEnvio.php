<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentoEnvio extends Model
{
    protected $table = 'documento_envios';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'paciente_id',
        'nutricionista_id',
        'email_destino',
        'mensaje_personal',
        'cantidad_documentos',
        'estado',
        'error_detalle',
        'fcreacion',
    ];

    /**
     * Registra un envío (exitoso o fallido) y vincula los documentos.
     *
     * @param int[] $documentoIds
     */
    public function registrar(
        int $pacienteId,
        ?int $nutricionistaId,
        string $emailDestino,
        ?string $mensajePersonal,
        array $documentoIds,
        string $estado = 'enviado',
        ?string $errorDetalle = null
    ): ?int {
        $documentoIds = array_values(array_unique(array_filter(array_map('intval', $documentoIds))));
        if ($documentoIds === [] || !$this->db->tableExists('documento_envios')) {
            return null;
        }

        $db = $this->db;
        $db->transStart();

        $this->insert([
            'paciente_id'          => $pacienteId,
            'nutricionista_id'     => $nutricionistaId,
            'email_destino'        => $emailDestino,
            'mensaje_personal'     => $mensajePersonal !== '' ? $mensajePersonal : null,
            'cantidad_documentos'  => count($documentoIds),
            'estado'               => $estado,
            'error_detalle'        => $errorDetalle,
            'fcreacion'            => date('Y-m-d H:i:s'),
        ]);

        $envioId = (int) $this->getInsertID();
        if ($envioId > 0) {
            $builder = $db->table('documento_envio_items');
            foreach ($documentoIds as $docId) {
                $builder->insert([
                    'envio_id'     => $envioId,
                    'documento_id' => $docId,
                ]);
            }
        }

        $db->transComplete();

        return $db->transStatus() ? $envioId : null;
    }

    /**
     * Historial de envíos que incluyen un documento.
     *
     * @return list<object>
     */
    public function getHistorialPorDocumento(int $documentoId, int $limit = 15): array
    {
        if (!$this->db->tableExists('documento_envios')) {
            return [];
        }

        return $this->db->table('documento_envios de')
            ->select('de.*, u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido')
            ->join('documento_envio_items dei', 'dei.envio_id = de.id')
            ->join('usuario u', 'u.id = de.nutricionista_id', 'left')
            ->where('dei.documento_id', $documentoId)
            ->orderBy('de.fcreacion', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }
}
