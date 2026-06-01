<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWhatsappAgendaSesionTable extends Migration
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
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'empresa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'paso' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'default'    => 'menu',
            ],
            'datos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'factualizacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('telefono');
        $this->forge->addKey('empresa_id');
        $this->forge->createTable('whatsapp_agenda_sesion', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('whatsapp_agenda_sesion', true);
    }
}
