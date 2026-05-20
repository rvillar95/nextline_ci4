-- =============================================================================
-- NutriNext — Catálogo comercial 2026 (Chile / CLP)
-- Ejecutar en hosting: mysql -u USER -p BASE < app/Database/Sql/2026-05-21_catalogo_planes_nutrinext.sql
-- Idempotente. No modifica paquetes Presencia/Gestión (ids 1–3).
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. Retirar línea nutrición antigua
-- -----------------------------------------------------------------------------
UPDATE `paquetes` SET `activo` = 'I', `factualizacion` = NOW()
WHERE `slug` IN (
    'nutricion', 'nutricion-premium', 'nutricion-avanzado',
    'nutri-start', 'nutri-pro', 'nutri-clinical', 'nutri-elite'
);

-- -----------------------------------------------------------------------------
-- 2. Catálogo nuevo (INSERT si no existe por slug)
-- -----------------------------------------------------------------------------
INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`, `fcreacion`, `factualizacion`)
SELECT 'Nutri Esencial', 'nutri-esencial',
  'Consultorio esencial: agenda, pacientes, historial y composición 2 componentes. Hasta 25 pacientes/mes.',
  39990.00, 12990.00, 'A', 20, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `paquetes` WHERE `slug` = 'nutri-esencial');

INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`, `fcreacion`, `factualizacion`)
SELECT 'Nutri Profesional', 'nutri-profesional',
  'Plan recomendado: plan alimentario, Mercado Pago, reserva web, perfil público y equipo. Hasta 70 pacientes/mes.',
  59990.00, 22990.00, 'A', 21, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `paquetes` WHERE `slug` = 'nutri-profesional');

INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`, `fcreacion`, `factualizacion`)
SELECT 'Nutri Avanzado', 'nutri-avanzado',
  'Antropometría completa (4 y 5 componentes + somatotipo) y recordatorios WhatsApp. Hasta 120 pacientes/mes.',
  89990.00, 36990.00, 'A', 22, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `paquetes` WHERE `slug` = 'nutri-avanzado');

INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`, `fcreacion`, `factualizacion`)
SELECT 'Nutri Clínica', 'nutri-clinica',
  'Multi-profesional y multi-sede. Precio desde cotización; contactar ventas.',
  0.00, 69990.00, 'A', 23, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `paquetes` WHERE `slug` = 'nutri-clinica');

INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`, `fcreacion`, `factualizacion`)
SELECT 'Nutri Partner', 'nutri-partner',
  'Plan interno piloto: acceso total para demos y partners. No visible en web pública.',
  0.00, 0.00, 'A', 99, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `paquetes` WHERE `slug` = 'nutri-partner');

-- -----------------------------------------------------------------------------
-- 3. Precios add-on — métodos de cálculo
-- -----------------------------------------------------------------------------
UPDATE `metodos_calculo` SET `precio_mensual` = 0.00, `es_addon` = 'N' WHERE `slug` = '2-componentes';
UPDATE `metodos_calculo` SET `precio_mensual` = 7990.00, `es_addon` = 'S' WHERE `slug` = '4-componentes';
UPDATE `metodos_calculo` SET `precio_mensual` = 12990.00, `es_addon` = 'S' WHERE `slug` = '5-componentes';
UPDATE `metodos_calculo` SET `precio_mensual` = 6990.00, `es_addon` = 'S' WHERE `slug` = 'somatotipo';

-- -----------------------------------------------------------------------------
-- 4. Rutas /calcular-* (módulo Historial)
-- -----------------------------------------------------------------------------
SET @modulo_historial_id = (
    SELECT `id` FROM `modulo` WHERE `ruta` = '/dashboard/historial' AND `estado` = 'A' LIMIT 1
);

INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
SELECT @modulo_historial_id, 'Calcular 2 componentes', '/calcular-2-componentes', 'editar', 'A', 'N', 50, NOW(), NOW(), '0000-00-00 00:00:00'
FROM DUAL WHERE @modulo_historial_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `modulo_detalle` WHERE `ruta` = '/calcular-2-componentes' AND `estado` = 'A');

INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
SELECT @modulo_historial_id, 'Calcular 4 componentes', '/calcular-4-componentes', 'editar', 'A', 'N', 51, NOW(), NOW(), '0000-00-00 00:00:00'
FROM DUAL WHERE @modulo_historial_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `modulo_detalle` WHERE `ruta` = '/calcular-4-componentes' AND `estado` = 'A');

INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
SELECT @modulo_historial_id, 'Calcular 5 componentes', '/calcular-5-componentes', 'editar', 'A', 'N', 52, NOW(), NOW(), '0000-00-00 00:00:00'
FROM DUAL WHERE @modulo_historial_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `modulo_detalle` WHERE `ruta` = '/calcular-5-componentes' AND `estado` = 'A');

INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
SELECT @modulo_historial_id, 'Calcular somatotipo', '/calcular-somatotipo', 'editar', 'A', 'N', 53, NOW(), NOW(), '0000-00-00 00:00:00'
FROM DUAL WHERE @modulo_historial_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `modulo_detalle` WHERE `ruta` = '/calcular-somatotipo' AND `estado` = 'A');

-- -----------------------------------------------------------------------------
-- 5. Módulos add-on (catálogo precios)
-- -----------------------------------------------------------------------------
UPDATE `modulo` SET `precio_mensual` = 0.00, `es_addon` = 'N'
WHERE `ruta` IN ('/dashboard/boton-pago', '/dashboard/plan-alimentario') AND `estado` = 'A';

INSERT INTO `modulo` (`nombre`, `descripcion`, `precio_mensual`, `es_addon`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`)
SELECT 'Recordatorios WhatsApp', 'Recordatorios automáticos de cita por WhatsApp',
  4990.00, 'S', '/addon/whatsapp-recordatorios', 'A', 'N', 'S', NOW(), NOW(), '0000-00-00 00:00:00'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `modulo` WHERE `ruta` = '/addon/whatsapp-recordatorios');

INSERT INTO `modulo` (`nombre`, `descripcion`, `precio_mensual`, `es_addon`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`)
SELECT 'Reserva web pública', 'Página /reservar para pacientes',
  4990.00, 'S', '/addon/reserva-web', 'A', 'N', 'S', NOW(), NOW(), '0000-00-00 00:00:00'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `modulo` WHERE `ruta` = '/addon/reserva-web');

-- -----------------------------------------------------------------------------
-- 6. paquete_modulo — rebuild solo catálogo nutri-* activo
-- -----------------------------------------------------------------------------
DELETE pm FROM `paquete_modulo` pm
INNER JOIN `paquetes` p ON p.id = pm.paquete_id
WHERE p.slug IN ('nutri-esencial', 'nutri-profesional', 'nutri-avanzado', 'nutri-clinica', 'nutri-partner');

-- Módulos base nutrición: 2,33,34,35,36,38,41,46 + extras por plan
-- Esencial
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
SELECT p.id, m.id, 'S', NOW()
FROM `paquetes` p
JOIN `modulo` m ON m.id IN (2, 33, 34, 35, 36, 38, 41, 46) AND m.estado = 'A'
WHERE p.slug = 'nutri-esencial';

-- Profesional (+ plan alimentario, botones pago, cancelar horas)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
SELECT p.id, m.id, 'S', NOW()
FROM `paquetes` p
JOIN `modulo` m ON m.id IN (2, 33, 34, 35, 36, 38, 41, 45, 44, 46, 39) AND m.estado = 'A'
WHERE p.slug = 'nutri-profesional';

-- Avanzado (igual módulos que profesional)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
SELECT p.id, m.id, 'S', NOW()
FROM `paquetes` p
JOIN `modulo` m ON m.id IN (2, 33, 34, 35, 36, 38, 41, 45, 44, 46, 39) AND m.estado = 'A'
WHERE p.slug = 'nutri-avanzado';

-- Clínica (como avanzado + pagos admin)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
SELECT p.id, m.id, 'S', NOW()
FROM `paquetes` p
JOIN `modulo` m ON m.id IN (2, 33, 34, 35, 36, 38, 41, 45, 44, 46, 39, 37) AND m.estado = 'A'
WHERE p.slug = 'nutri-clinica';

-- Partner (todo lo anterior)
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
SELECT p.id, m.id, 'S', NOW()
FROM `paquetes` p
JOIN `modulo` m ON m.id IN (2, 33, 34, 35, 36, 38, 41, 45, 44, 46, 39, 37) AND m.estado = 'A'
WHERE p.slug = 'nutri-partner';

-- Mi perfil en todos los planes nutri
SET @modulo_mi_perfil_id = (SELECT `id` FROM `modulo` WHERE `ruta` = '/dashboard/mi-perfil' AND `estado` = 'A' LIMIT 1);

INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
SELECT p.id, @modulo_mi_perfil_id, 'S', NOW()
FROM `paquetes` p
WHERE @modulo_mi_perfil_id IS NOT NULL
  AND p.slug IN ('nutri-esencial', 'nutri-profesional', 'nutri-avanzado', 'nutri-clinica', 'nutri-partner')
  AND NOT EXISTS (
    SELECT 1 FROM `paquete_modulo` pk WHERE pk.paquete_id = p.id AND pk.modulo_id = @modulo_mi_perfil_id
  );

-- -----------------------------------------------------------------------------
-- 7. paquete_modulo_detalle — métodos por plan
-- -----------------------------------------------------------------------------
DELETE pmd FROM `paquete_modulo_detalle` pmd
INNER JOIN `paquetes` p ON p.id = pmd.paquete_id
WHERE p.slug IN ('nutri-esencial', 'nutri-profesional', 'nutri-avanzado', 'nutri-clinica', 'nutri-partner');

-- Esencial + Profesional: solo 2 componentes
INSERT INTO `paquete_modulo_detalle` (`paquete_id`, `modulo_detalle_id`, `incluido`, `fcreacion`)
SELECT p.id, md.id, 'S', NOW()
FROM `paquetes` p
JOIN `modulo_detalle` md ON md.ruta = '/calcular-2-componentes' AND md.estado = 'A'
WHERE p.slug IN ('nutri-esencial', 'nutri-profesional');

-- Avanzado + Clínica + Partner: todos los métodos
INSERT INTO `paquete_modulo_detalle` (`paquete_id`, `modulo_detalle_id`, `incluido`, `fcreacion`)
SELECT p.id, md.id, 'S', NOW()
FROM `paquetes` p
JOIN `modulo_detalle` md ON md.ruta LIKE '/calcular-%' AND md.estado = 'A'
WHERE p.slug IN ('nutri-avanzado', 'nutri-clinica', 'nutri-partner');

SELECT 'Catálogo NutriNext 2026 aplicado' AS resultado;
