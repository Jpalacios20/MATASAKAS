-- OPCIONAL: si prefieres guardar las rutas nuevas directamente en la BD,
-- ejecuta este script. El parche de PHP del menú ya las resuelve sin tocar la BD.
UPDATE sub_menu SET url = 'Restobar/Bebidas' WHERE id = 52;
UPDATE sub_menu SET url = 'Restobar/Pedidos' WHERE id = 53;
UPDATE sub_menu SET url = 'Restobar/Ventas' WHERE id = 54;
