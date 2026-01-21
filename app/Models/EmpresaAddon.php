<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpresaAddon extends Model
{
    protected $table = 'empresa_addon';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'empresa_id',
        'tipo',            // modulo | metodo_calculo
        'referencia_id',   // modulo.id o metodos_calculo.id
        'precio_mensual',
        'fecha_inicio',
        'fecha_fin',
        'estado',          // activo | suspendido | cancelado
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    public function upsertAddon(array $data): bool
    {
        $db = \Config\Database::connect();

        // Upsert por UNIQUE (empresa_id, tipo, referencia_id)
        $sql = "INSERT INTO empresa_addon
                    (empresa_id, tipo, referencia_id, precio_mensual, fecha_inicio, fecha_fin, estado, fcreacion)
                VALUES
                    (:empresa_id:, :tipo:, :referencia_id:, :precio_mensual:, :fecha_inicio:, :fecha_fin:, :estado:, NOW())
                ON DUPLICATE KEY UPDATE
                    precio_mensual = VALUES(precio_mensual),
                    fecha_inicio   = VALUES(fecha_inicio),
                    fecha_fin      = VALUES(fecha_fin),
                    estado         = VALUES(estado),
                    factualizacion = NOW()";

        $db->query($sql, [
            'empresa_id' => (int) $data['empresa_id'],
            'tipo' => (string) $data['tipo'],
            'referencia_id' => (int) $data['referencia_id'],
            'precio_mensual' => (float) $data['precio_mensual'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'estado' => (string) $data['estado'],
        ]);

        return true;
    }
}

