<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PacienteActivarPermiso extends Migration
{
    public function up(): void
    {
        $modulo = $this->db->table('modulo')
            ->select('id')
            ->like('ruta', 'paciente')
            ->where('estado', 'A')
            ->orderBy('id', 'ASC')
            ->get()
            ->getRow();

        if ($modulo === null) {
            return;
        }

        $moduloId = (int) $modulo->id;
        $now = date('Y-m-d H:i:s');

        $rutas = [
            [
                'ruta'        => '/activar',
                'descripcion' => 'Reactivar paciente inactivo',
                'accion'      => 'eliminar',
                'orden'       => 17,
            ],
            [
                'ruta'        => '/eliminar',
                'descripcion' => 'Desactivar paciente',
                'accion'      => 'eliminar',
                'orden'       => 18,
            ],
        ];

        foreach ($rutas as $row) {
            $exists = $this->db->table('modulo_detalle')
                ->where('modulo_id', $moduloId)
                ->where('ruta', $row['ruta'])
                ->countAllResults();

            if ($exists > 0) {
                $this->db->table('modulo_detalle')
                    ->where('modulo_id', $moduloId)
                    ->where('ruta', $row['ruta'])
                    ->update([
                        'descripcion'    => $row['descripcion'],
                        'accion'         => $row['accion'],
                        'estado'         => 'A',
                        'factualizacion' => $now,
                    ]);
                continue;
            }

            $this->db->table('modulo_detalle')->insert([
                'modulo_id'      => $moduloId,
                'descripcion'    => $row['descripcion'],
                'ruta'           => $row['ruta'],
                'accion'         => $row['accion'],
                'estado'         => 'A',
                'mostrar'        => 'N',
                'orden'          => $row['orden'],
                'fcreacion'      => $now,
                'factualizacion' => $now,
                'feliminacion'   => '0000-00-00 00:00:00',
            ]);
        }
    }

    public function down(): void
    {
        $modulo = $this->db->table('modulo')
            ->select('id')
            ->like('ruta', 'paciente')
            ->get()
            ->getRow();

        if ($modulo === null) {
            return;
        }

        $this->db->table('modulo_detalle')
            ->where('modulo_id', (int) $modulo->id)
            ->whereIn('ruta', ['/activar', '/eliminar'])
            ->delete();
    }
}
