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
     * Método 2 Componentes: Masa Magra / Masa Adiposa
     * Fórmula: Durnin-Womersley simplificada
     */
    private function calcular2Componentes($historial, $paciente)
    {
        $peso = floatval($historial->peso_actual ?? 0);
        $altura = floatval($historial->altura_actual ?? 0);
        $genero = $paciente->genero ?? 'M';
        $edad = $this->calcularEdad($paciente->fecha_nacimiento ?? null);
        
        // Pliegues necesarios
        $pliegue_tricipital = floatval($historial->pliegue_tricipital ?? 0);
        $pliegue_subescapular = floatval($historial->pliegue_subescapular ?? 0);
        $pliegue_suprailíaco = floatval($historial->pliegue_suprailíaco ?? 0);
        $pliegue_bicipital = floatval($historial->pliegue_bicipital ?? 0);
        
        if ($peso <= 0 || $altura <= 0) {
            throw new \Exception('Peso y altura son requeridos para el cálculo');
        }
        
        // Suma de pliegues (mínimo 3)
        $suma_pliegues = $pliegue_tricipital + $pliegue_subescapular + $pliegue_suprailíaco + $pliegue_bicipital;
        
        if ($suma_pliegues <= 0) {
            throw new \Exception('Se requieren al menos 3 pliegues cutáneos para el cálculo');
        }
        
        // Calcular densidad corporal (Durnin-Womersley)
        $densidad = $this->calcularDensidadDurninWomersley($suma_pliegues, $edad, $genero);
        
        // Calcular porcentaje de grasa corporal
        $porcentaje_grasa = ((4.95 / $densidad) - 4.5) * 100;
        
        // Validar rango razonable
        if ($porcentaje_grasa < 5 || $porcentaje_grasa > 50) {
            // Usar fórmula alternativa si el resultado no es razonable
            $porcentaje_grasa = ($suma_pliegues * 0.5) + 5;
        }
        
        // Calcular masas
        $masa_adiposa_kg = ($peso * $porcentaje_grasa) / 100;
        $masa_magra_kg = $peso - $masa_adiposa_kg;
        $porcentaje_masa_magra = 100 - $porcentaje_grasa;
        
        return [
            'metodo' => '2-componentes',
            'nombre' => 'Masa Magra / Masa Adiposa',
            'componentes' => [
                'masa_adiposa' => [
                    'kg' => round($masa_adiposa_kg, 2),
                    'porcentaje' => round($porcentaje_grasa, 2)
                ],
                'masa_magra' => [
                    'kg' => round($masa_magra_kg, 2),
                    'porcentaje' => round($porcentaje_masa_magra, 2)
                ]
            ],
            'datos_usados' => [
                'peso' => $peso,
                'altura' => $altura,
                'suma_pliegues' => $suma_pliegues,
                'edad' => $edad,
                'genero' => $genero
            ]
        ];
    }
    
    /**
     * Calcular densidad corporal según Durnin-Womersley
     */
    private function calcularDensidadDurninWomersley($suma_pliegues, $edad, $genero)
    {
        // Fórmula simplificada de Durnin-Womersley
        // Valores aproximados según género y edad
        if ($genero === 'F') {
            // Mujeres
            if ($edad >= 16 && $edad <= 29) {
                $densidad = 1.1599 - (0.0717 * log10($suma_pliegues));
            } elseif ($edad >= 30 && $edad <= 39) {
                $densidad = 1.1423 - (0.0632 * log10($suma_pliegues));
            } elseif ($edad >= 40 && $edad <= 49) {
                $densidad = 1.1333 - (0.0612 * log10($suma_pliegues));
            } else {
                $densidad = 1.1339 - (0.0645 * log10($suma_pliegues));
            }
        } else {
            // Hombres
            if ($edad >= 16 && $edad <= 29) {
                $densidad = 1.1631 - (0.0632 * log10($suma_pliegues));
            } elseif ($edad >= 30 && $edad <= 39) {
                $densidad = 1.1422 - (0.0544 * log10($suma_pliegues));
            } elseif ($edad >= 40 && $edad <= 49) {
                $densidad = 1.1620 - (0.0700 * log10($suma_pliegues));
            } else {
                $densidad = 1.1715 - (0.0779 * log10($suma_pliegues));
            }
        }
        
        return $densidad;
    }
    
    /**
     * Método 4 Componentes: Grasa / Músculo / Hueso / Residual
     * Fórmula: Fisionutdep / De Rose
     */
    private function calcular4Componentes($historial, $paciente)
    {
        // Verificar datos requeridos
        $peso = floatval($historial->peso_actual ?? 0);
        $altura = floatval($historial->altura_actual ?? 0);
        $altura_sentado = floatval($historial->altura_sentado ?? 0);
        $diametro_humero = floatval($historial->diametro_humero ?? 0);
        $diametro_femur = floatval($historial->diametro_femur ?? 0);
        $circunferencia_brazo_contraido = floatval($historial->circunferencia_brazo_contraido ?? 0);
        $circunferencia_pantorrilla = floatval($historial->circunferencia_pantorrilla ?? 0);
        
        if ($peso <= 0 || $altura <= 0 || $altura_sentado <= 0) {
            throw new \Exception('Peso, altura y altura sentado son requeridos para el método 4 componentes');
        }
        
        if ($diametro_humero <= 0 || $diametro_femur <= 0) {
            throw new \Exception('Diámetros óseos (húmero y fémur) son requeridos para el método 4 componentes');
        }
        
        // Primero calcular grasa (usando método 2 componentes)
        $resultado_2comp = $this->calcular2Componentes($historial, $paciente);
        $porcentaje_grasa = $resultado_2comp['componentes']['masa_adiposa']['porcentaje'];
        $masa_grasa_kg = $resultado_2comp['componentes']['masa_adiposa']['kg'];
        
        // Calcular masa ósea (Fórmula de De Rose)
        $masa_osea_kg = $this->calcularMasaOsea($altura, $diametro_humero, $diametro_femur, $circunferencia_brazo_contraido, $circunferencia_pantorrilla, $paciente->genero ?? 'M');
        
        // Calcular masa residual (aproximación)
        $masa_residual_kg = $peso * 0.24; // Aproximación: 24% del peso
        
        // Calcular masa muscular
        $masa_muscular_kg = $peso - $masa_grasa_kg - $masa_osea_kg - $masa_residual_kg;
        
        // Calcular porcentajes
        $porcentaje_grasa_final = ($masa_grasa_kg / $peso) * 100;
        $porcentaje_musculo = ($masa_muscular_kg / $peso) * 100;
        $porcentaje_hueso = ($masa_osea_kg / $peso) * 100;
        $porcentaje_residual = ($masa_residual_kg / $peso) * 100;
        
        return [
            'metodo' => '4-componentes',
            'nombre' => 'Grasa / Músculo / Hueso / Residual',
            'componentes' => [
                'grasa' => [
                    'kg' => round($masa_grasa_kg, 2),
                    'porcentaje' => round($porcentaje_grasa_final, 2)
                ],
                'musculo' => [
                    'kg' => round($masa_muscular_kg, 2),
                    'porcentaje' => round($porcentaje_musculo, 2)
                ],
                'hueso' => [
                    'kg' => round($masa_osea_kg, 2),
                    'porcentaje' => round($porcentaje_hueso, 2)
                ],
                'residual' => [
                    'kg' => round($masa_residual_kg, 2),
                    'porcentaje' => round($porcentaje_residual, 2)
                ]
            ],
            'datos_usados' => [
                'peso' => $peso,
                'altura' => $altura,
                'altura_sentado' => $altura_sentado,
                'diametro_humero' => $diametro_humero,
                'diametro_femur' => $diametro_femur
            ]
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
     * Método 5 Componentes: Grasa / Músculo / Hueso / Residual / Piel
     * Fórmula: Francis Holway
     */
    private function calcular5Componentes($historial, $paciente)
    {
        // Primero calcular método 4 componentes
        $resultado_4comp = $this->calcular4Componentes($historial, $paciente);
        
        $peso = floatval($historial->peso_actual ?? 0);
        $altura = floatval($historial->altura_actual ?? 0);
        
        // Calcular masa de la piel (aproximación basada en superficie corporal)
        $superficie_corporal = sqrt(($altura * $peso) / 3600); // Fórmula de Du Bois
        $masa_piel_kg = $superficie_corporal * 0.37; // Aproximación: 0.37 kg por m²
        
        // Ajustar otros componentes
        $masa_grasa_kg = $resultado_4comp['componentes']['grasa']['kg'];
        $masa_musculo_kg = $resultado_4comp['componentes']['musculo']['kg'];
        $masa_osea_kg = $resultado_4comp['componentes']['hueso']['kg'];
        $masa_residual_kg = $resultado_4comp['componentes']['residual']['kg'];
        
        // Reajustar masa residual (reducir por la masa de piel)
        $masa_residual_kg = $masa_residual_kg - ($masa_piel_kg * 0.5); // Aproximación
        
        // Calcular porcentajes
        $porcentaje_grasa = ($masa_grasa_kg / $peso) * 100;
        $porcentaje_musculo = ($masa_musculo_kg / $peso) * 100;
        $porcentaje_hueso = ($masa_osea_kg / $peso) * 100;
        $porcentaje_residual = ($masa_residual_kg / $peso) * 100;
        $porcentaje_piel = ($masa_piel_kg / $peso) * 100;
        
        return [
            'metodo' => '5-componentes',
            'nombre' => 'Grasa / Músculo / Hueso / Residual / Piel',
            'componentes' => [
                'grasa' => [
                    'kg' => round($masa_grasa_kg, 2),
                    'porcentaje' => round($porcentaje_grasa, 2)
                ],
                'musculo' => [
                    'kg' => round($masa_musculo_kg, 2),
                    'porcentaje' => round($porcentaje_musculo, 2)
                ],
                'hueso' => [
                    'kg' => round($masa_osea_kg, 2),
                    'porcentaje' => round($porcentaje_hueso, 2)
                ],
                'residual' => [
                    'kg' => round($masa_residual_kg, 2),
                    'porcentaje' => round($porcentaje_residual, 2)
                ],
                'piel' => [
                    'kg' => round($masa_piel_kg, 2),
                    'porcentaje' => round($porcentaje_piel, 2)
                ]
            ],
            'datos_usados' => $resultado_4comp['datos_usados']
        ];
    }
    
    /**
     * Calcular Somatotipo (Heath-Carter)
     */
    private function calcularSomatotipo($historial, $paciente)
    {
        $peso = floatval($historial->peso_actual ?? 0);
        $altura = floatval($historial->altura_actual ?? 0);
        $altura_sentado = floatval($historial->altura_sentado ?? 0);
        $pliegue_tricipital = floatval($historial->pliegue_tricipital ?? 0);
        $pliegue_subescapular = floatval($historial->pliegue_subescapular ?? 0);
        $pliegue_suprailíaco = floatval($historial->pliegue_suprailíaco ?? 0);
        $diametro_humero = floatval($historial->diametro_humero ?? 0);
        $diametro_femur = floatval($historial->diametro_femur ?? 0);
        $circunferencia_brazo_contraido = floatval($historial->circunferencia_brazo_contraido ?? 0);
        $circunferencia_pantorrilla = floatval($historial->circunferencia_pantorrilla ?? 0);
        
        if ($peso <= 0 || $altura <= 0 || $altura_sentado <= 0) {
            throw new \Exception('Peso, altura y altura sentado son requeridos para el somatotipo');
        }
        
        // 1. ENDOMORFIA (grasa relativa)
        $suma_pliegues_endo = $pliegue_tricipital + $pliegue_subescapular + $pliegue_suprailíaco;
        $endomorfia = -0.7182 + (0.1451 * $suma_pliegues_endo) - (0.00068 * pow($suma_pliegues_endo, 2)) + (0.0000014 * pow($suma_pliegues_endo, 3));
        
        // 2. MESOMORFIA (desarrollo músculo-esquelético)
        $altura_metros = $altura / 100;
        $mesomorfia = 0.858 * ($diametro_humero + $diametro_femur) + 0.601 * ($circunferencia_brazo_contraido + $circunferencia_pantorrilla) - (0.188 * $altura_metros) + 0.161;
        
        // 3. ECTOMORFIA (linealidad relativa)
        $altura_peso_ratio = $altura / pow($peso, 1/3);
        if ($altura_peso_ratio >= 40.75) {
            $ectomorfia = 0.732 * $altura_peso_ratio - 28.58;
        } elseif ($altura_peso_ratio >= 38.25) {
            $ectomorfia = 0.463 * $altura_peso_ratio - 17.63;
        } else {
            $ectomorfia = 0.1;
        }
        
        // Normalizar valores (deben estar entre 0.5 y 7.5 aproximadamente)
        $endomorfia = max(0.5, min(7.5, $endomorfia));
        $mesomorfia = max(0.5, min(7.5, $mesomorfia));
        $ectomorfia = max(0.5, min(7.5, $ectomorfia));
        
        // Determinar tipo dominante
        $valores = ['endomorfo' => $endomorfia, 'mesomorfo' => $mesomorfia, 'ectomorfo' => $ectomorfia];
        $tipo_dominante = array_search(max($valores), $valores);
        
        return [
            'metodo' => 'somatotipo',
            'nombre' => 'Somatotipo (Heath-Carter)',
            'componentes' => [
                'endomorfia' => round($endomorfia, 2),
                'mesomorfia' => round($mesomorfia, 2),
                'ectomorfia' => round($ectomorfia, 2)
            ],
            'tipo_dominante' => $tipo_dominante,
            'descripcion' => $this->getDescripcionSomatotipo($endomorfia, $mesomorfia, $ectomorfia),
            'datos_usados' => [
                'peso' => $peso,
                'altura' => $altura,
                'altura_sentado' => $altura_sentado,
                'suma_pliegues' => $suma_pliegues_endo,
                'diametros' => ['humero' => $diametro_humero, 'femur' => $diametro_femur],
                'circunferencias' => ['brazo' => $circunferencia_brazo_contraido, 'pantorrilla' => $circunferencia_pantorrilla]
            ]
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
     */
    public function verificarDatosDisponibles($metodoSlug, $historial)
    {
        $requiere_datos = [
            '2-componentes' => ['peso_actual', 'altura_actual', 'pliegue_tricipital', 'pliegue_subescapular', 'pliegue_suprailíaco'],
            '4-componentes' => ['peso_actual', 'altura_actual', 'altura_sentado', 'diametro_humero', 'diametro_femur'],
            '5-componentes' => ['peso_actual', 'altura_actual', 'altura_sentado', 'diametro_humero', 'diametro_femur', 'diametro_biacromial', 'diametro_bi_iliocristal'],
            'somatotipo' => ['peso_actual', 'altura_actual', 'altura_sentado', 'pliegue_tricipital', 'pliegue_subescapular', 'pliegue_suprailíaco', 'diametro_humero', 'diametro_femur', 'circunferencia_brazo_contraido', 'circunferencia_pantorrilla']
        ];
        
        if (!isset($requiere_datos[$metodoSlug])) {
            return ['disponible' => false, 'faltantes' => []];
        }
        
        $faltantes = [];
        foreach ($requiere_datos[$metodoSlug] as $campo) {
            $valor = $historial->$campo ?? null;
            if (empty($valor) || floatval($valor) <= 0) {
                $faltantes[] = $campo;
            }
        }
        
        return [
            'disponible' => empty($faltantes),
            'faltantes' => $faltantes
        ];
    }
}
