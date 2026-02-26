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
 * Ejecutar: php spark import:sql [archivo] [--fresh]
 * Sin argumentos usa writable/nextline_pyme.sql
 * --fresh: borra las tablas que vengan en el dump antes de importar (evita "Multiple primary key" si la BD ya tenía datos).
 */
class ImportSql extends BaseCommand
{
    protected $group        = 'Database';
    protected $name         = 'import:sql';
    protected $description  = 'Importa un archivo .sql en la base de datos (para uso puntual en servidor)';
    protected $usage       = 'import:sql [archivo.sql] [--fresh]';
    protected $arguments   = [
        'archivo' => 'Ruta al archivo .sql (opcional)',
    ];
    protected $options   = [
        'fresh' => 'Borra las tablas del dump antes de importar (BD vacía para reimportar)',
    ];

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
        // DELIMITER es comando del cliente mysql; el servidor no lo entiende → quitar esas líneas
        $sql = preg_replace('/^\s*DELIMITER\s+\S+\s*$/m', "\n", $sql);
        // Procedimientos usan $$ como fin de sentencia → convertir a ; para multi_query
        $sql = preg_replace('/\$\$\s*(\r?\n)/m', ";\n", $sql);

        $db = \Config\Database::connect();
        $useFresh = CLI::getOption('fresh') !== null;
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
        if ($useFresh) {
            preg_match_all('/CREATE\s+TABLE\s+[`]([^`]+)[`]/i', $sql, $m);
            $tables = array_unique($m[1] ?? []);
            if ($tables !== []) {
                CLI::write('Modo --fresh: borrando ' . count($tables) . ' tablas del dump...', 'yellow');
                $mysqli->query('SET FOREIGN_KEY_CHECKS=0');
                foreach ($tables as $t) {
                    $safe = '`' . str_replace('`', '``', $t) . '`';
                    $mysqli->query('DROP TABLE IF EXISTS ' . $safe);
                }
                $mysqli->query('SET FOREIGN_KEY_CHECKS=1');
            }
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
