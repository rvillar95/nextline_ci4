<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BotonPagoEliminarPermiso extends Migration
{
    public function up(): void
    {
        $modulo = $this->db->table('modulo')
            ->select('id')
            ->where('ruta', '/dashboard/boton-pago')
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
            ->where('ruta', '/eliminar')
            ->countAllResults();

        if ($exists === 0) {
            $this->db->table('modulo_detalle')->insert([
                'modulo_id'      => $moduloId,
                'descripcion'    => 'Eliminar tarifa (AJAX)',
                'ruta'           => '/eliminar',
                'accion'         => 'eliminar',
                'estado'         => 'A',
                'mostrar'        => 'N',
                'orden'          => 8,
                'fcreacion'      => $now,
                'factualizacion' => $now,
                'feliminacion'   => '0000-00-00 00:00:00',
            ]);
        }

        $this->db->table('perfil_modulo')
            ->where('modulo_id', $moduloId)
            ->where('perfil_id', 9)
            ->update(['eliminar' => 1]);
    }

    public function down(): void
    {
        $modulo = $this->db->table('modulo')
            ->select('id')
            ->where('ruta', '/dashboard/boton-pago')
            ->get()
            ->getRow();

        if ($modulo === null) {
            return;
        }

        $moduloId = (int) $modulo->id;

        $this->db->table('modulo_detalle')
            ->where('modulo_id', $moduloId)
            ->where('ruta', '/eliminar')
            ->delete();

        $this->db->table('perfil_modulo')
            ->where('modulo_id', $moduloId)
            ->where('perfil_id', 9)
            ->update(['eliminar' => 0]);
    }
}
