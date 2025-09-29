<?php
/**
 * Script para verificar si las tablas de regiones y comunas existen y tienen datos
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
    
    // Verificar tabla regiones
    $stmt = $pdo->query("SHOW TABLES LIKE 'regiones'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'regiones' existe\n";
        
        $count = $pdo->query("SELECT COUNT(*) FROM regiones")->fetchColumn();
        echo "📊 Total de regiones: $count\n";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, nombre FROM regiones ORDER BY id LIMIT 5");
            echo "📋 Primeras 5 regiones:\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "   - ID: {$row['id']}, Nombre: {$row['nombre']}\n";
            }
        }
    } else {
        echo "❌ Tabla 'regiones' NO existe\n";
    }
    
    echo "\n";
    
    // Verificar tabla comunas
    $stmt = $pdo->query("SHOW TABLES LIKE 'comunas'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'comunas' existe\n";
        
        $count = $pdo->query("SELECT COUNT(*) FROM comunas")->fetchColumn();
        echo "📊 Total de comunas: $count\n";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, nombre, region_id FROM comunas ORDER BY id LIMIT 5");
            echo "📋 Primeras 5 comunas:\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "   - ID: {$row['id']}, Nombre: {$row['nombre']}, Región: {$row['region_id']}\n";
            }
        }
    } else {
        echo "❌ Tabla 'comunas' NO existe\n";
    }
    
    echo "\n";
    
    // Verificar campos en tabla clientes
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
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
