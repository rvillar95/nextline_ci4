-- =============================================================================
-- NutriNext — Empresa piloto Valentina Martínez (plan Nutri Partner, acceso total)
-- Ejecutar DESPUÉS de 2026-05-21_catalogo_planes_nutrinext.sql
-- =============================================================================

SET @paquete_partner_id = (SELECT `id` FROM `paquetes` WHERE `slug` = 'nutri-partner' AND `activo` = 'A' LIMIT 1);

SET @empresa_id = (
    SELECT e.`id`
    FROM `empresa` e
    LEFT JOIN `usuario` u ON u.`empresa_id` = e.`id` AND u.`estado` = 'A'
    WHERE e.`nombre` LIKE '%Valentina%'
       OR e.`nombre` LIKE '%Martinez%'
       OR e.`nombre` LIKE '%Martínez%'
       OR CONCAT(IFNULL(u.`nombre`, ''), ' ', IFNULL(u.`apellido`, '')) LIKE '%Valentina%'
       OR u.`email` LIKE '%valentina%'
    ORDER BY e.`id` ASC
    LIMIT 1
);

INSERT INTO `empresa` (`nombre`, `email`, `telefono`, `paquete_id`, `estado`, `fcreacion`, `factualizacion`)
SELECT 'Valentina Martínez - Nutrición', 'valentina@nutrinext.cl', NULL, @paquete_partner_id, 'A', NOW(), NOW()
FROM DUAL
WHERE @empresa_id IS NULL AND @paquete_partner_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `empresa` WHERE `nombre` = 'Valentina Martínez - Nutrición');

SET @empresa_id = IFNULL(@empresa_id, (
    SELECT `id` FROM `empresa` WHERE `nombre` = 'Valentina Martínez - Nutrición' ORDER BY `id` DESC LIMIT 1
));

UPDATE `empresa`
SET `paquete_id` = @paquete_partner_id, `factualizacion` = NOW()
WHERE `id` = @empresa_id AND @paquete_partner_id IS NOT NULL;

-- Suscripción piloto (12 meses, sin cobro) — solo si existe tabla
INSERT INTO `suscripciones` (
    `empresa_id`, `paquete_id`, `monto_mensual`, `fecha_inicio`, `fecha_fin`,
    `estado`, `renovacion_automatica`, `observaciones`, `fcreacion`
)
SELECT @empresa_id, @paquete_partner_id, 1.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 12 MONTH),
    'activa', 'N', 'Piloto partner Valentina Martínez', NOW()
FROM DUAL
WHERE @empresa_id IS NOT NULL AND @paquete_partner_id IS NOT NULL
  AND EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'suscripciones')
  AND NOT EXISTS (SELECT 1 FROM `suscripciones` s WHERE s.`empresa_id` = @empresa_id AND s.`estado` = 'activa');

-- Add-ons métodos a $0 (redundante con partner pero útil si se valida por empresa_addon)
INSERT INTO `empresa_addon` (`empresa_id`, `tipo`, `referencia_id`, `precio_mensual`, `fecha_inicio`, `fecha_fin`, `estado`, `fcreacion`)
SELECT @empresa_id, 'metodo_calculo', mc.`id`, 0.00, CURDATE(), NULL, 'activo', NOW()
FROM `metodos_calculo` mc
WHERE @empresa_id IS NOT NULL AND mc.`activo` = 'A'
  AND NOT EXISTS (
    SELECT 1 FROM `empresa_addon` ea
    WHERE ea.`empresa_id` = @empresa_id AND ea.`tipo` = 'metodo_calculo'
      AND ea.`referencia_id` = mc.`id` AND ea.`estado` = 'activo'
  );

-- Perfiles de la empresa: todos los módulos del paquete partner
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`, `factualizacion`)
SELECT pf.`id`, pm.`modulo_id`, 'S', 'S', 'S', 'S', 'A', pm.`modulo_id`, NOW(), NOW()
FROM `perfil` pf
JOIN `paquete_modulo` pm ON pm.`paquete_id` = @paquete_partner_id AND pm.`incluido` = 'S'
JOIN `modulo` m ON m.`id` = pm.`modulo_id` AND m.`estado` = 'A' AND m.`sa` = 'N'
WHERE pf.`empresa_id` = @empresa_id AND pf.`estado` = 'A' AND @paquete_partner_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM `perfil_modulo` x WHERE x.`perfil_id` = pf.`id` AND x.`modulo_id` = pm.`modulo_id`
  );

-- Perfil público visible en /equipo
UPDATE `usuario` u
SET u.`perfil_publico` = 'S',
    u.`perfil_publico_orden` = 1,
    u.`factualizacion` = NOW()
WHERE u.`empresa_id` = @empresa_id
  AND (u.`nombre` LIKE '%Valentina%' OR u.`email` LIKE '%valentina%')
  AND EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'usuario' AND column_name = 'perfil_publico'
  );

SELECT CONCAT('Piloto Valentina: empresa_id=', IFNULL(@empresa_id, 'N/A'),
  ' paquete_partner_id=', IFNULL(@paquete_partner_id, 'N/A')) AS resultado;
