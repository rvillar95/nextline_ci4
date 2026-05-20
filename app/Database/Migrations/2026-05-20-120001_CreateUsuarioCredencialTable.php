<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsuarioCredencialTable extends Migration
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
                'type'       => 'ENUM',
                'constraint' => ['titulo', 'diploma', 'curso', 'certificado', 'otro'],
                'default'    => 'otro',
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'institucion' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'anio' => [
                'type'       => 'SMALLINT',
                'constraint' => 4,
                'unsigned'   => true,
                'null'       => true,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'archivo_ruta' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'archivo_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'orden' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'visible_web' => [
                'type'       => 'ENUM',
                'constraint' => ['S', 'N'],
                'default'    => 'S',
            ],
            'fcreacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'factualizacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('usuario_id');
        $this->forge->addKey(['usuario_id', 'visible_web']);
        $this->forge->createTable('usuario_credencial', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('usuario_credencial', true);
    }
}
