<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGymEjercicioPrTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                     => ['type' => 'INT', 'unsigned' => false, 'auto_increment' => true],
            'usuario_id'             => ['type' => 'INT', 'null' => false],
            'ejercicio_id'           => ['type' => 'INT', 'null' => false],
            'nombre_ejercicio'       => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'peso_kg'                => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => false],
            'repeticiones'           => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true],
            'entrenamiento_serie_id' => ['type' => 'INT', 'null' => true],
            'logrado_en'             => ['type' => 'DATETIME', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['usuario_id', 'logrado_en']);
        $this->forge->addKey(['usuario_id', 'ejercicio_id']);
        $this->forge->createTable('gym_ejercicio_pr', true);
    }

    public function down()
    {
        $this->forge->dropTable('gym_ejercicio_pr', true);
    }
}
