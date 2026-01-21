-- Verificar configuración del módulo Perfil
SELECT 
    id,
    nombre,
    sa,
    estado,
    mostrar,
    ruta
FROM modulo
WHERE nombre LIKE '%Perfil%'
ORDER BY id;

-- Ver módulos con sa = 'S' (Solo Super Admin)
SELECT 
    id,
    nombre,
    sa,
    estado
FROM modulo
WHERE sa = 'S'
ORDER BY nombre;
