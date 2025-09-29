<?php
/**
 * Script para agregar campos region_id y comuna_id a la tabla clientes
 * Ejecutar desde la línea de comandos: php execute_agregar_campos_ubicacion_clientes.php
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
    $sqlFile = 'agregar_campos_ubicacion_clientes.sql';
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
        if (empty($statement) || strpos($statement, '--') === 0) continue;
        
        try {
            $pdo->exec($statement);
            $successCount++;
            
            // Mostrar progreso
            if (strpos($statement, 'ALTER TABLE') !== false) {
                echo "✅ Tabla clientes modificada exitosamente\n";
            } elseif (strpos($statement, 'ADD INDEX') !== false) {
                echo "🔍 Índice agregado\n";
            } elseif (strpos($statement, 'ADD CONSTRAINT') !== false) {
                echo "🔗 Clave foránea agregada\n";
            } elseif (strpos($statement, 'SELECT') !== false) {
                $result = $pdo->query($statement)->fetch();
                echo "📋 " . $result['resultado'] . "\n";
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
        echo "\n🎉 ¡Campos region_id y comuna_id agregados exitosamente a la tabla clientes!\n";
        echo "📋 Próximos pasos:\n";
        echo "   1. Ejecutar el script de regiones y comunas si no se ha hecho\n";
        echo "   2. Probar el formulario de registro de clientes\n";
        echo "   3. Probar el formulario de edición de clientes\n";
        echo "   4. Verificar que los selects dinámicos funcionen correctamente\n";
    } else {
        echo "\n⚠️  Se encontraron errores. Revisa los mensajes anteriores.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
