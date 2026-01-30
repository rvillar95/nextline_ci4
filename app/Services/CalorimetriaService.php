<?php

namespace App\Services;

use App\Models\Calorimetria;
use App\Models\CalorimetriaActividad;
use App\Models\ActividadMet;

/**
 * Servicio para cálculos de calorimetría
 */
class CalorimetriaService
{
    /**
     * Calcular TMB (Tasa Metabólica Basal) según sexo
     * Fórmula Harris-Benedict
     * 
     * @param float $peso Peso en kg
     * @param float $talla Talla en cm
     * @param int $edad Edad en años
     * @param string $sexo 'M' o 'F'
     * @return array ['tmb_hombres' => float, 'tmb_mujeres' => float, 'tmb_usado' => float]
     */
    public function calcularTMB($peso, $talla, $edad, $sexo)
    {
        // Fórmula Harris-Benedict para Hombres
        $tmb_hombres = 66 + (13.7 * $peso) + (5 * $talla) - (6.8 * $edad);
        
        // Fórmula Harris-Benedict para Mujeres
        $tmb_mujeres = 655 + (9.6 * $peso) + (1.8 * $talla) - (4.7 * $edad);
        
        // Seleccionar según sexo
        $tmb_usado = ($sexo == 'M') ? $tmb_hombres : $tmb_mujeres;
        
        return [
            'tmb_hombres' => round($tmb_hombres, 2),
            'tmb_mujeres' => round($tmb_mujeres, 2),
            'tmb_usado' => round($tmb_usado, 2)
        ];
    }

    /**
     * Calcular calorías por actividad
     * Fórmula: Calorías = METs × (minutos/60) × (TMB/24)
     * 
     * @param float $mets Valor MET de la actividad
     * @param int $minutos Minutos de actividad
     * @param float $tmbHombres TMB para hombres
     * @param float $tmbMujeres TMB para mujeres
     * @return array ['calorias_hombre' => float, 'calorias_mujer' => float]
     */
    public function calcularCaloriasActividad($mets, $minutos, $tmbHombres, $tmbMujeres)
    {
        if ($minutos <= 0 || $mets <= 0) {
            return ['calorias_hombre' => 0, 'calorias_mujer' => 0];
        }

        // Calcular para hombres y mujeres por separado
        $calorias_hombre = $mets * ($minutos / 60) * ($tmbHombres / 24);
        $calorias_mujer = $mets * ($minutos / 60) * ($tmbMujeres / 24);
        
        return [
            'calorias_hombre' => round($calorias_hombre, 2),
            'calorias_mujer' => round($calorias_mujer, 2)
        ];
    }

    /**
     * Calcular requerimiento total (TMB + actividades)
     * 
     * @param float $tmb TMB del paciente
     * @param array $actividades Array de actividades con calorias_hombre y calorias_mujer
     * @param string $sexo 'M' o 'F'
     * @return array ['total_minutos' => int, 'cals_habituales' => float, 'cals_entrenamiento' => float, 'requerimiento_total' => float]
     */
    public function calcularRequerimientoTotal($tmb, $actividades, $sexo)
    {
        $total_minutos = 0;
        $cals_habituales = 0;
        $cals_entrenamiento = 0;
        
        foreach ($actividades as $actividad) {
            $minutos = intval($actividad['minutos_dia'] ?? 0);
            $calorias = ($sexo == 'M') 
                ? floatval($actividad['calorias_hombre'] ?? 0)
                : floatval($actividad['calorias_mujer'] ?? 0);
            
            $total_minutos += $minutos;
            
            if ($actividad['tipo'] == 'diaria') {
                $cals_habituales += $calorias;
            } else {
                $cals_entrenamiento += $calorias;
            }
        }
        
        $requerimiento_total = $tmb + $cals_habituales + $cals_entrenamiento;
        
        return [
            'total_minutos' => $total_minutos,
            'cals_habituales' => round($cals_habituales, 2),
            'cals_entrenamiento' => round($cals_entrenamiento, 2),
            'requerimiento_total' => round($requerimiento_total, 2)
        ];
    }

    /**
     * Guardar calorimetría completa
     * 
     * @param int $detalleAgendaId
     * @param array $data Datos de la calorimetría
     * @return int ID de la calorimetría guardada
     */
    public function guardarCalorimetria($detalleAgendaId, $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calcular TMB
            $tmb = $this->calcularTMB(
                $data['peso'],
                $data['talla'],
                $data['edad'],
                $data['sexo']
            );

            // Calcular calorías por actividad
            $actividadesCalculadas = [];
            $actividadMetModel = new ActividadMet();
            
            foreach ($data['actividades'] as $actividadData) {
                $actividadMet = $actividadMetModel->find($actividadData['actividad_met_id']);
                if (!$actividadMet) {
                    continue;
                }

                $calorias = $this->calcularCaloriasActividad(
                    floatval($actividadMet->mets),
                    intval($actividadData['minutos_dia']),
                    $tmb['tmb_hombres'],
                    $tmb['tmb_mujeres']
                );

                $actividadesCalculadas[] = [
                    'tipo' => $actividadMet->tipo,
                    'actividad' => $actividadMet->nombre,
                    'mets' => $actividadMet->mets,
                    'minutos_dia' => intval($actividadData['minutos_dia']),
                    'calorias_hombre' => $calorias['calorias_hombre'],
                    'calorias_mujer' => $calorias['calorias_mujer'],
                    'orden' => $actividadMet->orden ?? 0
                ];
            }

            // Calcular requerimiento total
            $requerimiento = $this->calcularRequerimientoTotal(
                $tmb['tmb_usado'],
                $actividadesCalculadas,
                $data['sexo']
            );

            // Relación 1:1 detalle_agenda ↔ calorimetría: actualizar si existe, crear si no
            $calorimetriaModel = new Calorimetria();
            $existente = $calorimetriaModel->where('detalle_agenda_id', (int) $detalleAgendaId)->first();

            $calorimetriaData = [
                'detalle_agenda_id' => intval($detalleAgendaId),
                'paciente_id' => intval($data['paciente_id']),
                'nutricionista_id' => intval($data['nutricionista_id']),
                'peso' => floatval($data['peso']),
                'talla' => floatval($data['talla']),
                'edad' => intval($data['edad']),
                'sexo' => strtoupper($data['sexo']),
                'tmb_hombres' => floatval($tmb['tmb_hombres']),
                'tmb_mujeres' => floatval($tmb['tmb_mujeres']),
                'tmb_usado' => floatval($tmb['tmb_usado']),
                'total_minutos' => intval($requerimiento['total_minutos']),
                'cals_habituales' => floatval($requerimiento['cals_habituales']),
                'cals_entrenamiento' => floatval($requerimiento['cals_entrenamiento']),
                'requerimiento_total' => floatval($requerimiento['requerimiento_total']),
            ];

            if ($existente) {
                // Actualizar registro existente (1:1)
                $calorimetriaId = (int) $existente->id;
                $db->table('calorimetria')->where('id', $calorimetriaId)->update($calorimetriaData);
                // Eliminar actividades anteriores y reinsertar
                $db->table('calorimetria_actividad')->where('calorimetria_id', $calorimetriaId)->delete();
            } else {
                // Crear nuevo
                $db->table('calorimetria')->insert($calorimetriaData);
                $calorimetriaId = (int) $db->insertID();
                if ($calorimetriaId <= 0) {
                    throw new \Exception('Error al insertar calorimetría: no se obtuvo ID');
                }
            }

            // Guardar actividades (insertar siempre, en update ya se borraron las antiguas)
            $actividadModel = new CalorimetriaActividad();
            foreach ($actividadesCalculadas as $act) {
                $actividadData = [
                    'calorimetria_id' => $calorimetriaId,
                    'tipo' => $act['tipo'],
                    'actividad' => $act['actividad'],
                    'mets' => floatval($act['mets']),
                    'minutos_dia' => intval($act['minutos_dia']),
                    'calorias_hombre' => floatval($act['calorias_hombre']),
                    'calorias_mujer' => floatval($act['calorias_mujer']),
                    'orden' => intval($act['orden'] ?? 0)
                ];
                $actividadInsertId = $actividadModel->insert($actividadData);
                if (!$actividadInsertId) {
                    $errors = $actividadModel->errors();
                    throw new \Exception('Error al insertar actividad: ' . json_encode($errors));
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error al guardar calorimetría');
            }

            return $calorimetriaId;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error al guardar calorimetría: ' . $e->getMessage());
            throw $e;
        }
    }
}
