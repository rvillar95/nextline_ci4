<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GymEjercicioSensacion extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('sensacion', 'gym_entrenamiento_ejercicio') === false) {
            $this->forge->addColumn('gym_entrenamiento_ejercicio', [
                'sensacion' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'notas_coach'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('sensacion', 'gym_entrenamiento_ejercicio')) {
            $this->forge->dropColumn('gym_entrenamiento_ejercicio', 'sensacion');
        }
    }
}
