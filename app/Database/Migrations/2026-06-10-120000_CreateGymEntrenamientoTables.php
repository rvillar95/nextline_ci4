<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGymEntrenamientoTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => false, 'auto_increment' => true],
            'usuario_id' => ['type' => 'INT', 'null' => false],
            'empresa_id' => ['type' => 'INT', 'null' => false],
            'rutina_id' => ['type' => 'INT', 'null' => false],
            'programa_id' => ['type' => 'INT', 'null' => true],
            'programa_usuario_id' => ['type' => 'INT', 'null' => true],
            'estado' => ['type' => 'ENUM', 'constraint' => ['en_curso', 'completado', 'abandonado'], 'default' => 'en_curso'],
            'iniciado_en' => ['type' => 'DATETIME', 'null' => false],
            'finalizado_en' => ['type' => 'DATETIME', 'null' => true],
            'notas' => ['type' => 'TEXT', 'null' => true],
            'fcreacion' => ['type' => 'DATETIME', 'null' => false],
            'factualizacion' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('usuario_id');
        $this->forge->addKey('empresa_id');
        $this->forge->addKey(['usuario_id', 'estado']);
        $this->forge->createTable('gym_entrenamiento', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => false, 'auto_increment' => true],
            'entrenamiento_id' => ['type' => 'INT', 'null' => false],
            'ejercicio_id' => ['type' => 'INT', 'null' => false],
            'orden' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'nombre_ejercicio' => ['type' => 'VARCHAR', 'constraint' => 160],
            'series_planificadas' => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true],
            'reps_planificadas' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'descanso_seg' => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true],
            'notas_plan' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('entrenamiento_id');
        $this->forge->createTable('gym_entrenamiento_ejercicio', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => false, 'auto_increment' => true],
            'entrenamiento_ejercicio_id' => ['type' => 'INT', 'null' => false],
            'numero_serie' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 1],
            'peso_kg' => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true],
            'repeticiones' => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true],
            'completada' => ['type' => 'ENUM', 'constraint' => ['S', 'N'], 'default' => 'N'],
            'notas' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'fcreacion' => ['type' => 'DATETIME', 'null' => false],
            'factualizacion' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('entrenamiento_ejercicio_id');
        $this->forge->createTable('gym_entrenamiento_serie', true);
    }

    public function down()
    {
        $this->forge->dropTable('gym_entrenamiento_serie', true);
        $this->forge->dropTable('gym_entrenamiento_ejercicio', true);
        $this->forge->dropTable('gym_entrenamiento', true);
    }
}
