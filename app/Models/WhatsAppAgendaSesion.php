<?php

namespace App\Models;

use CodeIgniter\Model;

class WhatsAppAgendaSesion extends Model
{
    protected $table            = 'whatsapp_agenda_sesion';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';
    protected $useTimestamps      = false;
    protected $allowedFields      = ['telefono', 'empresa_id', 'paso', 'datos', 'factualizacion'];

    public function obtenerPorTelefono(string $telefono): ?array
    {
        $row = $this->where('telefono', $telefono)->first();
        return $row ?: null;
    }

    /**
     * @param int $empresaId 0 hasta que el paciente elige nutricionista (entonces viene de usuario.empresa_id)
     */
    public function guardarSesion(string $telefono, int $empresaId, string $paso, array $datos = []): void
    {
        if (!empty($datos['empresa_id'])) {
            $empresaId = (int) $datos['empresa_id'];
        }

        $existente = $this->obtenerPorTelefono($telefono);
        $payload = [
            'telefono'         => $telefono,
            'empresa_id'       => max(0, $empresaId),
            'paso'             => $paso,
            'datos'            => json_encode($datos, JSON_UNESCAPED_UNICODE),
            'factualizacion'   => date('Y-m-d H:i:s'),
        ];
        if ($existente) {
            $this->update($existente['id'], $payload);
        } else {
            $this->insert($payload);
        }
    }

    public function datosDecodificados(?array $sesion): array
    {
        if (!$sesion || empty($sesion['datos'])) {
            return [];
        }
        $decoded = json_decode($sesion['datos'], true);
        return is_array($decoded) ? $decoded : [];
    }

    public function eliminarPorTelefono(string $telefono): void
    {
        $this->where('telefono', $telefono)->delete();
    }
}
