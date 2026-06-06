<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GymVideoYRecordatorios extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('video_url', 'gym_ejercicio') === false) {
            $this->forge->addColumn('gym_ejercicio', [
                'video_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'instrucciones'],
            ]);
        }

        if ($this->db->fieldExists('video_url', 'gym_entrenamiento_ejercicio') === false) {
            $this->forge->addColumn('gym_entrenamiento_ejercicio', [
                'video_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'notas_plan'],
            ]);
        }

        if ($this->db->fieldExists('recibir_recordatorios_gym', 'usuario') === false) {
            $this->forge->addColumn('usuario', [
                'recibir_recordatorios_gym' => ['type' => 'CHAR', 'constraint' => 1, 'default' => 'S', 'null' => false, 'after' => 'estado'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('video_url', 'gym_ejercicio')) {
            $this->forge->dropColumn('gym_ejercicio', 'video_url');
        }
        if ($this->db->fieldExists('video_url', 'gym_entrenamiento_ejercicio')) {
            $this->forge->dropColumn('gym_entrenamiento_ejercicio', 'video_url');
        }
        if ($this->db->fieldExists('recibir_recordatorios_gym', 'usuario')) {
            $this->forge->dropColumn('usuario', 'recibir_recordatorios_gym');
        }
    }
}
