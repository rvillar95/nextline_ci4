<?php
declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateLeads extends Migration
{
    public function up(): void
    {
        // Estados de lead (semilla mínima en seeder)
        $this->forge->addField([
            'id'      => ['type' => 'INT', 'auto_increment' => true],
            'nombre'  => ['type' => 'VARCHAR', 'constraint' => 50],
        ])->addKey('id', true)->createTable('lead_estado', true);

        $this->forge->addField([
            'id'              => ['type' => 'INT', 'auto_increment' => true],
            'nombre'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'correo'          => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'telefono'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'mensaje'         => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'servicio_id'     => ['type' => 'INT', 'null' => true],
            'estado_id'       => ['type' => 'INT', 'null' => false, 'default' => 1], // 1=Nuevo
            'utm_source'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'utm_medium'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'utm_campaign'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'fcreacion'       => ['type' => 'DATETIME', 'null' => false],
            'factualizacion'  => ['type' => 'DATETIME', 'null' => true],
        ])->addKey('id', true)
          ->addKey('servicio_id')
          ->addKey('estado_id')
          ->createTable('lead_contacto', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('lead_contacto', true);
        $this->forge->dropTable('lead_estado', true);
    }
}
