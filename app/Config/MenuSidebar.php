<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Iconos disponibles para el menú lateral (Feather).
 */
class MenuSidebar extends BaseConfig
{
    /** @var array<string, string> slug => etiqueta */
    public array $iconos = [
        'home'           => 'Inicio / casa',
        'message-circle' => 'Mensajes',
        'calendar'       => 'Agenda / calendario',
        'users'          => 'Personas / equipo',
        'clipboard'      => 'Historial / clipboard',
        'file-text'      => 'Documentos',
        'tag'            => 'Tarifas / etiqueta',
        'dollar-sign'    => 'Dinero / cobros',
        'settings'       => 'Configuración',
        'user'           => 'Usuario / perfil',
        'briefcase'      => 'Empresa',
        'circle'         => 'Genérico',
    ];
}
