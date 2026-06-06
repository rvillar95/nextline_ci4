<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GymNotasCoach extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('notas_coach', 'gym_programa_usuario') === false) {
            $this->forge->addColumn('gym_programa_usuario', [
                'notas_coach' => ['type' => 'TEXT', 'null' => true, 'after' => 'estado'],
            ]);
        }

        if ($this->db->fieldExists('notas_coach', 'gym_entrenamiento_ejercicio') === false) {
            $this->forge->addColumn('gym_entrenamiento_ejercicio', [
                'notas_coach' => ['type' => 'TEXT', 'null' => true, 'after' => 'notas_plan'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('notas_coach', 'gym_entrenamiento_ejercicio')) {
            $this->forge->dropColumn('gym_entrenamiento_ejercicio', 'notas_coach');
        }
        if ($this->db->fieldExists('notas_coach', 'gym_programa_usuario')) {
            $this->forge->dropColumn('gym_programa_usuario', 'notas_coach');
        }
    }
}
