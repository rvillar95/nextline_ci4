<?php
/**
 * Script para probar las reglas de validación personalizadas
 */

// Configurar el entorno de CodeIgniter
require_once 'vendor/autoload.php';

// Crear instancia de CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Crear instancia del validador
$validation = \Config\Services::validation();

echo "🧪 PROBANDO REGLAS DE VALIDACIÓN PERSONALIZADAS\n\n";

// Probar validación de RUT
echo "📋 Probando validación de RUT:\n";
$rutTests = [
    '12.345.678-9' => true,
    '12345678-9' => true,
    '1.234.567-K' => true,
    '1234567-K' => true,
    '12.345.678-0' => false, // DV incorrecto
    '12345678-0' => false,   // DV incorrecto
    'abc' => false,          // Formato inválido
    '' => true               // Vacío permitido
];

foreach ($rutTests as $rut => $expected) {
    $isValid = $validation->check($rut, 'chilean_rut');
    $status = $isValid ? '✅' : '❌';
    $expectedStatus = $expected ? '✅' : '❌';
    echo "   $status RUT: '$rut' - Esperado: $expectedStatus\n";
}

echo "\n";

// Probar validación de teléfono
echo "📞 Probando validación de teléfono:\n";
$phoneTests = [
    '+56912345678' => true,
    '56912345678' => true,
    '912345678' => true,
    '+5691234567' => false,  // Muy corto
    '812345678' => false,    // Prefijo inválido
    'abc' => false,          // Formato inválido
    '' => true               // Vacío permitido
];

foreach ($phoneTests as $phone => $expected) {
    $isValid = $validation->check($phone, 'chilean_phone');
    $status = $isValid ? '✅' : '❌';
    $expectedStatus = $expected ? '✅' : '❌';
    echo "   $status Teléfono: '$phone' - Esperado: $expectedStatus\n";
}

echo "\n";

// Probar reglas completas del formulario
echo "📝 Probando reglas completas del formulario:\n";
$testData = [
    'tipo_cliente' => 'particular',
    'nombre_razon_social' => 'Juan Pérez',
    'rut_dni' => '12.345.678-9',
    'telefono' => '+56912345678',
    'email' => 'juan@ejemplo.com',
    'region_id' => '1',
    'comuna_id' => '1'
];

$rules = [
    'tipo_cliente' => 'required|in_list[particular,empresa,organizacion]',
    'nombre_razon_social' => 'required|string|max_length[200]',
    'rut_dni' => 'permit_empty|chilean_rut',
    'telefono' => 'permit_empty|chilean_phone',
    'email' => 'permit_empty|valid_email|max_length[150]',
    'region_id' => 'permit_empty|integer|greater_than[0]',
    'comuna_id' => 'permit_empty|integer|greater_than[0]'
];

if ($validation->run($testData, $rules)) {
    echo "   ✅ Validación completa exitosa\n";
} else {
    echo "   ❌ Errores de validación:\n";
    foreach ($validation->getErrors() as $field => $error) {
        echo "      - $field: $error\n";
    }
}

echo "\n🎯 Prueba completada\n";
