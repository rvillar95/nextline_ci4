<?php
/**
 * Script de Deployment Automático desde GitHub
 * 
 * INSTRUCCIONES:
 * 1. Sube este archivo a: /home/nextline/deploy.php (FUERA de public_html por seguridad)
 * 2. Configura el SECRET_KEY abajo
 * 3. Configura la ruta de tu proyecto
 * 4. Configura el webhook en GitHub apuntando a: https://vitasync.nextline.cl/deploy.php
 * 5. Configura permisos: chmod 755 /home/nextline/deploy.php
 */

// ============================================
// CONFIGURACIÓN
// ============================================

// Clave secreta (cámbiala por una clave aleatoria segura)
// Puedes generar una con: openssl rand -hex 32
define('SECRET_KEY', 'CAMBIAR_POR_TU_CLAVE_SECRETA_AQUI');

// Ruta donde está tu proyecto en el servidor (subdominio vitasync.nextline.cl)
define('REPO_PATH', '/home/nextline/vitasync.nextline.cl');

// Rama que quieres desplegar
define('BRANCH', 'feature/endgame');

// Archivo de log
define('LOG_FILE', '/home/nextline/deploy.log');

// ============================================
// FUNCIONES
// ============================================

function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] {$message}\n";
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND);
    error_log($logEntry); // También al error_log de PHP
}

function verifySignature($payload, $signature) {
    if (empty(SECRET_KEY) || SECRET_KEY === 'CAMBIAR_POR_TU_CLAVE_SECRETA_AQUI') {
        logMessage('WARNING: SECRET_KEY no configurada. Deployment permitido sin verificación.');
        return true; // Permitir si no está configurado (solo para desarrollo)
    }
    
    $calculated = 'sha256=' . hash_hmac('sha256', $payload, SECRET_KEY);
    return hash_equals($calculated, $signature);
}

function executeCommand($command, $path) {
    $output = [];
    $return_var = 0;
    
    $old_cwd = getcwd();
    chdir($path);
    
    exec($command . ' 2>&1', $output, $return_var);
    
    chdir($old_cwd);
    
    return [
        'output' => $output,
        'return_code' => $return_var,
        'success' => $return_var === 0
    ];
}

// ============================================
// PROCESAMIENTO
// ============================================

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed. Use POST.']));
}

logMessage('========================================');
logMessage('Deployment iniciado');

// Obtener payload
$payload = file_get_contents('php://input');
$headers = getallheaders();

// Verificar signature (si está configurado)
$signature = $headers['X-Hub-Signature-256'] ?? $headers['X-Hub-Signature'] ?? '';

if (!verifySignature($payload, $signature)) {
    logMessage('ERROR: Invalid signature');
    http_response_code(403);
    die(json_encode(['error' => 'Invalid signature']));
}

// Parsear payload JSON
$data = json_decode($payload, true);

// Verificar que es un push a la rama correcta
if (isset($data['ref'])) {
    $ref = $data['ref'];
    $branch = 'refs/heads/' . BRANCH;
    
    if ($ref !== $branch) {
        logMessage("INFO: Push a rama diferente ({$ref}), ignorando.");
        http_response_code(200);
        die(json_encode(['message' => 'Push to different branch, ignored']));
    }
}

logMessage("Push detectado a rama: " . BRANCH);

// Verificar que el directorio existe
if (!is_dir(REPO_PATH)) {
    logMessage("ERROR: Directorio no existe: " . REPO_PATH);
    http_response_code(500);
    die(json_encode(['error' => 'Repository path does not exist']));
}

// Verificar que es un repositorio Git
if (!is_dir(REPO_PATH . '/.git')) {
    logMessage("ERROR: No es un repositorio Git: " . REPO_PATH);
    http_response_code(500);
    die(json_encode(['error' => 'Not a Git repository']));
}

// 1. Hacer git pull
logMessage("Ejecutando: git pull origin " . BRANCH);
$gitResult = executeCommand("git pull origin " . BRANCH, REPO_PATH);

if (!$gitResult['success']) {
    logMessage("ERROR en git pull: " . implode("\n", $gitResult['output']));
    http_response_code(500);
    die(json_encode([
        'error' => 'Git pull failed',
        'output' => $gitResult['output']
    ]));
}

logMessage("Git pull exitoso: " . implode("\n", $gitResult['output']));

// 2. Instalar dependencias de Composer (si existe composer.json)
if (file_exists(REPO_PATH . '/composer.json')) {
    logMessage("Ejecutando: composer install --no-dev --optimize-autoloader");
    $composerResult = executeCommand(
        "composer install --no-dev --optimize-autoloader --no-interaction",
        REPO_PATH
    );
    
    if (!$composerResult['success']) {
        logMessage("WARNING en composer install: " . implode("\n", $composerResult['output']));
        // No fallar el deployment si composer falla, solo loguear
    } else {
        logMessage("Composer install exitoso");
    }
}

// 3. Limpiar caché de CodeIgniter
$cacheDir = REPO_PATH . '/writable/cache';
if (is_dir($cacheDir)) {
    logMessage("Limpiando caché de CodeIgniter");
    $files = glob($cacheDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    logMessage("Caché limpiado");
}

// 4. Limpiar caché de sesiones (opcional)
$sessionDir = REPO_PATH . '/writable/session';
if (is_dir($sessionDir)) {
    $sessionFiles = glob($sessionDir . '/*');
    $deleted = 0;
    foreach ($sessionFiles as $file) {
        if (is_file($file) && filemtime($file) < (time() - 3600)) { // Archivos más antiguos de 1 hora
            unlink($file);
            $deleted++;
        }
    }
    if ($deleted > 0) {
        logMessage("Limpiadas {$deleted} sesiones antiguas");
    }
}

logMessage("Deployment completado exitosamente");
logMessage('========================================');

// Respuesta exitosa
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Deployment completed successfully',
    'branch' => BRANCH,
    'timestamp' => date('Y-m-d H:i:s')
]);
