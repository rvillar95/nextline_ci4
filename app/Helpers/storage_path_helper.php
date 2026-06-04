<?php

/**
 * Rutas de almacenamiento (disco o GCS).
 * Prefijo en BD: private/empresa/{empresa_id}/nutricionista/{nutricionista_id}/...
 */

/**
 * @return array{empresa_id: int, nutricionista_id: int}
 */
function storage_context_from_session(?array $usuario = null): array
{
    $usuario ??= session()->get('usuario') ?? [];

    return [
        'empresa_id'       => (int) ($usuario['empresa_id'] ?? 0),
        'nutricionista_id' => (int) ($usuario['id'] ?? 0),
    ];
}

/**
 * @return array{empresa_id: int, nutricionista_id: int}
 */
function storage_context_for_usuario(int $usuarioId): array
{
    $db = \Config\Database::connect();
    $row = $db->table('usuario')
        ->select('id, empresa_id')
        ->where('id', $usuarioId)
        ->get()
        ->getRowArray();

    return [
        'empresa_id'       => (int) ($row['empresa_id'] ?? 0),
        'nutricionista_id' => $usuarioId,
    ];
}

function storage_segment_empresa(int $empresaId): string
{
    return 'empresa/' . ($empresaId > 0 ? (string) $empresaId : '0');
}

function storage_segment_nutricionista(int $nutricionistaId): string
{
    return 'nutricionista/' . ($nutricionistaId > 0 ? (string) $nutricionistaId : '0');
}

function storage_folder_empresa_nutricionista(int $empresaId, int $nutricionistaId): string
{
    return storage_segment_empresa($empresaId) . '/' . storage_segment_nutricionista($nutricionistaId);
}

/** Credenciales del profesional (mi perfil). */
function storage_folder_credenciales(int $empresaId, int $nutricionistaId): string
{
    return storage_folder_empresa_nutricionista($empresaId, $nutricionistaId) . '/credenciales';
}

/** Documentos clínicos de un paciente. */
function storage_folder_documento_paciente(int $empresaId, int $nutricionistaId, int $pacienteId): string
{
    return storage_folder_empresa_nutricionista($empresaId, $nutricionistaId)
        . '/pacientes/' . ($pacienteId > 0 ? (string) $pacienteId : '0')
        . '/documentos';
}
