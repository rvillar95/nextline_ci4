<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateListadoMaterialTables extends Migration
{
    public function up()
    {
        // Tabla principal: listado_material
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'numero_listado' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'titulo' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'cliente_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'proyecto_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'fecha_listado' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['borrador', 'finalizado', 'enviado', 'archivado'],
                'default' => 'borrador',
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('cliente_id');
        $this->forge->addKey('proyecto_id');
        $this->forge->createTable('listado_material');

        // Tabla de items: listado_material_item
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'listado_material_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nombre_material' => [
                'type' => 'VARCHAR',
                'constraint' => 300,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'unidad_medida' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'cantidad' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'orden' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('listado_material_id');
        $this->forge->addForeignKey('listado_material_id', 'listado_material', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('listado_material_item');
    }

    public function down()
    {
        $this->forge->dropTable('listado_material_item', true);
        $this->forge->dropTable('listado_material', true);
    }
}

