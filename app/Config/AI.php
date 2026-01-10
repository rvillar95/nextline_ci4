<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AI extends BaseConfig
{
    /**
     * Configuración para OpenAI
     */
    public string $openaiApiKey = '';
    public string $openaiModel = 'gpt-4o-mini'; // gpt-4o-mini (económico) o gpt-4 (mejor calidad)
    public int $openaiMaxTokens = 2000;
    public float $openaiTemperature = 0.7;

    /**
     * Configuración para Google Cloud AI
     */
    public string $googleApiKey = '';
    public string $googleProjectId = '';
    public string $googleLocation = 'us-central1';

    /**
     * Configuración para Anthropic Claude
     */
    public string $anthropicApiKey = '';
    public string $anthropicModel = 'claude-3-haiku-20240307';

    /**
     * Proveedor por defecto
     * Opciones: 'openai', 'google', 'anthropic'
     */
    public string $defaultProvider = 'openai';

    /**
     * Habilitar/deshabilitar funcionalidades de IA
     */
    public bool $habilitarGeneracionItems = true;
    public bool $habilitarGeneracionDescripciones = true;
    public bool $habilitarSugerenciasPrecio = true;
    public bool $habilitarAnalisisImagenes = false;

    /**
     * Rate limiting
     */
    public int $maxRequestsPorMinuto = 10;
    public int $maxRequestsPorDia = 100;

    /**
     * Timeout para requests a APIs de IA (segundos)
     */
    public int $timeout = 30;
}
