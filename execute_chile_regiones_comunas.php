<?php
/**
 * Script para ejecutar la creación de tablas de Regiones y Comunas de Chile
 * Ejecutar desde la línea de comandos: php execute_chile_regiones_comunas.php
 */

// Configuración de la base de datos
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'nextline_ci4';

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado a la base de datos: $database\n";
    
    // Leer el archivo SQL
    $sqlFile = 'chile_regiones_comunas.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("❌ No se encontró el archivo: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Dividir el SQL en statements individuales
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "📊 Ejecutando " . count($statements) . " statements SQL...\n\n";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $index => $statement) {
        if (empty($statement)) continue;
        
        try {
            $pdo->exec($statement);
            $successCount++;
            
            // Mostrar progreso
            if (strpos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE.*?`(\w+)`/', $statement, $matches);
                $tableName = $matches[1] ?? 'tabla';
                echo "✅ Tabla creada: $tableName\n";
            } elseif (strpos($statement, 'INSERT INTO') !== false) {
                preg_match('/INSERT INTO.*?`(\w+)`/', $statement, $matches);
                $tableName = $matches[1] ?? 'tabla';
                echo "📝 Datos insertados en: $tableName\n";
            } elseif (strpos($statement, 'CREATE INDEX') !== false) {
                echo "🔍 Índice creado\n";
            }
            
        } catch (PDOException $e) {
            $errorCount++;
            echo "❌ Error en statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
            echo "   Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }
    
    echo "\n📈 RESUMEN:\n";
    echo "✅ Statements exitosos: $successCount\n";
    echo "❌ Statements con error: $errorCount\n";
    
    if ($errorCount == 0) {
        echo "\n🎉 ¡Base de datos de Regiones y Comunas de Chile creada exitosamente!\n";
        echo "📋 Próximos pasos:\n";
        echo "   1. Actualizar el formulario de clientes para usar selects de región/comuna\n";
        echo "   2. Implementar JavaScript para filtrar comunas por región\n";
        echo "   3. Actualizar el modelo Cliente para usar las nuevas tablas\n";
    } else {
        echo "\n⚠️  Se encontraron errores. Revisa los mensajes anteriores.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
