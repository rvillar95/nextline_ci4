<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificacionesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'info',
            ],
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'mensaje' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'enlace' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'referencia_tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'referencia_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'leida' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'fcreacion' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['usuario_id', 'leida']);
        $this->forge->addKey(['usuario_id', 'fcreacion']);
        $this->forge->createTable('notificaciones', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('notificaciones', true);
    }
}
