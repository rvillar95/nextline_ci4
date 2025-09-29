<?php
/**
 * Script para ejecutar el SQL que agrega el campo subtotal_sin_iva
 * a la tabla cotizaciones
 */

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'nextline_pyme';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conectado a la base de datos exitosamente.\n";
    
    // Leer el archivo SQL
    $sql = file_get_contents('agregar_campo_subtotal_sin_iva.sql');
    
    if ($sql === false) {
        throw new Exception("No se pudo leer el archivo SQL");
    }
    
    // Ejecutar el SQL
    $pdo->exec($sql);
    
    echo "Campo 'subtotal_sin_iva' agregado exitosamente a la tabla 'cotizaciones'.\n";
    echo "El campo almacenará el total antes de aplicar el IVA.\n";
    
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
