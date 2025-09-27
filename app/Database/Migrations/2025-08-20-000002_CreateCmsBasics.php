<?php
declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateCmsBasics extends Migration
{
    public function up(): void
    {
        // Páginas
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 120],
            'titulo'     => ['type' => 'VARCHAR', 'constraint' => 150],
            'contenido'  => ['type' => 'TEXT', 'null' => true],
            'estado'     => ['type' => 'CHAR', 'constraint' => 1, 'default' => 'A'],
            'fcreacion'  => ['type' => 'DATETIME', 'null' => false],
            'factualizacion' => ['type' => 'DATETIME', 'null' => true],
        ])->addKey('id', true)->addUniqueKey('slug')->createTable('pagina', true);

        // Banners
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'titulo'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'subtitulo' => ['type' => 'VARCHAR', 'constraint' => 250, 'null' => true],
            'imagen'    => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => false],
            'link'      => ['type' => 'VARCHAR', 'constraint' => 250, 'null' => true],
            'orden'     => ['type' => 'INT', 'default' => 1],
            'estado'    => ['type' => 'CHAR', 'constraint' => 1, 'default' => 'A'],
        ])->addKey('id', true)->createTable('banner', true);

        // SEO Meta genérico (entity_type + entity_id)
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'entity_type' => ['type' => 'VARCHAR', 'constraint' => 40], // servicio|galeria|pagina
            'entity_id'   => ['type' => 'INT', 'null' => false],
            'meta_title'  => ['type' => 'VARCHAR', 'constraint' => 70, 'null' => true],
            'meta_desc'   => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'og_image'    => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => true],
        ])->addKey('id', true)->addKey(['entity_type', 'entity_id'])->createTable('seo_meta', true);

        // Categorías para portafolio
        $this->forge->addField([
            'id'      => ['type' => 'INT', 'auto_increment' => true],
            'nombre'  => ['type' => 'VARCHAR', 'constraint' => 80],
        ])->addKey('id', true)->createTable('categoria', true);

        $this->forge->addField([
            'galeria_id'   => ['type' => 'INT'],
            'categoria_id' => ['type' => 'INT'],
        ])->addKey(['galeria_id', 'categoria_id'], true)->createTable('galeria_categoria', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('galeria_categoria', true);
        $this->forge->dropTable('categoria', true);
        $this->forge->dropTable('seo_meta', true);
        $this->forge->dropTable('banner', true);
        $this->forge->dropTable('pagina', true);
    }
}
