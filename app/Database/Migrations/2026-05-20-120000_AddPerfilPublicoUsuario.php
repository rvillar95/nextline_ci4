<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerfilPublicoUsuario extends Migration
{
    public function up(): void
    {
        $fields = [
            'titulo_profesional' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'foto',
            ],
            'especialidad' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'after'      => 'titulo_profesional',
            ],
            'carrera' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'after'      => 'especialidad',
            ],
            'presentacion' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'carrera',
            ],
            'descripcion_profesional' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'presentacion',
            ],
        ];

        if ($this->db->tableExists('usuario')) {
            foreach ($fields as $name => $def) {
                if (!$this->db->fieldExists($name, 'usuario')) {
                    $this->forge->addColumn('usuario', [$name => $def]);
                }
            }
        }
    }

    public function down(): void
    {
        if (!$this->db->tableExists('usuario')) {
            return;
        }
        foreach (['descripcion_profesional', 'presentacion', 'carrera', 'especialidad', 'titulo_profesional'] as $col) {
            if ($this->db->fieldExists($col, 'usuario')) {
                $this->forge->dropColumn('usuario', $col);
            }
        }
    }
}
