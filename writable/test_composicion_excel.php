<?php

/**
 * Prueba de paridad PHP vs Excel de referencia.
 * Ejecutar: php writable/test_composicion_excel.php
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Services/ComposicionCorporalService.php';

$service = new App\Services\ComposicionCorporalService();

function fila(string $metodo, string $metrica, $esperado, $obtenido, float $tol = 0.15): array
{
    $diff = is_numeric($esperado) && is_numeric($obtenido)
        ? abs((float) $esperado - (float) $obtenido)
        : null;
    $ok = $diff !== null && $diff <= $tol;
    return compact('metodo', 'metrica', 'esperado', 'obtenido', 'diff', 'ok');
}

$filas = [];

// ─── 1. SOMATOTIPO — SOMATOTIPO BASICO.xls (Deportista 8) ───
$hSom = (object) [
    'peso_actual' => 77.1,
    'altura_actual' => 174.0,
    'pliegue_tricipital' => 7.0,
    'pliegue_subescapular' => 8.4,
    'pliegue_supraespinal' => 6.4,
    'pliegue_pantorrilla_medial' => 4.4,
    'diametro_humero' => 8.5,
    'diametro_femur' => 9.8,
    'circunferencia_brazo_contraido' => 32.4,
    'circunferencia_pantorrilla' => 38.6,
];
$rSom = $service->calcular('somatotipo', $hSom, (object) ['genero' => 'M']);
$excelSom = ['endo' => 2.08, 'meso' => 6.99, 'ecto' => 1.35, 'x' => -0.73, 'y' => 10.55];
foreach (['endo' => 'endomorfia', 'meso' => 'mesomorfia', 'ecto' => 'ectomorfia'] as $k => $campo) {
    $filas[] = fila('Somatotipo', $campo, $excelSom[$k], $rSom['componentes'][$campo], 0.1);
}
$filas[] = fila('Somatotipo', 'coord_x', $excelSom['x'], $rSom['coordenadas_somatochart']['x'], 0.1);
$filas[] = fila('Somatotipo', 'coord_y', $excelSom['y'], $rSom['coordenadas_somatochart']['y'], 0.1);

// ─── 2. DOS COMPONENTES — fórmulas Kerr (datos clínicos realistas, misma talla/peso que hoja Mujeres) ───
$h2 = (object) [
    'peso_actual' => 52.0,
    'altura_actual' => 161.0,
    'pliegue_tricipital' => 12,
    'pliegue_subescapular' => 15,
    'pliegue_supraespinal' => 14,
    'pliegue_abdominal' => 18,
    'pliegue_muslo_medial' => 16,
    'pliegue_pantorrilla_medial' => 10,
    'circunferencia_brazo_relajado' => 28,
    'circunferencia_antebrazo_maximo' => 24,
    'circunferencia_torax' => 88,
    'circunferencia_muslo_maximo' => 54,
    'circunferencia_pantorrilla' => 36,
];
$r2 = $service->calcular('2-componentes', $h2, (object) ['genero' => 'F']);
$ref2 = ['kg_adiposa' => 17.89, 'pct_adiposa' => 34.41, 'kg_muscular' => 26.84, 'pct_muscular' => 51.61];
$filas[] = fila('2 Componentes', 'kg_masa_adiposa', $ref2['kg_adiposa'], $r2['componentes']['masa_adiposa']['kg']);
$filas[] = fila('2 Componentes', '%_masa_adiposa', $ref2['pct_adiposa'], $r2['componentes']['masa_adiposa']['porcentaje']);
$filas[] = fila('2 Componentes', 'kg_masa_muscular', $ref2['kg_muscular'], $r2['componentes']['masa_muscular']['kg']);
$filas[] = fila('2 Componentes', '%_masa_muscular', $ref2['pct_muscular'], $r2['componentes']['masa_muscular']['porcentaje']);

// ─── 3. CUATRO COMPONENTES — Fisionutdep.xlsm (valores por defecto de la hoja + celdas J8/J10/J12/J14) ───
$h4 = (object) [
    'peso_actual' => 50.0,
    'altura_actual' => 150.0,
    'pliegue_tricipital' => 10,
    'pliegue_subescapular' => 10,
    'pliegue_suprailíaco' => 10,
    'pliegue_bicipital' => 10,
    'pliegue_pantorrilla_medial' => 10,
    'pliegue_muslo_medial' => 10,
    'circunferencia_brazo_relajado' => 20,
    'circunferencia_pantorrilla' => 20,
    'circunferencia_muslo_medio' => 20,
    'diametro_humero' => 30,
    'diametro_muneca' => 30,
    'diametro_femur' => 30,
];
$p4 = (object) ['genero' => 'F', 'fecha_nacimiento' => '1992-05-20'];
$r4 = $service->calcular('4-componentes', $h4, $p4);

// Valores cacheados en el Excel de referencia (misma fila de datos demo)
$excel4 = [
    'kg_muscular' => 5.49,
    'kg_grasa' => 12.74,
    'pct_grasa' => 25.48,
    'kg_osea' => 69.0,
    'kg_residual' => -37.23,
];
$filas[] = fila('4 Componentes', 'kg_muscular (J8)', $excel4['kg_muscular'], $r4['componentes']['musculo']['kg']);
$filas[] = fila('4 Componentes', 'kg_grasa (J10)', $excel4['kg_grasa'], $r4['componentes']['grasa']['kg']);
$filas[] = fila('4 Componentes', '%_grasa (D46)', $excel4['pct_grasa'], $r4['componentes']['grasa']['porcentaje']);
$filas[] = fila('4 Componentes', 'kg_osea (J12)', $excel4['kg_osea'], $r4['componentes']['hueso']['kg']);
$filas[] = fila('4 Componentes', 'kg_residual (J14)', $excel4['kg_residual'], $r4['componentes']['residual']['kg']);
$filas[] = fila('4 Componentes', 'advertencia_residual_neg', 1, !empty($r4['advertencias']) ? 1 : 0, 0);

// ─── 4. CINCO COMPONENTES — Holway (datos moderados, no placeholders del Excel) ───
$h5 = (object) [
    'peso_actual' => 70.0,
    'altura_actual' => 175.0,
    'altura_sentado' => 92.0,
    'diametro_biacromial' => 38.0,
    'diametro_torax_transverso' => 28.0,
    'diametro_torax_anteroposterior' => 18.0,
    'diametro_bi_iliocristal' => 29.0,
    'diametro_humero' => 6.8,
    'diametro_femur' => 10.0,
    'circunferencia_cabeza' => 56.0,
    'circunferencia_brazo_relajado' => 30.0,
    'circunferencia_antebrazo_maximo' => 27.0,
    'circunferencia_torax' => 95.0,
    'circunferencia_cintura' => 78.0,
    'circunferencia_muslo_maximo' => 58.0,
    'circunferencia_pantorrilla' => 37.0,
    'pliegue_tricipital' => 12.0,
    'pliegue_subescapular' => 14.0,
    'pliegue_supraespinal' => 12.0,
    'pliegue_abdominal' => 20.0,
    'pliegue_muslo_medial' => 18.0,
    'pliegue_pantorrilla_medial' => 10.0,
];
$r5 = $service->calcular('5-componentes', $h5, (object) ['genero' => 'M', 'fecha_nacimiento' => '1995-01-01']);
// Valores esperados = fórmulas Excel «Proc datos brutos» (Holway), mismo dataset clínico
$excel5 = [
    'kg_piel' => 3.82,
    'kg_adiposa' => 21.84,
    'kg_muscular' => 33.69,
    'kg_residual' => 7.60,
    'kg_osea' => 8.20,
    'peso_reconstituido' => 75.15,
];
$filas[] = fila('5 Componentes', 'kg_piel (MPIEL)', $excel5['kg_piel'], $r5['componentes']['masa_piel']['kg']);
$filas[] = fila('5 Componentes', 'kg_adiposa (MADIP)', $excel5['kg_adiposa'], $r5['componentes']['masa_adiposa']['kg']);
$filas[] = fila('5 Componentes', 'kg_muscular (MMUSC)', $excel5['kg_muscular'], $r5['componentes']['masa_muscular']['kg']);
$filas[] = fila('5 Componentes', 'kg_residual (MRES)', $excel5['kg_residual'], $r5['componentes']['masa_residual']['kg']);
$filas[] = fila('5 Componentes', 'kg_osea_total (MO)', $excel5['kg_osea'], $r5['componentes']['masa_osea_total']['kg']);
$filas[] = fila('5 Componentes', 'peso_reconstituido', $excel5['peso_reconstituido'], $r5['cierre_peso']['peso_reconstituido_kg']);
$filas[] = fila('5 Componentes', 'cierre_peso_medido_kg', $h5->peso_actual, $r5['cierre_peso']['peso_medido_kg'], 0.01);

// ─── Salida ───
$ok = 0;
$fail = 0;
echo str_repeat('=', 72) . PHP_EOL;
echo "PRUEBA DE PARIDAD — ComposicionCorporalService vs Excel" . PHP_EOL;
echo str_repeat('=', 72) . PHP_EOL;
printf("%-16s %-22s %10s %10s %8s %s\n", 'Método', 'Métrica', 'Esperado', 'Obtenido', 'Δ', 'OK');
echo str_repeat('-', 72) . PHP_EOL;
foreach ($filas as $f) {
    $status = $f['ok'] ? '✓' : '✗';
    if ($f['ok']) {
        $ok++;
    } else {
        $fail++;
    }
    $diff = $f['diff'] !== null ? number_format($f['diff'], 3) : '-';
    printf(
        "%-16s %-22s %10s %10s %8s %s\n",
        $f['metodo'],
        $f['metrica'],
        is_numeric($f['esperado']) ? number_format((float) $f['esperado'], 2) : $f['esperado'],
        is_numeric($f['obtenido']) ? number_format((float) $f['obtenido'], 2) : $f['obtenido'],
        $diff,
        $status
    );
}
echo str_repeat('-', 72) . PHP_EOL;
echo "Resultado: {$ok} OK, {$fail} FAIL de " . count($filas) . " comprobaciones\n";
echo str_repeat('=', 72) . PHP_EOL;

if ($fail > 0) {
    exit(1);
}
