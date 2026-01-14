-- =====================================================
-- AGREGAR RUTAS DE INTEGRACIÓN CON CALENDARIO
-- =====================================================
-- Este script agrega los permisos necesarios para la
-- integración con Google Calendar y Outlook Calendar
-- en el módulo de Agenda (modulo_id = 33)
-- =====================================================

INSERT INTO `modulo_detalle`
(`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(235, 33, 'Conectar Calendario (Google/Outlook)', '/calendario/connect', 'editar', 'A', 'S', 20, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = VALUES(`descripcion`),
    `accion` = VALUES(`accion`),
    `estado` = 'A',
    `mostrar` = VALUES(`mostrar`),
    `orden` = VALUES(`orden`),
    `factualizacion` = NOW();

-- NOTA: /calendario/callback no necesita permiso porque es una ruta pública
-- que recibe el callback de OAuth2 desde Google/Microsoft

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que se agregó correctamente
SELECT 
    id, 
    modulo_id, 
    descripcion, 
    ruta, 
    accion, 
    estado, 
    mostrar,
    orden
FROM modulo_detalle 
WHERE modulo_id = 33 AND ruta = '/calendario/connect';

-- Verificar la ruta completa que debería estar permitida
SELECT 
    'RUTA COMPLETA' as tipo,
    CONCAT(m.ruta, md.ruta) as ruta_completa,
    md.descripcion,
    md.accion,
    pm.editar as tiene_permiso_editar
FROM modulo m
JOIN modulo_detalle md ON md.modulo_id = m.id
LEFT JOIN perfil_modulo pm ON pm.modulo_id = m.id
WHERE m.id = 33
  AND md.ruta = '/calendario/connect'
  AND pm.perfil_id = 9; -- Ajusta el perfil_id según corresponda

-- =====================================================
-- NOTAS
-- =====================================================
-- 1. La ruta /calendar/connect requiere permiso 'editar' porque
--    es una acción de configuración que modifica los tokens del usuario
-- 
-- 2. La ruta /calendar/callback NO necesita permiso porque:
--    - Es una ruta pública que recibe el callback de OAuth2
--    - No requiere autenticación (el token viene en el state)
--    - Es similar a las rutas /confirmar-cita y /cancelar-cita
--
-- 3. Si necesitas que aparezca en el menú, cambia mostrar = 'S'
--    Si solo debe estar disponible pero no en el menú, mostrar = 'N'
