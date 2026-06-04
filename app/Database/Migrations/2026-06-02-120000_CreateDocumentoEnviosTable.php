<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentoEnviosTable extends Migration
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
            'paciente_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nutricionista_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'email_destino' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'mensaje_personal' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'cantidad_documentos' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['enviado', 'fallido'],
                'default'    => 'enviado',
            ],
            'error_detalle' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'fcreacion' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('paciente_id');
        $this->forge->addKey('nutricionista_id');
        $this->forge->addKey('fcreacion');
        $this->forge->createTable('documento_envios', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'envio_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'documento_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['envio_id', 'documento_id']);
        $this->forge->addKey('documento_id');
        $this->forge->addForeignKey('envio_id', 'documento_envios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('documento_id', 'documentos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('documento_envio_items', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('documento_envio_items', true);
        $this->forge->dropTable('documento_envios', true);
    }
}
