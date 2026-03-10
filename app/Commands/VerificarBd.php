<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Verifica que la base de datos importada tenga todas las tablas que la app espera
 * y muestra el número de filas por tabla (para comparar con el dump de origen).
 *
 * Uso: php spark db:check
 */
class VerificarBd extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:check';
    protected $description = 'Verifica tablas y conteo de filas en la BD (para validar importación)';
    protected $usage       = 'db:check';

    /** Tablas que la aplicación usa (Models + table() en controladores/librerías) */
    private const TABLAS_ESPERADAS = [
        'actividad_met',
        'agenda',
        'agenda_paciente',
        'botones_pago_plantilla',
        'calorimetria',
        'calorimetria_actividad',
        'clientes',
        'comunas',
        'cotizacion_archivos',
        'cotizacion_items',
        'cotizaciones',
        'detalle_agenda',
        'documentos',
        'empresa',
        'empresa_addon',
        'empresa_configuraciones',
        'galeria',
        'galeria_categoria',
        'historial_clinico',
        'historial_examen_bioquimico',
        'historial_tendencia_consumo',
        'imagenes',
        'intercambio_porcion',
        'lead_contacto',
        'lead_estado',
        'listado_material',
        'listado_material_item',
        'metodos_calculo',
        'modulo',
        'modulo_detalle',
        'modalidad_agenda',
        'paquete_modulo',
        'paquete_modulo_detalle',
        'paquetes',
        'pacientes',
        'pagos',
        'perfil',
        'perfil_modulo',
        'plan_alimentario',
        'plan_alimentario_comida',
        'plan_alimentario_item',
        'plan_alimentario_porcion',
        'proyectos',
        'regiones',
        'servicio',
        'servicio_categoria',
        'suscripciones',
        'testimonios',
        'tipo_agenda',
        'usuario',
        'usuario_calendar_tokens',
        'usuario_configuraciones',
        'whatsapp_mensajes',
        'migrations',
    ];

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        try {
            $db->query('SELECT 1');
        } catch (\Throwable $e) {
            CLI::error('No se pudo conectar a la base de datos: ' . $e->getMessage());
            return 1;
        }

        $prefix = $db->getPrefix();
        $existing = $this->obtenerTablasExistentes($db);

        CLI::newLine();
        CLI::write('=== Verificación de BD (importación) ===', 'cyan');
        CLI::write('Base de datos: ' . $db->getDatabase(), 'yellow');
        CLI::newLine();

        $faltan = [];
        $conteos = [];

        foreach (self::TABLAS_ESPERADAS as $tabla) {
            $nombre = $prefix . $tabla;
            if (! in_array($nombre, $existing, true)) {
                $faltan[] = $tabla;
                $conteos[$tabla] = null;
                continue;
            }
            try {
                $count = $db->table($tabla)->countAll();
                $conteos[$tabla] = $count;
            } catch (\Throwable $e) {
                $conteos[$tabla] = 'error: ' . $e->getMessage();
            }
        }

        if ($faltan !== []) {
            CLI::write('Tablas que NO existen en la BD:', 'red');
            foreach ($faltan as $t) {
                CLI::write('  - ' . $t, 'red');
            }
            CLI::newLine();
        }

        CLI::write('Conteo de filas por tabla (comparar con el servidor de origen):', 'green');
        CLI::newLine();

        $maxLen = max(array_map('strlen', array_keys($conteos)));
        foreach ($conteos as $tabla => $count) {
            $missing = in_array($tabla, $faltan, true);
            $str = str_pad($tabla, $maxLen) . '  ';
            if ($missing) {
                CLI::write($str . '(no existe)', 'red');
            } elseif (is_string($count)) {
                CLI::write($str . $count, 'yellow');
            } else {
                CLI::write($str . number_format($count), 'white');
            }
        }

        CLI::newLine();
        if ($faltan !== []) {
            CLI::write('Recomendación: exportar de nuevo el dump desde el origen (mysqldump sin límites) e importar con: php spark import:sql writable/nextline_pyme.sql --fresh', 'yellow');
            return 1;
        }
        CLI::write('Todas las tablas esperadas existen. Si faltan datos, compara los números con el origen.', 'green');
        return 0;
    }

    /** @return list<string> */
    private function obtenerTablasExistentes($db): array
    {
        $result = $db->query('SHOW TABLES')->getResult();
        $key    = 'Tables_in_' . $db->getDatabase();
        $list   = [];
        foreach ($result as $row) {
            $list[] = $row->{$key};
        }
        return $list;
    }
}
