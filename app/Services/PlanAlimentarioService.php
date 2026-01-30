<?php

namespace App\Services;

use App\Models\PlanAlimentario;
use App\Models\PlanAlimentarioPorcion;
use App\Models\PlanAlimentarioComida;
use App\Models\PlanAlimentarioItem;
use App\Models\IntercambioPorcion;

/**
 * Servicio para cálculos y gestión de planes alimentarios
 */
class PlanAlimentarioService
{
    /**
     * Calcular macros objetivo en gramos
     * 
     * @param float $kcal Requerimiento calórico total
     * @param float $protPorcentaje % de proteínas
     * @param float $grasaPorcentaje % de grasas
     * @param float $choPorcentaje % de carbohidratos
     * @return array ['prot_gramos' => float, 'grasa_gramos' => float, 'cho_gramos' => float]
     */
    public function calcularMacros($kcal, $protPorcentaje, $grasaPorcentaje, $choPorcentaje)
    {
        // Proteínas: 4 kcal/g
        $prot_gramos = ($kcal * $protPorcentaje / 100) / 4;
        
        // Grasas: 9 kcal/g
        $grasa_gramos = ($kcal * $grasaPorcentaje / 100) / 9;
        
        // Carbohidratos: 4 kcal/g
        $cho_gramos = ($kcal * $choPorcentaje / 100) / 4;
        
        return [
            'prot_gramos' => round($prot_gramos, 2),
            'grasa_gramos' => round($grasa_gramos, 2),
            'cho_gramos' => round($cho_gramos, 2)
        ];
    }

    /**
     * Calcular valores nutricionales de porciones
     * 
     * @param float $porciones Cantidad de porciones
     * @param int $intercambioId ID del intercambio
     * @return array ['calorias' => float, 'cho' => float, 'grasa' => float, 'prot' => float]
     */
    public function calcularPorciones($porciones, $intercambioId)
    {
        $intercambioModel = new IntercambioPorcion();
        $intercambio = $intercambioModel->find($intercambioId);
        
        if (!$intercambio) {
            return ['calorias' => 0, 'cho' => 0, 'grasa' => 0, 'prot' => 0];
        }

        return [
            'calorias' => round($porciones * floatval($intercambio->kcal), 2),
            'cho' => round($porciones * floatval($intercambio->cho_g), 2),
            'grasa' => round($porciones * floatval($intercambio->grasa_g), 2),
            'prot' => round($porciones * floatval($intercambio->prot_g), 2)
        ];
    }

    /**
     * Calcular totales del plan desde porciones
     * 
     * @param array $porciones Array de porciones con valores calculados
     * @return array ['total_kcal' => float, 'total_cho' => float, 'total_grasa' => float, 'total_prot' => float]
     */
    public function calcularTotalesPlan($porciones)
    {
        $total_kcal = 0;
        $total_cho = 0;
        $total_grasa = 0;
        $total_prot = 0;

        foreach ($porciones as $porcion) {
            $total_kcal += floatval($porcion['calorias'] ?? 0);
            $total_cho += floatval($porcion['cho'] ?? 0);
            $total_grasa += floatval($porcion['grasa'] ?? 0);
            $total_prot += floatval($porcion['prot'] ?? 0);
        }

        return [
            'total_kcal' => round($total_kcal, 2),
            'total_cho' => round($total_cho, 2),
            'total_grasa' => round($total_grasa, 2),
            'total_prot' => round($total_prot, 2)
        ];
    }

    /**
     * Calcular adecuación porcentual igual que planilla: TOTAL = Requerimiento + Aporte, % = TOTAL / Requerimiento × 100
     * 
     * @param array $totales Totales del plan (aporte de porciones)
     * @param array $objetivos Objetivos (kcal, cho_g, grasa_g, prot_g)
     * @return array ['adecuacion_kcal_porc' => float, 'adecuacion_cho_porc' => float, 'adecuacion_grasa_porc' => float, 'adecuacion_prot_porc' => float]
     */
    public function calcularAdecuacion($totales, $objetivos)
    {
        $total_sum_kcal = $objetivos['kcal'] + ($totales['total_kcal'] ?? 0);
        $total_sum_cho = ($objetivos['cho_g'] ?? 0) + ($totales['total_cho'] ?? 0);
        $total_sum_grasa = ($objetivos['grasa_g'] ?? 0) + ($totales['total_grasa'] ?? 0);
        $total_sum_prot = ($objetivos['prot_g'] ?? 0) + ($totales['total_prot'] ?? 0);

        $adecuacion_kcal_porc = ($objetivos['kcal'] > 0)
            ? round(($total_sum_kcal / $objetivos['kcal']) * 100, 2)
            : 0;

        $adecuacion_cho_porc = ($objetivos['cho_g'] > 0)
            ? round(($total_sum_cho / $objetivos['cho_g']) * 100, 2)
            : 0;

        $adecuacion_grasa_porc = ($objetivos['grasa_g'] > 0)
            ? round(($total_sum_grasa / $objetivos['grasa_g']) * 100, 2)
            : 0;

        $adecuacion_prot_porc = ($objetivos['prot_g'] > 0)
            ? round(($total_sum_prot / $objetivos['prot_g']) * 100, 2)
            : 0;

        return [
            'adecuacion_kcal_porc' => $adecuacion_kcal_porc,
            'adecuacion_cho_porc' => $adecuacion_cho_porc,
            'adecuacion_grasa_porc' => $adecuacion_grasa_porc,
            'adecuacion_prot_porc' => $adecuacion_prot_porc
        ];
    }

    /**
     * Guardar plan alimentario completo
     * 
     * @param int $detalleAgendaId
     * @param array $data Datos del plan
     * @return int ID del plan guardado
     */
    public function guardarPlan($detalleAgendaId, $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calcular macros objetivo
            $macros = $this->calcularMacros(
                $data['requerimiento_kcal'],
                $data['prot_porcentaje'],
                $data['grasa_porcentaje'],
                $data['cho_porcentaje']
            );

            // Calcular totales desde porciones
            $totales = $this->calcularTotalesPlan($data['porciones'] ?? []);

            // Calcular adecuación
            $objetivos = [
                'kcal' => $data['requerimiento_kcal'],
                'cho_g' => $macros['cho_gramos'],
                'grasa_g' => $macros['grasa_gramos'],
                'prot_g' => $macros['prot_gramos']
            ];
            $adecuacion = $this->calcularAdecuacion($totales, $objetivos);

            // Guardar plan (INSERT directo vía DB para evitar error "near ') VALUES'" del Model)
            $planData = [
                'detalle_agenda_id' => (int) $detalleAgendaId,
                'paciente_id' => (int) $data['paciente_id'],
                'nutricionista_id' => (int) $data['nutricionista_id'],
                'calorimetria_id' => isset($data['calorimetria_id']) && $data['calorimetria_id'] !== '' ? (int) $data['calorimetria_id'] : null,
                'requerimiento_kcal' => (float) $data['requerimiento_kcal'],
                'prot_porcentaje' => (float) $data['prot_porcentaje'],
                'grasa_porcentaje' => (float) $data['grasa_porcentaje'],
                'cho_porcentaje' => (float) $data['cho_porcentaje'],
                'prot_gramos' => (float) $macros['prot_gramos'],
                'grasa_gramos' => (float) $macros['grasa_gramos'],
                'cho_gramos' => (float) $macros['cho_gramos'],
                'adecuacion_min' => round($data['requerimiento_kcal'] * 0.9, 2),
                'adecuacion_max' => round($data['requerimiento_kcal'] * 1.1, 2),
                'total_kcal' => (float) $totales['total_kcal'],
                'total_cho' => (float) $totales['total_cho'],
                'total_grasa' => (float) $totales['total_grasa'],
                'total_prot' => (float) $totales['total_prot'],
                'adecuacion_kcal_porc' => $adecuacion['adecuacion_kcal_porc'],
                'adecuacion_cho_porc' => $adecuacion['adecuacion_cho_porc'],
                'adecuacion_grasa_porc' => $adecuacion['adecuacion_grasa_porc'],
                'adecuacion_prot_porc' => $adecuacion['adecuacion_prot_porc'],
                'observaciones' => isset($data['observaciones']) && $data['observaciones'] !== '' ? $data['observaciones'] : null,
            ];

            // Un solo registro por detalle_agenda: si existe → actualizar; si no → insertar
            $existe = $db->table('plan_alimentario')
                ->where('detalle_agenda_id', (int) $detalleAgendaId)
                ->limit(1)
                ->get()
                ->getRow();

            if ($existe) {
                $planId = (int) $existe->id;
                $db->table('plan_alimentario')->where('id', $planId)->update($planData);
                // Borrar porciones anteriores para reemplazar con las nuevas
                $db->table('plan_alimentario_porcion')->where('plan_alimentario_id', $planId)->delete();
            } else {
                $db->table('plan_alimentario')->insert($planData);
                $planId = (int) $db->insertID();
                if ($planId === false || $planId === 0) {
                    throw new \Exception('No se pudo crear el plan alimentario (insert falló).');
                }
            }

            // Guardar porciones
            $porcionModel = new PlanAlimentarioPorcion();
            $orden = 0;
            foreach ($data['porciones'] ?? [] as $porcionData) {
                if (empty($porcionData['intercambio_porcion_id']) || floatval($porcionData['porciones'] ?? 0) <= 0) {
                    continue;
                }

                $valores = $this->calcularPorciones(
                    floatval($porcionData['porciones']),
                    intval($porcionData['intercambio_porcion_id'])
                );

                $porcionModel->insert([
                    'plan_alimentario_id' => $planId,
                    'intercambio_porcion_id' => $porcionData['intercambio_porcion_id'],
                    'porciones' => floatval($porcionData['porciones']),
                    'calorias' => $valores['calorias'],
                    'cho' => $valores['cho'],
                    'grasa' => $valores['grasa'],
                    'prot' => $valores['prot'],
                    'orden' => $orden++
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error al guardar plan alimentario');
            }

            return $planId;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error al guardar plan alimentario: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Distribuir porciones por comidas
     * 
     * @param int $planId
     * @param array $distribucion Array con distribución por comidas
     * @return bool
     */
    public function distribuirComidas($planId, $distribucion)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $comidaModel = new PlanAlimentarioComida();
            $itemModel = new PlanAlimentarioItem();
            $intercambioModel = new IntercambioPorcion();

            // Eliminar comidas existentes
            $comidasExistentes = $comidaModel->where('plan_alimentario_id', $planId)->findAll();
            foreach ($comidasExistentes as $comida) {
                $itemModel->where('plan_alimentario_comida_id', $comida->id)->delete();
            }
            $comidaModel->where('plan_alimentario_id', $planId)->delete();

            // Guardar nuevas comidas
            $orden = 0;
            foreach ($distribucion as $comidaData) {
                // Calcular totales de la comida desde items
                $total_kcal = 0;
                $total_cho = 0;
                $total_grasa = 0;
                $total_prot = 0;

                foreach ($comidaData['items'] ?? [] as $itemData) {
                    if (empty($itemData['intercambio_porcion_id']) || floatval($itemData['porciones'] ?? 0) <= 0) {
                        continue;
                    }

                    $valores = $this->calcularPorciones(
                        floatval($itemData['porciones']),
                        intval($itemData['intercambio_porcion_id'])
                    );

                    $total_kcal += $valores['calorias'];
                    $total_cho += $valores['cho'];
                    $total_grasa += $valores['grasa'];
                    $total_prot += $valores['prot'];
                }

                // Guardar comida
                $comidaId = $comidaModel->insert([
                    'plan_alimentario_id' => $planId,
                    'comida' => $comidaData['comida'],
                    'porcentaje_vct' => $comidaData['porcentaje_vct'] ?? null,
                    'minuta' => $comidaData['minuta'] ?? null,
                    'total_kcal' => round($total_kcal, 2),
                    'total_cho' => round($total_cho, 2),
                    'total_grasa' => round($total_grasa, 2),
                    'total_prot' => round($total_prot, 2),
                    'orden' => $orden++
                ]);

                // Guardar items de la comida
                $itemOrden = 0;
                foreach ($comidaData['items'] ?? [] as $itemData) {
                    if (empty($itemData['intercambio_porcion_id']) || floatval($itemData['porciones'] ?? 0) <= 0) {
                        continue;
                    }

                    $valores = $this->calcularPorciones(
                        floatval($itemData['porciones']),
                        intval($itemData['intercambio_porcion_id'])
                    );

                    $itemModel->insert([
                        'plan_alimentario_comida_id' => $comidaId,
                        'intercambio_porcion_id' => $itemData['intercambio_porcion_id'],
                        'ingrediente' => $itemData['ingrediente'] ?? null,
                        'porciones' => floatval($itemData['porciones']),
                        'medida_casera' => $itemData['medida_casera'] ?? null,
                        'gramaje' => $itemData['gramaje'] ?? null,
                        'calorias' => $valores['calorias'],
                        'cho' => $valores['cho'],
                        'grasa' => $valores['grasa'],
                        'prot' => $valores['prot'],
                        'costo_promedio' => $itemData['costo_promedio'] ?? null,
                        'cantidad_comprada' => $itemData['cantidad_comprada'] ?? null,
                        'observacion' => $itemData['observacion'] ?? null,
                        'orden' => $itemOrden++
                    ]);
                }
            }

            // Recalcular totales del plan desde comidas
            $planModel = new PlanAlimentario();
            $plan = $planModel->find($planId);
            if ($plan) {
                $comidas = $comidaModel->where('plan_alimentario_id', $planId)->findAll();
                $totalesPlan = [
                    'total_kcal' => 0,
                    'total_cho' => 0,
                    'total_grasa' => 0,
                    'total_prot' => 0
                ];

                foreach ($comidas as $comida) {
                    $totalesPlan['total_kcal'] += floatval($comida->total_kcal);
                    $totalesPlan['total_cho'] += floatval($comida->total_cho);
                    $totalesPlan['total_grasa'] += floatval($comida->total_grasa);
                    $totalesPlan['total_prot'] += floatval($comida->total_prot);
                }

                // Recalcular adecuación
                $objetivos = [
                    'kcal' => $plan->requerimiento_kcal,
                    'cho_g' => $plan->cho_gramos,
                    'grasa_g' => $plan->grasa_gramos,
                    'prot_g' => $plan->prot_gramos
                ];
                $adecuacion = $this->calcularAdecuacion($totalesPlan, $objetivos);

                // Actualizar plan
                $planModel->update($planId, [
                    'total_kcal' => round($totalesPlan['total_kcal'], 2),
                    'total_cho' => round($totalesPlan['total_cho'], 2),
                    'total_grasa' => round($totalesPlan['total_grasa'], 2),
                    'total_prot' => round($totalesPlan['total_prot'], 2),
                    'adecuacion_kcal_porc' => $adecuacion['adecuacion_kcal_porc'],
                    'adecuacion_cho_porc' => $adecuacion['adecuacion_cho_porc'],
                    'adecuacion_grasa_porc' => $adecuacion['adecuacion_grasa_porc'],
                    'adecuacion_prot_porc' => $adecuacion['adecuacion_prot_porc']
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error al distribuir comidas');
            }

            return true;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error al distribuir comidas: ' . $e->getMessage());
            throw $e;
        }
    }
}
