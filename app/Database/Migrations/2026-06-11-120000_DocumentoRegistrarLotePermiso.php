<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DocumentoRegistrarLotePermiso extends Migration
{
    public function up(): void
    {
        $modulo = $this->db->table('modulo')
            ->select('id')
            ->where('ruta', '/dashboard/documento')
            ->where('estado', 'A')
            ->get()
            ->getRow();

        if ($modulo === null) {
            return;
        }

        $moduloId = (int) $modulo->id;
        $now = date('Y-m-d H:i:s');

        $exists = $this->db->table('modulo_detalle')
            ->where('modulo_id', $moduloId)
            ->where('ruta', '/registrar-lote')
            ->countAllResults();

        if ($exists === 0) {
            $this->db->table('modulo_detalle')->insert([
                'modulo_id'      => $moduloId,
                'descripcion'    => 'Registrar documentos en lote',
                'ruta'           => '/registrar-lote',
                'accion'         => 'registrar',
                'estado'         => 'A',
                'mostrar'        => 'N',
                'orden'          => 11,
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
            ->where('ruta', '/dashboard/documento')
            ->get()
            ->getRow();

        if ($modulo === null) {
            return;
        }

        $this->db->table('modulo_detalle')
            ->where('modulo_id', (int) $modulo->id)
            ->where('ruta', '/registrar-lote')
            ->delete();
    }
}
