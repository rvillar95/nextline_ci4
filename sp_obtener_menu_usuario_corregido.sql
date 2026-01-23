-- Procedimiento almacenado corregido sin DEFINER
-- Esto evita el error de permisos SET USER

DROP PROCEDURE IF EXISTS `sp_obtener_menu_usuario`;

DELIMITER $$

CREATE PROCEDURE `sp_obtener_menu_usuario` (IN `p_usuario_id` INT) 
BEGIN 
    DECLARE v_poder INT; 
    DECLARE v_empresa_id INT; 
    DECLARE v_paquete_id INT; 
    
    -- Obtener datos del usuario 
    SELECT p.poder, u.empresa_id 
    INTO v_poder, v_empresa_id 
    FROM usuario u 
    INNER JOIN perfil p ON p.id = u.perfil_id 
    WHERE u.id = p_usuario_id 
    LIMIT 1; 
    
    -- Si es Super Admin (poder=3), mostrar TODOS los módulos 
    IF v_poder = 3 THEN 
        SELECT 
            m.id, 
            m.nombre, 
            m.descripcion, 
            m.ruta, 
            m.estado, 
            m.mostrar, 
            m.sa, 
            m.orden, 
            'S' as incluido_en_paquete, 
            NULL as paquete_nombre 
        FROM modulo m 
        WHERE m.estado = 'A' 
        AND m.mostrar = 'S' 
        ORDER BY m.orden, m.id; 
    ELSE 
        -- Usuario Admin de Empresa: mostrar solo módulos de su paquete 
        SELECT paquete_id 
        INTO v_paquete_id 
        FROM empresa 
        WHERE id = v_empresa_id 
        LIMIT 1; 
        
        SELECT 
            m.id, 
            m.nombre, 
            m.descripcion, 
            m.ruta, 
            m.estado, 
            m.mostrar, 
            m.sa, 
            m.orden, 
            pm.incluido as incluido_en_paquete, 
            p.nombre as paquete_nombre 
        FROM modulo m 
        INNER JOIN paquete_modulo pm ON pm.modulo_id = m.id 
        INNER JOIN paquetes p ON p.id = pm.paquete_id 
        WHERE m.estado = 'A' 
        AND m.mostrar = 'S' 
        AND m.sa = 'N' -- Excluir módulos de Super Admin 
        AND pm.paquete_id = v_paquete_id 
        AND pm.incluido = 'S' 
        ORDER BY m.orden, m.id; 
    END IF; 
END$$

DELIMITER ;
