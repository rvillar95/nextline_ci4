<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HistorialEliminarPermiso extends Migration
{
    public function up(): void
    {
        $modulo = $this->db->table('modulo')
            ->select('id')
            ->like('ruta', 'historial')
            ->where('estado', 'A')
            ->orderBy('id', 'ASC')
            ->get()
            ->getRow();

        if ($modulo === null) {
            return;
        }

        $moduloId = (int) $modulo->id;
        $now = date('Y-m-d H:i:s');

        $exists = $this->db->table('modulo_detalle')
            ->where('modulo_id', $moduloId)
            ->where('ruta', '/eliminar')
            ->countAllResults();

        if ($exists === 0) {
            $this->db->table('modulo_detalle')->insert([
                'modulo_id'      => $moduloId,
                'descripcion'    => 'Eliminar registro de historial clínico',
                'ruta'           => '/eliminar',
                'accion'         => 'eliminar',
                'estado'         => 'A',
                'mostrar'        => 'N',
                'orden'          => 20,
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
            ->like('ruta', 'historial')
            ->get()
            ->getRow();

        if ($modulo === null) {
            return;
        }

        $this->db->table('modulo_detalle')
            ->where('modulo_id', (int) $modulo->id)
            ->where('ruta', '/eliminar')
            ->delete();
    }
}
