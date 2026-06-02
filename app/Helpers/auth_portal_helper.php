<?php

/**
 * Portal alumno (gym) vs dashboard NutriNext / staff.
 * No usar solo `poder`: Nutricionista también tiene poder 1.
 */
function es_usuario_portal_alumno(?array $usuario): bool
{
    if ($usuario === null || $usuario === []) {
        return false;
    }

    return strcasecmp(trim((string) ($usuario['perfil_nombre'] ?? '')), 'Alumno') === 0;
}

/**
 * Agenda, consulta activa, calendario y notificaciones clínicas del layout dashboard.
 */
function usuario_dashboard_tiene_modulo_clinico(?array $usuario): bool
{
    if ($usuario === null || $usuario === [] || empty($usuario['perfil_id'])) {
        return false;
    }

    $perfilNombre = trim((string) ($usuario['perfil_nombre'] ?? ''));
    if (strcasecmp($perfilNombre, 'Entrenador') === 0 || strcasecmp($perfilNombre, 'Alumno') === 0) {
        return false;
    }

    $moduloDetalle = new \App\Models\ModuloDetalle();
    $modulos = $moduloDetalle->getMenu((int) $usuario['perfil_id']);

    $patronesClinicos = [
        '/dashboard/agenda',
        '/dashboard/paciente',
        '/dashboard/pacientes',
        '/dashboard/consulta',
        '/dashboard/ficha',
    ];

    foreach ($modulos as $modulo) {
        $ruta = (string) ($modulo['ruta'] ?? '');
        foreach ($patronesClinicos as $patron) {
            if ($ruta !== '' && stripos($ruta, $patron) !== false) {
                return true;
            }
        }
    }

    return false;
}
