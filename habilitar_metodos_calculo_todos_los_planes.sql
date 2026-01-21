-- =====================================================
-- HABILITAR TODOS LOS MÉTODOS DE CÁLCULO EN LOS PLANES
-- =====================================================
-- Marca como incluidos todos los modulo_detalle con ruta '/calcular-%'
-- en la tabla paquete_modulo_detalle.
--
-- Útil para pruebas/demo: así todos los métodos quedan disponibles (verde).
--
-- NOTA:
-- - Esto NO toca precios ni flags de add-on en metodos_calculo.
-- - Solo afecta la disponibilidad por paquete.
-- =====================================================

-- (Opcional) Ajusta estos IDs según tus paquetes
SET @paquete_basico   = 4;
SET @paquete_premium  = 5;
SET @paquete_avanzado = 6;

-- Insertar/Actualizar para un paquete específico
-- Repite para cada paquete
INSERT INTO `paquete_modulo_detalle` (`paquete_id`, `modulo_detalle_id`, `incluido`, `fcreacion`)
SELECT @paquete_basico, md.id, 'S', NOW()
FROM modulo_detalle md
WHERE md.ruta LIKE '/calcular-%'
ON DUPLICATE KEY UPDATE `incluido` = 'S';

INSERT INTO `paquete_modulo_detalle` (`paquete_id`, `modulo_detalle_id`, `incluido`, `fcreacion`)
SELECT @paquete_premium, md.id, 'S', NOW()
FROM modulo_detalle md
WHERE md.ruta LIKE '/calcular-%'
ON DUPLICATE KEY UPDATE `incluido` = 'S';

INSERT INTO `paquete_modulo_detalle` (`paquete_id`, `modulo_detalle_id`, `incluido`, `fcreacion`)
SELECT @paquete_avanzado, md.id, 'S', NOW()
FROM modulo_detalle md
WHERE md.ruta LIKE '/calcular-%'
ON DUPLICATE KEY UPDATE `incluido` = 'S';

-- Verificación rápida
SELECT 
  p.nombre AS paquete,
  md.descripcion AS metodo,
  pmd.incluido
FROM paquete_modulo_detalle pmd
JOIN paquetes p ON p.id = pmd.paquete_id
JOIN modulo_detalle md ON md.id = pmd.modulo_detalle_id
WHERE pmd.paquete_id IN (@paquete_basico, @paquete_premium, @paquete_avanzado)
  AND md.ruta LIKE '/calcular-%'
ORDER BY pmd.paquete_id, md.orden;

