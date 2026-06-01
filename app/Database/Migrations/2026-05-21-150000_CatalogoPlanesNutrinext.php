<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Aplica catálogo comercial NutriNext 2026 vía SQL (mismo contenido que hosting).
 */
class CatalogoPlanesNutrinext extends Migration
{
    public function up()
    {
        $files = [
            APPPATH . 'Database/Sql/2026-05-21_catalogo_planes_nutrinext.sql',
            APPPATH . 'Database/Sql/2026-05-21_empresa_piloto_valentina.sql',
        ];

        foreach ($files as $path) {
            if (! is_file($path)) {
                continue;
            }
            $sql = file_get_contents($path);
            if ($sql === false || trim($sql) === '') {
                continue;
            }
            foreach ($this->splitStatements($sql) as $statement) {
                $statement = trim($statement);
                if ($statement === '' || str_starts_with($statement, '--')) {
                    continue;
                }
                try {
                    $this->db->query($statement);
                } catch (\Throwable $e) {
                    log_message('warning', 'Migration CatalogoPlanesNutrinext: ' . $e->getMessage());
                }
            }
        }
    }

    public function down()
    {
        // Sin rollback automático: usar backup de paquetes.
    }

    /**
     * @return list<string>
     */
    private function splitStatements(string $sql): array
    {
        $parts  = preg_split('/;\s*\n/', $sql) ?: [];
        $result = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '') {
                $result[] = $part;
            }
        }

        return $result;
    }
}
