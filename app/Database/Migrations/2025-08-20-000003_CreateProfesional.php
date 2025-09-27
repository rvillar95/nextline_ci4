<?php
declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateProfesional extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'nombre'    => ['type' => 'VARCHAR', 'constraint' => 120],
            'cargo'     => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'bio'       => ['type' => 'VARCHAR', 'constraint' => 1000, 'null' => true],
            'foto'      => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
            'estado'    => ['type' => 'CHAR', 'constraint' => 1, 'default' => 'A'],
            'fcreacion' => ['type' => 'DATETIME', 'null' => false],
        ])->addKey('id', true)->createTable('profesional', true);

        // Si vas a usar `especialidad` existente, asociamos N:N
        $this->forge->addField([
            'profesional_id' => ['type' => 'INT'],
            'especialidad_id'=> ['type' => 'INT'],
        ])->addKey(['profesional_id', 'especialidad_id'], true)->createTable('profesional_especialidad', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('profesional_especialidad', true);
        $this->forge->dropTable('profesional', true);
    }
}
