-- Ocultar el módulo "Historial Clínico" del menú lateral.
-- La funcionalidad (buscar por tags, comparar historiales) queda accesible desde
-- Agenda → Consulta en curso (botones "Buscar historiales por tag" y "Comparar historiales").
-- Los datos del paciente se ven en Pacientes → Detalle del paciente.

UPDATE `modulo`
SET `mostrar` = 'N'
WHERE `nombre` = 'Historial Clínico'
  AND `mostrar` = 'S';

-- Para revertir y volver a mostrar el módulo en el menú:
-- UPDATE modulo SET mostrar = 'S' WHERE nombre = 'Historial Clínico';
