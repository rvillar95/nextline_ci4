-- =====================================================
-- CREAR NUEVO CLIENTE CON PLAN PREMIUM
-- =====================================================
-- Este script crea una nueva empresa, asigna el paquete Premium,
-- crea el usuario y verifica que todo esté correcto
-- =====================================================

-- =====================================================
-- PASO 1: Crear la Empresa
-- =====================================================
-- Ajustar los datos según el cliente
INSERT INTO empresa (
    nombre, 
    nombre_comercial, 
    paquete_id,
    rut,
    direccion,
    telefono,
    email,
    estado,
    fcreacion
) VALUES (
    'Juanito Pérez Nutrición',           -- Nombre de la empresa
    'Clínica Nutrición JP',              -- Nombre comercial
    4,                                    -- ID del paquete Premium (ajustar si es diferente)
    '12345678-9',                         -- RUT (opcional)
    'Dirección del cliente',              -- Dirección (opcional)
    '+56912345678',                       -- Teléfono (opcional)
    'contacto@juanitoperez.cl',           -- Email (opcional)
    'A',
    NOW()
);

-- Obtener el ID de la empresa creada
SET @empresa_id = LAST_INSERT_ID();

-- =====================================================
-- PASO 2: Verificar/Crear Paquete Premium
-- =====================================================
-- Si el paquete Premium no existe, crearlo
-- (Ajustar el ID según tu caso - puede ser 4, 5, etc.)

-- Verificar qué paquetes existen
SELECT id, nombre, slug, activo 
FROM paquetes 
WHERE activo = 'A'
ORDER BY id;

-- Si necesitas crear un nuevo paquete Premium:
-- INSERT INTO paquetes (
--     nombre, 
--     slug, 
--     descripcion, 
--     precio_setup, 
--     precio_mensual, 
--     activo, 
--     orden
-- ) VALUES (
--     'NextLine Nutrición Premium',
--     'nutricion-premium',
--     'Plan Premium con todos los módulos de nutricionistas',
--     0.00,
--     0.00,  -- Ajustar precio según corresponda
--     'A',
--     5
-- );

-- =====================================================
-- PASO 3: Asignar Módulos al Paquete Premium
-- =====================================================
-- Asegurar que el paquete Premium tenga todos los módulos
-- (Ajustar el paquete_id según corresponda)

INSERT INTO paquete_modulo (paquete_id, modulo_id, incluido, fcreacion)
VALUES
    (4, 33, 'S', NOW()),  -- Agenda
    (4, 34, 'S', NOW()),  -- Pacientes
    (4, 35, 'S', NOW()),  -- Documentos
    (4, 36, 'S', NOW()),  -- Historial Clínico
    (4, 37, 'S', NOW()),  -- Pagos
    (4, 38, 'S', NOW()),  -- Configuraciones
    (4, 39, 'S', NOW())   -- Cancelar Horas
ON DUPLICATE KEY UPDATE
    incluido = 'S',
    fcreacion = NOW();

-- =====================================================
-- PASO 4: Crear el Usuario
-- =====================================================
-- IMPORTANTE: La contraseña debe estar hasheada
-- Usar password_hash() en PHP o generar hash manualmente

-- Generar hash de contraseña (ejemplo: "password123")
-- En PHP: password_hash('password123', PASSWORD_DEFAULT)
-- O usar: SELECT SHA2('password123', 256) (menos seguro, solo para pruebas)

INSERT INTO usuario (
    nombre,
    apellido,
    correo,
    telefono,
    clave,
    perfil_id,
    empresa_id,
    estado,
    fcreacion
) VALUES (
    'Juanito',                              -- Nombre
    'Pérez',                                -- Apellido
    'juanito@ejemplo.com',                  -- Email (ajustar)
    '+56912345678',                         -- Teléfono (ajustar)
    '$2y$10$EjemploDeHashAqui1234567890',  -- Hash de contraseña (GENERAR NUEVO)
    9,                                      -- Perfil Nutricionista (id=9)
    @empresa_id,                            -- ID de la empresa creada
    'A',
    NOW()
);

-- Obtener el ID del usuario creado
SET @usuario_id = LAST_INSERT_ID();

-- =====================================================
-- PASO 5: Verificar Permisos del Perfil
-- =====================================================
-- Asegurar que el perfil "Nutricionista" (id=9) tenga permisos
-- en todos los módulos del paquete Premium

INSERT INTO perfil_modulo (
    perfil_id, 
    modulo_id, 
    ver, 
    editar, 
    eliminar, 
    estado,
    orden,
    fcreacion
) VALUES
    (9, 33, 1, 1, 1, 'A', 1, NOW()),  -- Agenda
    (9, 34, 1, 1, 1, 'A', 2, NOW()),  -- Pacientes
    (9, 35, 1, 1, 1, 'A', 3, NOW()),  -- Documentos
    (9, 36, 1, 1, 1, 'A', 4, NOW()),  -- Historial
    (9, 37, 1, 1, 1, 'A', 5, NOW()),  -- Pagos
    (9, 38, 1, 1, 1, 'A', 6, NOW()),  -- Configuraciones
    (9, 39, 1, 1, 1, 'A', 7, NOW())   -- Cancelar Horas
ON DUPLICATE KEY UPDATE
    ver = 1,
    editar = 1,
    eliminar = 1,
    estado = 'A',
    factualizacion = NOW();

-- =====================================================
-- PASO 6: Verificación Final
-- =====================================================

-- Verificar empresa y paquete
SELECT 
    e.id AS empresa_id,
    e.nombre AS empresa_nombre,
    e.paquete_id,
    p.nombre AS paquete_nombre,
    p.slug AS paquete_slug
FROM empresa e
LEFT JOIN paquetes p ON p.id = e.paquete_id
WHERE e.id = @empresa_id;

-- Verificar módulos del paquete
SELECT 
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    m.ruta AS modulo_ruta,
    pm.incluido
FROM paquete_modulo pm
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.paquete_id = (SELECT paquete_id FROM empresa WHERE id = @empresa_id)
  AND pm.incluido = 'S'
ORDER BY pm.modulo_id;

-- Verificar usuario y sus permisos
SELECT 
    u.id AS usuario_id,
    u.nombre,
    u.apellido,
    u.correo,
    u.empresa_id,
    e.nombre AS empresa_nombre,
    e.paquete_id,
    p.nombre AS paquete_nombre,
    u.perfil_id,
    pf.nombre AS perfil_nombre,
    COUNT(pm.modulo_id) AS modulos_con_permiso
FROM usuario u
JOIN empresa e ON e.id = u.empresa_id
LEFT JOIN paquetes p ON p.id = e.paquete_id
LEFT JOIN perfil pf ON pf.id = u.perfil_id
LEFT JOIN perfil_modulo pm ON pm.perfil_id = u.perfil_id AND pm.estado = 'A'
WHERE u.id = @usuario_id
GROUP BY u.id, u.nombre, u.apellido, u.correo, u.empresa_id, 
         e.nombre, e.paquete_id, p.nombre, u.perfil_id, pf.nombre;

-- Verificar que el usuario verá los módulos correctos
-- (Simulación de lo que verá en el menú)
SELECT 
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    m.ruta AS modulo_ruta,
    pm.ver,
    pm.editar,
    pm.eliminar,
    pkm.incluido AS en_paquete
FROM perfil_modulo pm
JOIN modulo m ON m.id = pm.modulo_id
JOIN usuario u ON u.perfil_id = pm.perfil_id
JOIN empresa e ON e.id = u.empresa_id
LEFT JOIN paquete_modulo pkm ON pkm.modulo_id = pm.modulo_id 
    AND pkm.paquete_id = e.paquete_id 
    AND pkm.incluido = 'S'
WHERE u.id = @usuario_id
  AND pm.estado = 'A'
  AND m.estado = 'A'
  AND m.mostrar = 'S'
  AND pkm.incluido = 'S'  -- Solo módulos que están en el paquete
ORDER BY pm.orden ASC;

-- =====================================================
-- RESUMEN
-- =====================================================
-- 1. ✅ Empresa creada (ID: @empresa_id)
-- 2. ✅ Paquete asignado a la empresa
-- 3. ✅ Módulos asignados al paquete
-- 4. ✅ Usuario creado (ID: @usuario_id)
-- 5. ✅ Permisos del perfil verificados
-- 
-- Próximos pasos:
-- - El usuario puede iniciar sesión con su email y contraseña
-- - Verá solo los módulos que están en su paquete Y en su perfil
-- - Podrá usar todos los módulos del plan Premium
-- =====================================================
