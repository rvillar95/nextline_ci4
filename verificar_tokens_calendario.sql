-- =====================================================
-- QUERIES PARA VERIFICAR TOKENS DE CALENDARIO
-- =====================================================
-- Este script contiene queries SQL para verificar
-- los tokens de OAuth2 guardados en la base de datos
-- =====================================================

-- 1. Ver todos los tokens guardados
SELECT 
    id,
    usuario_id,
    provider,
    LEFT(access_token, 50) as access_token_preview,
    LEFT(refresh_token, 50) as refresh_token_preview,
    expires_at,
    token_type,
    fcreacion,
    factualizacion,
    CASE 
        WHEN expires_at > NOW() THEN 'Válido'
        ELSE 'Expirado'
    END as estado_token
FROM usuario_calendar_tokens
ORDER BY fcreacion DESC;

-- 2. Ver tokens de un usuario específico (reemplaza 19 con el ID del usuario)
SELECT 
    id,
    usuario_id,
    provider,
    LEFT(access_token, 50) as access_token_preview,
    LEFT(refresh_token, 50) as refresh_token_preview,
    expires_at,
    token_type,
    fcreacion,
    factualizacion,
    CASE 
        WHEN expires_at > NOW() THEN 'Válido'
        ELSE 'Expirado'
    END as estado_token,
    TIMESTAMPDIFF(SECOND, NOW(), expires_at) as segundos_restantes
FROM usuario_calendar_tokens
WHERE usuario_id = 19  -- Reemplaza con el ID del usuario
ORDER BY fcreacion DESC;

-- 3. Ver tokens de Google Calendar específicamente
SELECT 
    uct.id,
    uct.usuario_id,
    u.nombre as nombre_usuario,
    uct.provider,
    LEFT(uct.access_token, 50) as access_token_preview,
    LEFT(uct.refresh_token, 50) as refresh_token_preview,
    uct.expires_at,
    uct.token_type,
    uct.fcreacion,
    CASE 
        WHEN uct.expires_at > NOW() THEN 'Válido'
        ELSE 'Expirado'
    END as estado_token
FROM usuario_calendar_tokens uct
LEFT JOIN usuario u ON u.id = uct.usuario_id
WHERE uct.provider = 'google'
ORDER BY uct.fcreacion DESC;

-- 4. Verificar si un usuario tiene token guardado
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN 'Sí tiene token'
        ELSE 'No tiene token'
    END as tiene_token,
    COUNT(*) as cantidad_tokens
FROM usuario_calendar_tokens
WHERE usuario_id = 19  -- Reemplaza con el ID del usuario
  AND provider = 'google';

-- 5. Ver tokens expirados
SELECT 
    id,
    usuario_id,
    provider,
    expires_at,
    TIMESTAMPDIFF(HOUR, expires_at, NOW()) as horas_expirado,
    fcreacion
FROM usuario_calendar_tokens
WHERE expires_at < NOW()
ORDER BY expires_at DESC;

-- 6. Ver tokens válidos (no expirados)
SELECT 
    id,
    usuario_id,
    provider,
    expires_at,
    TIMESTAMPDIFF(HOUR, NOW(), expires_at) as horas_restantes,
    fcreacion
FROM usuario_calendar_tokens
WHERE expires_at > NOW()
ORDER BY expires_at ASC;

-- 7. Ver el token completo de un usuario (CUIDADO: muestra tokens completos)
SELECT 
    id,
    usuario_id,
    provider,
    access_token,
    refresh_token,
    expires_at,
    token_type,
    fcreacion,
    factualizacion
FROM usuario_calendar_tokens
WHERE usuario_id = 19  -- Reemplaza con el ID del usuario
  AND provider = 'google'
LIMIT 1;

-- 8. Contar tokens por proveedor
SELECT 
    provider,
    COUNT(*) as total_tokens,
    SUM(CASE WHEN expires_at > NOW() THEN 1 ELSE 0 END) as tokens_validos,
    SUM(CASE WHEN expires_at < NOW() THEN 1 ELSE 0 END) as tokens_expirados
FROM usuario_calendar_tokens
GROUP BY provider;

-- 9. Ver últimos tokens guardados (últimas 10)
SELECT 
    id,
    usuario_id,
    provider,
    LEFT(access_token, 30) as access_token_preview,
    expires_at,
    fcreacion,
    factualizacion
FROM usuario_calendar_tokens
ORDER BY fcreacion DESC
LIMIT 10;

-- 10. Verificar estructura de la tabla
DESCRIBE usuario_calendar_tokens;

-- =====================================================
-- NOTAS
-- =====================================================
-- - Los tokens de acceso (access_token) expiran en ~1 hora
-- - Los tokens de refresco (refresh_token) no expiran
-- - Si un token expira, se usa refresh_token para obtener uno nuevo
-- - Cada usuario puede tener un token por proveedor (google/outlook)
-- - La tabla tiene una restricción UNIQUE en (usuario_id, provider)
