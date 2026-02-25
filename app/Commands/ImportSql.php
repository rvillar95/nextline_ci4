<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Importa un dump SQL en la base de datos actual (útil cuando MySQL está solo en red privada, ej. Azure).
 *
 * Uso en el servidor (Azure Kudu/SSH):
 *   1. Sube nextline_pyme.sql a writable/ (Kudu: Advanced Tools > Go to Kudu > Debug console > Bash: drag & drop o curl)
 *   2. cd /home/site/wwwroot && php spark import:sql
 *   3. Borra writable/nextline_pyme.sql después
 *
 * Ejecutar: php spark import:sql [archivo]
 * Sin argumentos usa writable/nextline_pyme.sql
 */
class ImportSql extends BaseCommand
{
    protected $group        = 'Database';
    protected $name         = 'import:sql';
    protected $description  = 'Importa un archivo .sql en la base de datos (para uso puntual en servidor)';
    protected $usage       = 'import:sql [archivo.sql]';

    public function run(array $params)
    {
        $file = $params[0] ?? (FCPATH . 'writable' . DIRECTORY_SEPARATOR . 'nextline_pyme.sql');
        $file = realpath($file) ?: $file;

        if (! is_file($file) || ! is_readable($file)) {
            CLI::error("Archivo no encontrado o no legible: {$file}");
            CLI::write('Sube el .sql a writable/ y vuelve a ejecutar.', 'yellow');
            return 1;
        }

        CLI::write("Leyendo {$file}...", 'cyan');
        $sql = file_get_contents($file);
        if ($sql === false || $sql === '') {
            CLI::error('No se pudo leer el archivo o está vacío.');
            return 1;
        }

        // Quitar DEFINER para Azure MySQL
        $sql = preg_replace('/DEFINER\s*=\s*`[^`]+`@`[^`]+`\s+/', '', $sql);

        $db = \Config\Database::connect();
        try {
            $db->query('SELECT 1');
        } catch (\Throwable $e) {
            CLI::error('No se pudo conectar a la base de datos: ' . $e->getMessage());
            return 1;
        }
        $mysqli = $db->connID ?? ($db->mysqli ?? null);
        if (! $mysqli instanceof \mysqli) {
            CLI::error('Este comando solo funciona con el driver MySQLi.');
            return 1;
        }
        CLI::write('Ejecutando consultas (puede tardar)...', 'yellow');
        set_time_limit(0);

        if (! $mysqli->multi_query($sql)) {
            CLI::error('Error: ' . $mysqli->error);
            return 1;
        }

        do {
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
        } while ($mysqli->more_results() && $mysqli->next_result());

        if ($mysqli->error) {
            CLI::error('Error al finalizar: ' . $mysqli->error);
            return 1;
        }

        CLI::write('Importación completada.', 'green');
        return 0;
    }
}
