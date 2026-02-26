<?php

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.1'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * ENV POR ENTORNO: Azure usa .env.azure si no existe .env
 *---------------------------------------------------------------
 * Local: ten tu .env con valores de desarrollo (no se versiona).
 * Azure: sube .env.azure con valores de producción, o define las
 * variables en App Service → Configuración → Configuración de la aplicación.
 * Si existe .env.azure y no .env, se copia .env.azure → .env antes de arrancar.
 * Si está definida MYSQLCONNSTR_* (Azure MySQL), se usa siempre para DB (sobrescribe .env).
 */
$isAzure = getenv('WEBSITE_SITE_NAME') !== false;
if ($isAzure) {
    if (! is_file(FCPATH . '.env') && is_file(FCPATH . '.env.azure')) {
        copy(FCPATH . '.env.azure', FCPATH . '.env');
    }
}
// Connection string de Azure MySQL: tiene prioridad para que no se use localhost por .env o defaults
// En Azure las cadenas de conexión pueden estar en getenv() o en $_SERVER
$cs = getenv('MYSQLCONNSTR_AZURE_MYSQL_CONNECTIONSTRING')
    ?: ($_SERVER['MYSQLCONNSTR_AZURE_MYSQL_CONNECTIONSTRING'] ?? null)
    ?: getenv('MYSQLCONNSTR_default')
    ?: ($_SERVER['MYSQLCONNSTR_default'] ?? null);
if ($cs !== null && $cs !== '' && $cs !== false) {
    $cs   = (string) $cs;
    $pairs = [];
    foreach (explode(';', $cs) as $part) {
        if (strpos($part, '=') !== false) {
            [$k, $v] = explode('=', $part, 2);
            $pairs[trim($k)] = trim($v);
        }
    }
    $host = $pairs['Server'] ?? $pairs['Data Source'] ?? 'localhost';
    $db   = $pairs['Database'] ?? 'mysql';
    // Si la cadena trae "mysql" (BD del sistema), usar DATABASE_NAME de App Settings o vitasync (BD en Azure)
    if ($db === 'mysql') {
        $db = getenv('DATABASE_NAME') ?: ($_SERVER['DATABASE_NAME'] ?? null) ?: 'vitasync';
    }
    $user = $pairs['User Id'] ?? $pairs['Uid'] ?? '';
    $pass = $pairs['Password'] ?? $pairs['Pwd'] ?? '';
    $_ENV['CI_ENVIRONMENT'] = $_ENV['CI_ENVIRONMENT'] ?? 'production';
    $_ENV['database.default.hostname'] = $host;
    $_ENV['database.default.database'] = $db;
    $_ENV['database.default.username'] = $user;
    $_ENV['database.default.password'] = $pass;
    $_ENV['database.default.DBDriver'] = 'MySQLi';
    $_ENV['database.default.port']      = '3306';
    putenv('database.default.hostname=' . $host);
    putenv('database.default.database=' . $db);
    putenv('database.default.username=' . $user);
    putenv('database.default.password=' . $pass);
    putenv('database.default.DBDriver=MySQLi');
    putenv('database.default.port=3306');
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '/app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Config\Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(CodeIgniter\Boot::bootWeb($paths));
