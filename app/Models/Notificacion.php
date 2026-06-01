<?php

namespace App\Models;

use CodeIgniter\Model;

class Notificacion extends Model
{
    protected $table            = 'notificaciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'usuario_id', 'tipo', 'titulo', 'mensaje', 'enlace',
        'referencia_tipo', 'referencia_id', 'leida', 'fcreacion',
    ];
    protected $useTimestamps = false;

    /**
     * Crea una notificación in-app para un usuario.
     */
    public function crear(int $usuarioId, string $tipo, string $titulo, string $mensaje, ?string $enlace = null, ?string $referenciaTipo = null, ?int $referenciaId = null): ?int
    {
        $id = $this->insert([
            'usuario_id'      => $usuarioId,
            'tipo'            => $tipo,
            'titulo'          => $titulo,
            'mensaje'         => $mensaje,
            'enlace'          => $enlace,
            'referencia_tipo' => $referenciaTipo,
            'referencia_id'   => $referenciaId,
            'leida'           => 0,
            'fcreacion'       => date('Y-m-d H:i:s'),
        ]);

        return $id ? (int) $id : null;
    }

    public function contarNoLeidas(int $usuarioId): int
    {
        return (int) $this->where('usuario_id', $usuarioId)->where('leida', 0)->countAllResults();
    }
}
