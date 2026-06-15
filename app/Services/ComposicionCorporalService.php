<?php

namespace App\Services;

use App\Models\HistorialClinico;
use App\Models\Paciente;

/**
 * Servicio para calcular composición corporal usando diferentes métodos
 */
class ComposicionCorporalService
{
    /**
     * Calcular composición según método
     * 
     * @param string $metodoSlug Slug del método (2-componentes, 4-componentes, 5-componentes, somatotipo)
     * @param object $historial Objeto historial_clinico con todos los datos
     * @param object $paciente Objeto paciente con datos personales
     * @return array Resultado del cálculo
     */
    public function calcular($metodoSlug, $historial, $paciente)
    {
        switch ($metodoSlug) {
            case '2-componentes':
                return $this->calcular2Componentes($historial, $paciente);
            case '4-componentes':
                return $this->calcular4Componentes($historial, $paciente);
            case '5-componentes':
                return $this->calcular5Componentes($historial, $paciente);
            case 'somatotipo':
                return $this->calcularSomatotipo($historial, $paciente);
            default:
                throw new \Exception('Método no válido: ' . $metodoSlug);
        }
    }
    
    /**
     * Calcular edad desde fecha de nacimiento
     */
    private function calcularEdad($fechaNacimiento)
    {
        if (empty($fechaNacimiento)) {
            return null;
        }
        
        // Convertir fecha a formato Y-m-d si viene en otro formato
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fechaNacimiento, $matches)) {
            $fechaNacimiento = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        
        $fecha = new \DateTime($fechaNacimiento);
        $hoy = new \DateTime();
        $edad = $hoy->diff($fecha)->y;
        
        return $edad;
    }
    
    /**
     * Método 2 Componentes — Excel «KG_% DE MM Y MA - 2 COMPONENTES.xlsx»
     * Masa Adiposa y Masa Muscular (Kerr / phantom 170.18 cm).
     *
     * Adiposa: Σ6 pliegues → Z_ADIP → kg (C36–C39).
     * Muscular: perímetros corregidos (L42–L46) → Z_MUSC → kg (C42–C45).
     */
    private function calcular2Componentes($historial, $paciente)
    {
        $P = floatval($historial->peso_actual ?? 0);
        $T = floatval($historial->altura_actual ?? 0);

        $PTRI = floatval($historial->pliegue_tricipital ?? 0);
        $PSUB = floatval($historial->pliegue_subescapular ?? 0);
        $PSUP = floatval($historial->pliegue_supraespinal ?? 0);
        $PABD = floatval($historial->pliegue_abdominal ?? 0);
        $PMED = floatval($historial->pliegue_muslo_medial ?? 0);
        $PPANPL = floatval($historial->pliegue_pantorrilla_medial ?? 0);

        $PBR = floatval($historial->circunferencia_brazo_relajado ?? 0);
        $PAM = floatval($historial->circunferencia_antebrazo_maximo ?? 0);
        $PTX = floatval($historial->circunferencia_torax ?? 0);
        $PMUS = floatval($historial->circunferencia_muslo_maximo ?? 0);
        $PPAN = floatval($historial->circunferencia_pantorrilla ?? 0);

        if ($P <= 0 || $T <= 0) {
            throw new \Exception('Peso y talla son requeridos para el modelo 2 componentes (Kerr)');
        }

        $SUM6 = $PTRI + $PSUB + $PSUP + $PABD + $PMED + $PPANPL;
        if ($SUM6 <= 0) {
            throw new \Exception(
                'Se requieren 6 pliegues (mm): tríceps, subescapular, supraespinal, abdominal, muslo medial y pantorrilla medial'
            );
        }

        $T_REF = 170.18;
        $ratioT = $T_REF / $T;
        $PI = M_PI;

        $Z_ADIP = (($SUM6 * $ratioT) - 116.41) / 34.79;
        $MADIP = (($Z_ADIP * 5.85) + 25.6) / pow($ratioT, 3);
        if ($MADIP <= 0) {
            throw new \Exception('Masa adiposa inválida (≤ 0). Revisá los 6 pliegues (mm) y la talla (cm).');
        }

        $PBR_CORR = $PBR - (($PTRI * $PI) / 10);
        $PAM_CORR = $PAM;
        $PMUS_CORR = $PMUS - (($PMED * $PI) / 10);
        $PPAN_CORR = $PPAN - (($PPANPL * $PI) / 10);
        $PTX_CORR = $PTX - (($PSUB * $PI) / 10);

        $SUM_PER_CORR = $PBR_CORR + $PAM_CORR + $PMUS_CORR + $PPAN_CORR + $PTX_CORR;
        if ($SUM_PER_CORR <= 0) {
            throw new \Exception(
                'Se requieren perímetros (cm): brazo relajado, antebrazo máximo, tórax mesoesternal, muslo máximo y pantorrilla máxima'
            );
        }

        $Z_MUSC = (($SUM_PER_CORR * $ratioT) - 207.21) / 13.74;
        $MMUSC = (($Z_MUSC * 5.4) + 24.5) / pow($ratioT, 3);
        if ($MMUSC <= 0) {
            throw new \Exception(
                'Masa muscular inválida (≤ 0). Revisá perímetros (cm) y pliegues (mm); suele deberse a unidades incorrectas (pulgadas como cm).'
            );
        }

        $pctADIP = ($P > 0) ? ($MADIP / $P) * 100 : 0;
        $pctMUSC = ($P > 0) ? ($MMUSC / $P) * 100 : 0;

        return [
            'metodo' => '2-componentes',
            'nombre' => 'Modelo 2 componentes (Kerr — Masa Adiposa y Masa Muscular)',
            'componentes' => [
                'masa_adiposa' => [
                    'kg' => round($MADIP, 2),
                    'porcentaje' => round($pctADIP, 2),
                ],
                'masa_muscular' => [
                    'kg' => round($MMUSC, 2),
                    'porcentaje' => round($pctMUSC, 2),
                ],
            ],
            'datos_usados' => [
                'peso' => $P,
                'talla' => $T,
                'suma_6_pliegues' => $SUM6,
                'suma_perimetros_corregidos' => round($SUM_PER_CORR, 2),
            ],
            'pasos_calculo' => [
                'suma_6_pliegues_mm' => round($SUM6, 2),
                'z_adip' => round($Z_ADIP, 4),
                'madip_kg' => round($MADIP, 2),
                'pbr_corr' => round($PBR_CORR, 2),
                'pam_corr' => round($PAM_CORR, 2),
                'pmus_corr' => round($PMUS_CORR, 2),
                'ppan_corr' => round($PPAN_CORR, 2),
                'ptx_corr' => round($PTX_CORR, 2),
                'sum_per_corr' => round($SUM_PER_CORR, 2),
                'z_musc' => round($Z_MUSC, 4),
                'mmusc_kg' => round($MMUSC, 2),
            ],
        ];
    }
    
    /**
     * Método 4 Componentes — Excel «Composición Corporal Fisionutdep - 4 COMPONENTES.xlsm»
     * Hoja: Antropometría & Composición (ecuación Siri por defecto).
     *
     * Grasa: Durnin (log) sobre 4 pliegues TR + SE + SI + Bíceps → % Siri → kg.
     * Ósea: 3.02 × (((T/100)² × (diámetro muñeca/100) × (fémur/100) × 400)^0.712) — celdas G34 y D36.
     * Muscular: Lee (celda J8) con perímetros corregidos (brazo, pantorrilla, muslo).
     * Residual: P − (MM + MG + MO).
     */
    private function calcular4Componentes($historial, $paciente)
    {
        $P = floatval($historial->peso_actual ?? 0);
        $T = floatval($historial->altura_actual ?? 0);
        $genero = strtoupper(trim($paciente->genero ?? 'M'));
        $esMujer = ($genero === 'F' || $genero === 'MUJER');
        $Edad = (int) $this->calcularEdad($paciente->fecha_nacimiento ?? null);
        if ($Edad === null || $Edad < 0) {
            $Edad = 0;
        }

        $TR = floatval($historial->pliegue_tricipital ?? 0);
        $SE = floatval($historial->pliegue_subescapular ?? 0);
        $SI = floatval($historial->pliegue_suprailíaco ?? 0);
        $BI = floatval($historial->pliegue_bicipital ?? 0);

        $PBR = floatval($historial->circunferencia_brazo_relajado ?? 0);
        $PPAN = floatval($historial->circunferencia_pantorrilla ?? 0);
        $PMUS = floatval($historial->circunferencia_muslo_medio ?? 0);
        $PLPAN = floatval($historial->pliegue_pantorrilla_medial ?? 0);
        $PLMUS = floatval($historial->pliegue_muslo_medial ?? 0);
        $DMUN = floatval($historial->diametro_muneca ?? 0);
        $DF = floatval($historial->diametro_femur ?? 0);

        if ($P <= 0 || $T <= 0) {
            throw new \Exception('Peso y talla son requeridos para el modelo 4 componentes (Fisionutdep)');
        }

        $suma4Durnin = $TR + $SE + $SI + $BI;
        if ($suma4Durnin <= 0) {
            throw new \Exception('Se requieren 4 pliegues (mm): tríceps, subescapular, suprailíaco y bíceps (Fisionutdep / Durnin)');
        }
        if ($PBR <= 0 || $PPAN <= 0 || $PMUS <= 0) {
            throw new \Exception('Se requieren circunferencias (cm): brazo relajado, pantorrilla y muslo (Fisionutdep / Lee)');
        }
        if ($PLPAN <= 0 || $PLMUS <= 0) {
            throw new \Exception('Se requieren pliegues (mm): pantorrilla medial y muslo medial (Fisionutdep / perímetros corregidos)');
        }
        if ($DMUN <= 0 || $DF <= 0) {
            throw new \Exception('Se requieren diámetros óseos (cm): muñeca y fémur para masa ósea (Fisionutdep — celdas G34 y D36)');
        }

        $logSuma = log10($suma4Durnin);
        if ($esMujer) {
            if ($Edad >= 17 && $Edad <= 19) {
                $D = 1.1549 - (0.0678 * $logSuma);
            } elseif ($Edad >= 20 && $Edad <= 29) {
                $D = 1.1599 - (0.0717 * $logSuma);
            } elseif ($Edad >= 30 && $Edad <= 39) {
                $D = 1.1423 - (0.0632 * $logSuma);
            } elseif ($Edad >= 40 && $Edad <= 49) {
                $D = 1.1333 - (0.0612 * $logSuma);
            } else {
                $D = 1.1339 - (0.0645 * $logSuma);
            }
        } else {
            if ($Edad >= 17 && $Edad <= 19) {
                $D = 1.1620 - (0.0630 * $logSuma);
            } elseif ($Edad >= 20 && $Edad <= 29) {
                $D = 1.1631 - (0.0632 * $logSuma);
            } elseif ($Edad >= 30 && $Edad <= 39) {
                $D = 1.1422 - (0.0544 * $logSuma);
            } elseif ($Edad >= 40 && $Edad <= 49) {
                $D = 1.1620 - (0.0700 * $logSuma);
            } else {
                $D = 1.1715 - (0.0779 * $logSuma);
            }
        }

        if ($D <= 0) {
            throw new \Exception('Densidad corporal inválida. Revisá pliegues (mm) y edad.');
        }

        // Siri % (celda D46): (495/D) − 450 ≡ ((4.95/D)−4.50)×100
        $pctMG = (495 / $D) - 450;
        $MG = ($pctMG * $P) / 100;

        // Masa ósea (celda J12) — diámetro muñeca G34 y diámetro fémur D36
        $MO = 3.02 * pow((($T / 100) ** 2) * ($DMUN / 100) * ($DF / 100) * 400, 0.712);

        // Perímetros corregidos (J54, J55, J56) — π/10 ≈ 0.31416
        $piDec = M_PI / 10;
        $perBrazoCorr = $PBR - ($BI * $piDec);
        $perPiernaCorr = $PPAN - ($PLPAN * $piDec);
        $perMusloCorr = $PMUS - ($PLMUS * $piDec);

        // Ajuste étnico G12 (por defecto caucásica-latina = 0)
        $ajusteEtnia = 0.0;

        if ($esMujer) {
            $mmLee = ($T * ((0.00744 * $perBrazoCorr ** 2) + (0.00088 * $perMusloCorr ** 2) + (0.00441 * $perPiernaCorr ** 2)))
                - (0.048 * $Edad) + 7.8 + $ajusteEtnia;
        } else {
            $mmLee = ($T * ((0.00744 * $perBrazoCorr ** 2) + (0.00088 * $perMusloCorr ** 2) + (0.00447 * $perPiernaCorr ** 2)))
                + 2.4 - (0.048 * $Edad) + 7.8 + $ajusteEtnia;
        }
        $MM = $mmLee / 100;

        // Residual por diferencia (celda J14)
        $MR = $P - $MM - $MG - $MO;

        if ($MM <= 0) {
            throw new \Exception('Masa muscular inválida (≤ 0). Revisá circunferencias (cm) y pliegues (mm) según Fisionutdep.');
        }

        $advertencias = [];
        if ($MR < 0) {
            $advertencias[] = 'Masa residual negativa: revisá diámetros óseos (cm) y demás mediciones. El Excel puede mostrar lo mismo con datos de ejemplo irreales.';
        }

        $pctMM = ($P > 0) ? ($MM / $P) * 100 : 0;
        $pctMO = ($P > 0) ? ($MO / $P) * 100 : 0;
        $pctMR = ($P > 0) ? ($MR / $P) * 100 : 0;

        return [
            'metodo' => '4-componentes',
            'nombre' => 'Modelo 4 componentes (Fisionutdep — Siri / Lee)',
            'advertencias' => $advertencias,
            'componentes' => [
                'grasa' => [
                    'kg' => round($MG, 2),
                    'porcentaje' => round($pctMG, 2),
                ],
                'hueso' => [
                    'kg' => round($MO, 2),
                    'porcentaje' => round($pctMO, 2),
                ],
                'residual' => [
                    'kg' => round($MR, 2),
                    'porcentaje' => round($pctMR, 2),
                ],
                'musculo' => [
                    'kg' => round($MM, 2),
                    'porcentaje' => round($pctMM, 2),
                ],
            ],
            'datos_usados' => [
                'peso' => $P,
                'talla' => $T,
                'sexo' => $esMujer ? 'Mujer' : 'Hombre',
                'edad' => $Edad,
                'suma_4_pliegues_durnin' => $suma4Durnin,
                'diametro_muneca' => $DMUN,
                'diametro_femur' => $DF,
                'perimetro_brazo_corregido' => round($perBrazoCorr, 2),
                'perimetro_pantorrilla_corregido' => round($perPiernaCorr, 2),
                'perimetro_muslo_corregido' => round($perMusloCorr, 2),
            ],
            'pasos_calculo' => [
                'suma_4_pliegues_mm' => round($suma4Durnin, 2),
                'densidad_durnin' => round($D, 4),
                'pct_grasa_siri' => round($pctMG, 2),
                'masa_grasa_kg' => round($MG, 2),
                'masa_osea_kg' => round($MO, 2),
                'masa_muscular_lee_kg' => round($MM, 2),
                'masa_residual_kg' => round($MR, 2),
            ],
        ];
    }
    
    /**
     * Calcular masa ósea (Fórmula de De Rose)
     */
    private function calcularMasaOsea($altura, $diametro_humero, $diametro_femur, $circunferencia_brazo, $circunferencia_pantorrilla, $genero)
    {
        // Fórmula simplificada de De Rose
        // Masa ósea = 3.02 * (altura^0.712) * (diametro_humero^0.393) * (diametro_femur^0.315)
        $altura_metros = $altura / 100;
        
        $masa_osea = 3.02 * pow($altura_metros, 0.712) * pow($diametro_humero, 0.393) * pow($diametro_femur, 0.315);
        
        // Ajuste según género
        if ($genero === 'F') {
            $masa_osea = $masa_osea * 0.9; // Mujeres tienen aproximadamente 10% menos masa ósea
        }
        
        return $masa_osea;
    }
    
    /**
     * Método 5 Componentes — Excel «Antropgym Francis Holway - 5 COMPONENTE.xlsx»
     * Hoja de cálculo: «Proc datos brutos» (D. Kerr, 1988 / Holway).
     *
     * REGLAS:
     * - NO usar densidad + Siri.
     * - NO calcular músculo por diferencia.
     * - Usar exclusivamente las fórmulas/constantes provistas por el Excel.
     *
     * Entradas (unidades):
     * - Peso P (kg), Talla T (cm), Talla sentado TS (cm), Sexo, Edad (años; si no viene, se calcula)
     * - Diámetros (cm): DBA, DTT, DTAP, DBI, DH, DF
     * - Perímetros (cm): PCAB, PBR, PAM, PTX, PCIN, PMUS, PPAN
     * - Pliegues (mm): PTRI, PSUB, PSUP, PABD, PMED, PPANPL
     */
    private function calcular5Componentes($historial, $paciente)
    {
        $P = floatval($historial->peso_actual ?? 0);
        $T = floatval($historial->altura_actual ?? 0);
        $TS = floatval($historial->altura_sentado ?? 0);

        $generoRaw = strtoupper(trim($paciente->genero ?? ''));
        $esMujer = ($generoRaw === 'F' || $generoRaw === 'MUJER');
        $sexoTxt = $esMujer ? 'Mujer' : 'Hombre';

        $Edad = (int) $this->calcularEdad($paciente->fecha_nacimiento ?? null);
        if ($Edad === null || $Edad < 0) $Edad = 0;

        // ---- Diámetros (cm) ----
        $DBA = floatval($historial->diametro_biacromial ?? 0);
        $DTT = floatval($historial->diametro_torax_transverso ?? 0);
        $DTAP = floatval($historial->diametro_torax_anteroposterior ?? 0);
        $DBI = floatval($historial->diametro_bi_iliocristal ?? 0);
        $DH = floatval($historial->diametro_humero ?? 0);
        $DF = floatval($historial->diametro_femur ?? 0);

        // ---- Perímetros (cm) ----
        $PCAB = floatval($historial->circunferencia_cabeza ?? 0);
        $PBR = floatval($historial->circunferencia_brazo_relajado ?? 0);
        $PAM = floatval($historial->circunferencia_antebrazo_maximo ?? 0);
        $PTX = floatval($historial->circunferencia_torax ?? 0);
        $PCIN = floatval($historial->circunferencia_cintura ?? 0);
        $PMUS = floatval($historial->circunferencia_muslo_maximo ?? 0);
        $PPAN = floatval($historial->circunferencia_pantorrilla ?? 0);

        // ---- Pliegues (mm) ----
        $PTRI = floatval($historial->pliegue_tricipital ?? 0);
        $PSUB = floatval($historial->pliegue_subescapular ?? 0);
        $PSUP = floatval($historial->pliegue_supraespinal ?? 0); // Supraespinal (Holway)
        $PABD = floatval($historial->pliegue_abdominal ?? 0);
        $PMED = floatval($historial->pliegue_muslo_medial ?? 0);
        $PPANPL = floatval($historial->pliegue_pantorrilla_medial ?? 0); // Pantorrilla (pliegue)

        if ($P <= 0 || $T <= 0 || $TS <= 0) {
            throw new \Exception('Peso, talla y talla sentado son requeridos para el modelo 5 componentes (Holway)');
        }

        // Constantes (Excel)
        $T_REF = 170.18;
        $TS_REF = 89.92;
        $PI = 3.141;
        $PI_10 = 0.3141; // π/10 en el Excel

        // K (Área superficial)
        if ($Edad > 0 && $Edad < 12) {
            $K = 70.691;
        } else {
            $K = $esMujer ? 73.074 : 68.308;
        }

        // Grosor piel (GP)
        $GP = $esMujer ? 1.96 : 2.07;

        // Warnings recomendados (unidades / rangos típicos)
        $warnings = [];
        if ($DH > 15 || $DF > 15) {
            $warnings[] = '¿Ingresaste mm? Húmero/Fémur están en cm. Si ves valores > 15, casi seguro fueron mm.';
        }
        foreach ([
            'PTRI' => $PTRI, 'PSUB' => $PSUB, 'PSUP' => $PSUP, 'PABD' => $PABD, 'PMED' => $PMED, 'PPANPL' => $PPANPL
        ] as $k => $v) {
            if ($v > 0 && ($v < 1 || $v > 80)) {
                $warnings[] = "Revisá unidades: pliegue {$k} está en mm (típicamente 1–80).";
            }
        }
        foreach ([
            'DBA' => $DBA, 'DTT' => $DTT, 'DTAP' => $DTAP, 'DBI' => $DBI, 'DH' => $DH, 'DF' => $DF
        ] as $k => $v) {
            if ($v > 0 && ($v < 2 || $v > 12)) {
                $warnings[] = "Revisá unidades: diámetro {$k} está en cm (típicamente 2–12).";
            }
        }

        // Perímetros (cm) - rangos clínicos típicos (aprox). Si son muy bajos suele ser: pulgadas cargadas como cm.
        foreach ([
            'PCAB' => $PCAB, // 45–65
            'PBR' => $PBR,   // 18–45
            'PAM' => $PAM,   // 18–40
            'PTX' => $PTX,   // 60–130
            'PCIN' => $PCIN, // 50–140
            'PMUS' => $PMUS, // 30–90
            'PPAN' => $PPAN  // 25–60
        ] as $k => $v) {
            if ($v > 0 && $v < 15) {
                $warnings[] = "Perímetro {$k} muy bajo para cm ({$v}). ¿Ingresaste pulgadas?";
            }
        }

        // =========================
        // PASO 1 — MASA DE LA PIEL
        // =========================
        // AS = (K * P^0.425 * T^0.725) / 10000
        $AS = ($K * pow($P, 0.425) * pow($T, 0.725)) / 10000;
        // MPIEL = AS * GP * 1.05
        $MPIEL = $AS * $GP * 1.05;
        if ($MPIEL <= 0) {
            throw new \Exception('Resultado inválido: MPIEL <= 0. Revisá P (kg), T (cm), sexo/edad y unidades.');
        }

        // =========================
        // PASO 2 — MASA ADIPOSA
        // =========================
        // Σ6 = PTRI + PSUB + PSUP + PABD + PMED + PPANPL
        $SUM6 = $PTRI + $PSUB + $PSUP + $PABD + $PMED + $PPANPL;
        if ($SUM6 <= 0) {
            throw new \Exception('Se requieren los 6 pliegues (mm): tricipital, subescapular, supraespinal, abdominal, muslo medial, pantorrilla (pliegue)');
        }
        $ratioT = ($T_REF / $T);
        // Z_ADIP = ( (Σ6 * (T_REF / T)) - 116.41 ) / 34.79
        $Z_ADIP = (($SUM6 * $ratioT) - 116.41) / 34.79;
        // MADIP = ( (Z_ADIP * 5.85) + 25.6 ) / ( (T_REF / T)^3 )
        $MADIP = (($Z_ADIP * 5.85) + 25.6) / pow($ratioT, 3);
        if ($MADIP <= 0) {
            throw new \Exception('Resultado inválido: MADIP <= 0 (masa adiposa). Revisá Σ6 pliegues (mm) y T (cm).');
        }

        // =========================
        // PASO 3 — MASA MUSCULAR
        // =========================
        // Perímetros corregidos (pliegues mm -> cm con /10)
        $PBR_CORR = $PBR - (($PTRI * $PI) / 10);
        $PAM_CORR = $PAM;
        $PMUS_CORR = $PMUS - (($PMED * $PI) / 10);
        $PPAN_CORR = $PPAN - (($PPANPL * $PI) / 10);
        $PTX_CORR = $PTX - (($PSUB * $PI) / 10);

        $SUM_PER_CORR = $PBR_CORR + $PAM_CORR + $PMUS_CORR + $PPAN_CORR + $PTX_CORR;
        if ($SUM_PER_CORR <= 0) {
            throw new \Exception('Se requieren perímetros (cm) para masa muscular: brazo relajado, antebrazo máximo, muslo máximo, pantorrilla máxima, tórax mesoesternal');
        }
        // Z_MUSC = ( (SUM_PER_CORR * (T_REF / T)) - 207.21 ) / 13.74
        $Z_MUSC = (($SUM_PER_CORR * $ratioT) - 207.21) / 13.74;
        // MMUSC = ( (Z_MUSC * 5.4) + 24.5 ) / ( (T_REF / T)^3 )
        $MMUSC = (($Z_MUSC * 5.4) + 24.5) / pow($ratioT, 3);
        if ($MMUSC <= 0) {
            throw new \Exception(
                'Resultado inválido: MMUSC <= 0 (masa muscular). ' .
                'Revisá perímetros (cm) y pliegues (mm). Este error casi siempre ocurre por perímetros muy bajos (p. ej. pulgadas cargadas como cm) o algún perímetro faltante/incorrecto. ' .
                'Detalle: PBR_CORR=' . round($PBR_CORR, 2) . ', PAM_CORR=' . round($PAM_CORR, 2) . ', PMUS_CORR=' . round($PMUS_CORR, 2) . ', PPAN_CORR=' . round($PPAN_CORR, 2) . ', PTX_CORR=' . round($PTX_CORR, 2) .
                ' | SUM_PER_CORR=' . round($SUM_PER_CORR, 2) . ', Z_MUSC=' . round($Z_MUSC, 4) . ', (T_REF/T)=' . round($ratioT, 4)
            );
        }

        // =========================
        // PASO 4 — MASA RESIDUAL
        // =========================
        // PCIN_CORR = PCIN - (PABD * 0.3141)
        $PCIN_CORR = $PCIN - ($PABD * $PI_10);
        // SUM_TORAX = DTT + DTAP + PCIN_CORR
        $SUM_TORAX = $DTT + $DTAP + $PCIN_CORR;
        if ($SUM_TORAX <= 0) {
            throw new \Exception('Se requieren diámetros tórax (cm) y cintura (cm) para masa residual');
        }
        $ratioTS = ($TS_REF / $TS);
        // Z_RES = ( (SUM_TORAX * (TS_REF / TS)) - 109.35 ) / 7.08
        $Z_RES = (($SUM_TORAX * $ratioTS) - 109.35) / 7.08;
        // MRES = ( (Z_RES * 1.24) + 6.1 ) / ( (TS_REF / TS)^3 )
        $MRES = (($Z_RES * 1.24) + 6.1) / pow($ratioTS, 3);
        if ($MRES <= 0) {
            throw new \Exception(
                'Resultado inválido: MRES <= 0 (masa residual). ' .
                'Revisá DTT/DTAP (cm), PCIN (cm), PABD (mm) y TS (cm). ' .
                'Valores intermedios: SUM_TORAX=' . round($SUM_TORAX, 2) . ', Z_RES=' . round($Z_RES, 4) . ', (TS_REF/TS)=' . round($ratioTS, 4)
            );
        }

        // =========================
        // PASO 5 — MASA ÓSEA
        // =========================
        // Cabeza
        if ($PCAB <= 0) {
            throw new \Exception('Falta la circunferencia de cabeza (cm). Completala en la sección Circunferencias — es un perímetro con cinta, no un diámetro óseo.');
        }
        $Z_CAB = ($PCAB - 56) / 1.44;
        $MO_CAB = ($Z_CAB * 0.18) + 1.2;
        if ($MO_CAB <= 0) {
            throw new \Exception(
                'Circunferencia de cabeza inválida (' . round($PCAB, 1) . ' cm): el valor es muy bajo para 5 componentes. ' .
                'Medila con cinta en cm (típico 50–60), en Circunferencias — no en Diámetros óseos.'
            );
        }

        // Cuerpo
        if ($DBA <= 0 || $DBI <= 0 || $DH <= 0 || $DF <= 0) {
            throw new \Exception('Diámetros (cm) requeridos para masa ósea (cuerpo): biacromial, bi-iliocristal, húmero, fémur');
        }
        $SUM_DIAM = $DBA + $DBI + ($DH * 2) + ($DF * 2);
        $Z_OSEA = (($SUM_DIAM * $ratioT) - 98.88) / 5.33;
        $MO_CUE = (($Z_OSEA * 1.34) + 6.7) / pow($ratioT, 3);
        if ($MO_CUE <= 0) {
            throw new \Exception(
                'Resultado inválido: MO_CUE <= 0 (ósea cuerpo). ' .
                'Revisá diámetros DBA/DBI/DH/DF (cm). ' .
                'Valores intermedios: SUM_DIAM=' . round($SUM_DIAM, 2) . ', Z_OSEA=' . round($Z_OSEA, 4) . ', (T_REF/T)=' . round($ratioT, 4)
            );
        }

        $MO_TOTAL = $MO_CAB + $MO_CUE;

        // =========================
        // PASO 6 — TOTALES y %
        // =========================
        $M_TOTAL = $MADIP + $MMUSC + $MRES + $MO_TOTAL + $MPIEL;
        if ($M_TOTAL <= 0) {
            throw new \Exception(
                'No se pudo calcular la masa total (resultado inválido). ' .
                'Componentes: MADIP=' . round($MADIP, 2) . 'kg, MMUSC=' . round($MMUSC, 2) . 'kg, MRES=' . round($MRES, 2) . 'kg, ' .
                'MO_TOTAL=' . round($MO_TOTAL, 2) . 'kg, MPIEL=' . round($MPIEL, 2) . 'kg. ' .
                'Verificá datos y unidades (cm/mm) y rangos.'
            );
        }

        $pctADIP = ($MADIP / $M_TOTAL) * 100;
        $pctMUSC = ($MMUSC / $M_TOTAL) * 100;
        $pctRES = ($MRES / $M_TOTAL) * 100;
        $pctOSEO = ($MO_TOTAL / $M_TOTAL) * 100;
        $pctPIEL = ($MPIEL / $M_TOTAL) * 100;

        // Cierre de peso Kerr: peso medido vs peso reconstituido por el modelo
        $deltaPesoKg = $M_TOTAL - $P;
        $deltaPesoPct = ($P > 0) ? (($deltaPesoKg / $P) * 100) : 0;
        $avisoDeltaPeso = abs($deltaPesoPct) > 7;

        return [
            'metodo' => '5-componentes',
            'nombre' => 'Fraccionamiento 5 componentes (Kerr/Holway - Excel)',
            'warnings' => $warnings,
            'cierre_peso' => [
                'peso_medido_kg' => round($P, 2),
                'peso_reconstituido_kg' => round($M_TOTAL, 2),
                'delta_kg' => round($deltaPesoKg, 2),
                'delta_pct' => round($deltaPesoPct, 2),
                'aviso_delta' => $avisoDeltaPeso,
            ],
            'componentes' => [
                // Formato solicitado
                'masa_adiposa' => ['kg' => round($MADIP, 2), 'porcentaje' => round($pctADIP, 2)],
                'masa_muscular' => ['kg' => round($MMUSC, 2), 'porcentaje' => round($pctMUSC, 2)],
                'masa_residual' => ['kg' => round($MRES, 2), 'porcentaje' => round($pctRES, 2)],
                'masa_osea_total' => [
                    'kg' => round($MO_TOTAL, 2),
                    'porcentaje' => round($pctOSEO, 2),
                    'osea_cabeza_kg' => round($MO_CAB, 2),
                    'osea_cuerpo_kg' => round($MO_CUE, 2),
                ],
                'masa_piel' => ['kg' => round($MPIEL, 2), 'porcentaje' => round($pctPIEL, 2)],
                'masa_total' => ['kg' => round($M_TOTAL, 2), 'porcentaje' => 100.0],

                // Compatibilidad con UI/Charts existentes
                'grasa' => ['kg' => round($MADIP, 2), 'porcentaje' => round($pctADIP, 2)],
                'musculo' => ['kg' => round($MMUSC, 2), 'porcentaje' => round($pctMUSC, 2)],
                'residual' => ['kg' => round($MRES, 2), 'porcentaje' => round($pctRES, 2)],
                'hueso' => ['kg' => round($MO_TOTAL, 2), 'porcentaje' => round($pctOSEO, 2)],
                'piel' => ['kg' => round($MPIEL, 2), 'porcentaje' => round($pctPIEL, 2)],
            ],
            'datos_usados' => [
                'peso' => $P,
                'talla' => $T,
                'talla_sentado' => $TS,
                'edad' => $Edad,
                'sexo' => $sexoTxt,
                // diámetros
                'diametro_biacromial' => $DBA,
                'diametro_torax_transverso' => $DTT,
                'diametro_torax_anteroposterior' => $DTAP,
                'diametro_bi_iliocristal' => $DBI,
                'diametro_humero' => $DH,
                'diametro_femur' => $DF,
                // perímetros
                'circunferencia_cabeza' => $PCAB,
                'circunferencia_brazo_relajado' => $PBR,
                'circunferencia_antebrazo_maximo' => $PAM,
                'circunferencia_torax' => $PTX,
                'circunferencia_cintura' => $PCIN,
                'circunferencia_muslo_maximo' => $PMUS,
                'circunferencia_pantorrilla' => $PPAN,
                // pliegues
                'pliegue_tricipital' => $PTRI,
                'pliegue_subescapular' => $PSUB,
                'pliegue_supraespinal' => $PSUP,
                'pliegue_abdominal' => $PABD,
                'pliegue_muslo_medial' => $PMED,
                'pliegue_pantorrilla_medial' => $PPANPL,
            ],
            'pasos_calculo' => [
                // Requeridos por auditoría (mínimo)
                'k_area_superficial' => $K,
                'gp_grosor_piel' => $GP,
                'as_area_superficial' => round($AS, 6),
                'mpiell_masa_piel_kg' => round($MPIEL, 6),
                'suma_6_pliegues_mm' => round($SUM6, 2),
                'z_adip' => round($Z_ADIP, 6),
                'madip_kg' => round($MADIP, 6),
                'pbr_corr' => round($PBR_CORR, 4),
                'pmus_corr' => round($PMUS_CORR, 4),
                'ppan_corr' => round($PPAN_CORR, 4),
                'ptx_corr' => round($PTX_CORR, 4),
                'sum_per_corr' => round($SUM_PER_CORR, 4),
                'z_musc' => round($Z_MUSC, 6),
                'mmusc_kg' => round($MMUSC, 6),
                'pcin_corr' => round($PCIN_CORR, 4),
                'sum_torax' => round($SUM_TORAX, 4),
                'z_res' => round($Z_RES, 6),
                'mres_kg' => round($MRES, 6),
                'sum_diam' => round($SUM_DIAM, 4),
                'z_cab' => round($Z_CAB, 6),
                'z_osea' => round($Z_OSEA, 6),
            ]
        ];
    }
    
    /**
     * Calcular Somatotipo (Heath-Carter)
     */
    /**
     * Somatotipo Heath-Carter — Excel «SOMATOTIPO BASICO.xls» (hoja DATOS).
     *
     * Endo: cúbica sobre TRC + SSC + SSP (supraespinal).
     * Meso: 0.858·HUM + 0.601·FEM + 0.188·PCB + 0.161·PCP − 0.131·T + 4.5
     *       (PCB = brazo flex − TRC/10; PCP = pantorrilla máx − pliegue pantorrilla/10).
     * Ecto: HWR = T / P^(1/3) con tramos 38.28 / 40.75.
     */
    private function calcularSomatotipo($historial, $paciente)
    {
        $peso = floatval($historial->peso_actual ?? 0);
        $altura = floatval($historial->altura_actual ?? 0);
        $pliegue_tricipital = floatval($historial->pliegue_tricipital ?? 0);
        $pliegue_subescapular = floatval($historial->pliegue_subescapular ?? 0);
        $pliegue_supraespinal = floatval($historial->pliegue_supraespinal ?? 0);
        $pliegue_pantorrilla = floatval($historial->pliegue_pantorrilla_medial ?? 0);
        $diametro_humero = floatval($historial->diametro_humero ?? 0);
        $diametro_femur = floatval($historial->diametro_femur ?? 0);
        $circunferencia_brazo_contraido = floatval($historial->circunferencia_brazo_contraido ?? 0);
        $circunferencia_pantorrilla = floatval($historial->circunferencia_pantorrilla ?? 0);

        if ($peso <= 0 || $altura <= 0) {
            throw new \Exception('Peso y talla son requeridos para el somatotipo (Heath-Carter)');
        }
        if ($pliegue_tricipital <= 0 || $pliegue_subescapular <= 0 || $pliegue_supraespinal <= 0) {
            throw new \Exception('Se requieren pliegues (mm): tríceps, subescapular y supraespinal');
        }
        if ($diametro_humero <= 0 || $diametro_femur <= 0) {
            throw new \Exception('Se requieren diámetros (cm): húmero y fémur');
        }
        if ($circunferencia_brazo_contraido <= 0 || $circunferencia_pantorrilla <= 0) {
            throw new \Exception('Se requieren circunferencias (cm): brazo flexionado y pantorrilla máxima');
        }

        $suma_pliegues_endo = $pliegue_tricipital + $pliegue_subescapular + $pliegue_supraespinal;
        $endomorfia = -0.7182
            + (0.1451 * $suma_pliegues_endo)
            - (0.00068 * pow($suma_pliegues_endo, 2))
            + (0.0000014 * pow($suma_pliegues_endo, 3));

        $pcb = $circunferencia_brazo_contraido - ($pliegue_tricipital / 10);
        $pcp = $circunferencia_pantorrilla - ($pliegue_pantorrilla / 10);
        $mesomorfia = (0.858 * $diametro_humero)
            + (0.601 * $diametro_femur)
            + (0.188 * $pcb)
            + (0.161 * $pcp)
            - (0.131 * $altura)
            + 4.5;

        $altura_peso_ratio = $altura / pow($peso, 1 / 3);
        if ($altura_peso_ratio <= 38.28) {
            $ectomorfia = 0.1;
        } elseif ($altura_peso_ratio < 40.75) {
            $ectomorfia = (0.463 * $altura_peso_ratio) - 17.63;
        } else {
            $ectomorfia = (0.732 * $altura_peso_ratio) - 28.58;
        }

        $coordX = $ectomorfia - $endomorfia;
        $coordY = (2 * $mesomorfia) - ($endomorfia + $ectomorfia);

        $valores = ['endomorfo' => $endomorfia, 'mesomorfo' => $mesomorfia, 'ectomorfo' => $ectomorfia];
        $tipo_dominante = array_search(max($valores), $valores);

        return [
            'metodo' => 'somatotipo',
            'nombre' => 'Somatotipo (Heath-Carter)',
            'componentes' => [
                'endomorfia' => round($endomorfia, 2),
                'mesomorfia' => round($mesomorfia, 2),
                'ectomorfia' => round($ectomorfia, 2),
            ],
            'coordenadas_somatochart' => [
                'x' => round($coordX, 2),
                'y' => round($coordY, 2),
            ],
            'tipo_dominante' => $tipo_dominante,
            'descripcion' => $this->getDescripcionSomatotipo($endomorfia, $mesomorfia, $ectomorfia),
            'datos_usados' => [
                'peso' => $peso,
                'altura' => $altura,
                'suma_3_pliegues' => round($suma_pliegues_endo, 2),
                'perimetro_brazo_corregido' => round($pcb, 2),
                'perimetro_pantorrilla_corregido' => round($pcp, 2),
                'diametros' => ['humero' => $diametro_humero, 'femur' => $diametro_femur],
            ],
            'pasos_calculo' => [
                'suma_3_pliegues_endo' => round($suma_pliegues_endo, 2),
                'endomorfia' => round($endomorfia, 2),
                'perimetro_brazo_corregido' => round($pcb, 2),
                'perimetro_pantorrilla_corregido' => round($pcp, 2),
                'mesomorfia' => round($mesomorfia, 2),
                'altura_peso_ratio' => round($altura_peso_ratio, 2),
                'ectomorfia' => round($ectomorfia, 2),
                'coordenada_x' => round($coordX, 2),
                'coordenada_y' => round($coordY, 2),
            ],
        ];
    }
    
    /**
     * Obtener descripción del somatotipo
     */
    private function getDescripcionSomatotipo($endo, $meso, $ecto)
    {
        $descripciones = [
            'endomorfo' => 'Predominio de grasa corporal. Típicamente cuerpo redondeado con tendencia a acumular grasa.',
            'mesomorfo' => 'Predominio de masa muscular y estructura ósea. Típicamente cuerpo atlético y musculoso.',
            'ectomorfo' => 'Predominio de estructura lineal. Típicamente cuerpo delgado con poca grasa y músculo.'
        ];
        
        $tipo = array_search(max(['endomorfo' => $endo, 'mesomorfo' => $meso, 'ectomorfo' => $ecto]), ['endomorfo' => $endo, 'mesomorfo' => $meso, 'ectomorfo' => $ecto]);
        
        return $descripciones[$tipo] ?? 'Somatotipo balanceado';
    }
    
    /**
     * Verificar si hay datos suficientes para un método
     * Campos alineados con especificación y Excel (2: Kerr; 4: Fisionutdep; 5: Francis Holway)
     */
    public function verificarDatosDisponibles($metodoSlug, $historial)
    {
        $requiere_datos = [
            // 2 comp Kerr (Excel KG_% DE MM Y MA): Σ6 pliegues + 5 perímetros corregidos
            '2-componentes' => [
                'peso_actual', 'altura_actual',
                'pliegue_tricipital', 'pliegue_subescapular', 'pliegue_supraespinal', 'pliegue_abdominal',
                'pliegue_muslo_medial', 'pliegue_pantorrilla_medial',
                'circunferencia_brazo_relajado', 'circunferencia_antebrazo_maximo', 'circunferencia_torax',
                'circunferencia_muslo_maximo', 'circunferencia_pantorrilla',
            ],
            // 4 comp Fisionutdep.xlsm: Durnin + Lee + ósea (diámetro muñeca G34 + fémur D36)
            '4-componentes' => [
                'peso_actual', 'altura_actual',
                'pliegue_tricipital', 'pliegue_subescapular', 'pliegue_suprailíaco', 'pliegue_bicipital',
                'pliegue_pantorrilla_medial', 'pliegue_muslo_medial',
                'circunferencia_brazo_relajado', 'circunferencia_pantorrilla', 'circunferencia_muslo_medio',
                'diametro_muneca', 'diametro_femur',
            ],
            // 5 comp Kerr: P, T, Sexo; 6 pliegues (TR, SE, SI, AB, MME, PA); DH, DF. Sin perímetros.
            '5-componentes' => [
                // Holway (Excel Antropgym): P, T, TS; diámetros; perímetros; pliegues (mm)
                'peso_actual', 'altura_actual', 'altura_sentado',

                // Diámetros (cm)
                'diametro_biacromial', 'diametro_torax_transverso', 'diametro_torax_anteroposterior',
                'diametro_bi_iliocristal', 'diametro_humero', 'diametro_femur',

                // Perímetros (cm)
                'circunferencia_cabeza', 'circunferencia_brazo_relajado', 'circunferencia_antebrazo_maximo',
                'circunferencia_torax', 'circunferencia_cintura', 'circunferencia_muslo_maximo', 'circunferencia_pantorrilla',

                // Pliegues (mm) — OJO: usa supraespinal (no suprailíaco)
                'pliegue_tricipital', 'pliegue_subescapular', 'pliegue_supraespinal',
                'pliegue_abdominal', 'pliegue_muslo_medial', 'pliegue_pantorrilla_medial'
            ],
            // Somatotipo BASICO.xls: TRC, SSC, SSP + diámetros + brazo flex / pantorrilla corregidos
            'somatotipo' => [
                'peso_actual', 'altura_actual',
                'pliegue_tricipital', 'pliegue_subescapular', 'pliegue_supraespinal', 'pliegue_pantorrilla_medial',
                'diametro_humero', 'diametro_femur',
                'circunferencia_brazo_contraido', 'circunferencia_pantorrilla',
            ],
        ];
        
        if (!isset($requiere_datos[$metodoSlug])) {
            return ['disponible' => false, 'faltantes' => []];
        }
        
        $faltantes = [];
        foreach ($requiere_datos[$metodoSlug] as $campo) {
            $valor = $historial->$campo ?? null;
            if ($valor === null || $valor === '' || (is_numeric($valor) && floatval($valor) <= 0)) {
                $faltantes[] = $campo;
            }
        }
        
        return [
            'disponible' => empty($faltantes),
            'faltantes' => $faltantes
        ];
    }
}
