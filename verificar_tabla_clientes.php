<?php
/**
 * Script para verificar la estructura de la tabla clientes
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
    
    echo "✅ Conectado a la base de datos: $database\n\n";
    
    // Verificar estructura de la tabla clientes
    echo "📋 Estructura de la tabla 'clientes':\n";
    $stmt = $pdo->query("DESCRIBE clientes");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "   - {$row['Field']}: {$row['Type']} " . 
             ($row['Null'] === 'NO' ? '(NOT NULL)' : '(NULL)') . 
             ($row['Key'] ? " [{$row['Key']}]" : '') . "\n";
    }
    
    echo "\n";
    
    // Verificar si existen los campos region_id y comuna_id
    $stmt = $pdo->query("SHOW COLUMNS FROM clientes LIKE 'region_id'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Campo 'region_id' existe en tabla 'clientes'\n";
    } else {
        echo "❌ Campo 'region_id' NO existe en tabla 'clientes'\n";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM clientes LIKE 'comuna_id'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Campo 'comuna_id' existe en tabla 'clientes'\n";
    } else {
        echo "❌ Campo 'comuna_id' NO existe en tabla 'clientes'\n";
    }
    
    echo "\n";
    
    // Mostrar algunos registros de ejemplo
    echo "📊 Registros existentes en tabla 'clientes':\n";
    $stmt = $pdo->query("SELECT id, tipo_cliente, nombre_razon_social, estado FROM clientes LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "   - ID: {$row['id']}, Tipo: {$row['tipo_cliente']}, Nombre: {$row['nombre_razon_social']}, Estado: {$row['estado']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
