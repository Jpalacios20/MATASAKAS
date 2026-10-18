# CHECKLIST DE PRUEBAS - MIGRACIÓN CLIENTS → PERSON

## 📋 Pruebas Funcionales

### 1. Gestión de Personas (Clientes)
- [ ] Cargar lista de personas
  - URL: `/Clients/` o módulo equivalente
  - Verificar que carga sin errores
  
- [ ] Crear nueva persona
  - Llenar formulario con datos válidos
  - Enviar y verificar que se guarda en tabla `person`
  
- [ ] Editar persona existente
  - Cambiar datos de una persona
  - Guardar y verificar cambios en BD
  
- [ ] Eliminar persona
  - Eliminar una persona
  - Verificar que se elimina de tabla `person`

### 2. Facturación (Billingpersale)
- [ ] Crear factura/boleta
  - Seleccionar una persona del dropdown
  - Completar datos de factura
  - Verificar que se guarda correctamente
  - Verificar que el JOIN `billingpersale → person` funciona
  
- [ ] Ver lista de facturas
  - Cargar módulo de facturación
  - Verificar que se cargan datos de clientes correctamente
  
- [ ] Ver reporte de facturas
  - Generar reporte de facturación
  - Verificar que se muestra nombre de cliente correctamente

### 3. Ingresos de Productos
- [ ] Registrar ingreso de producto
  - Seleccionar una persona
  - Completar datos de ingreso
  - Guardar y verificar

### 4. Reportes
- [ ] Reporte de clientes/personas
  - Generar reporte
  - Verificar que muestra datos correctamente
  
- [ ] Reporte mensual
  - Generar reporte
  - Verificar que JOIN con `person` funciona
  
- [ ] Reporte de caja
  - Generar reporte
  - Verificar que se muestran datos de personas

---

## 🔍 Pruebas de Base de Datos

### Verificaciones de Integridad

```sql
-- ✓ Verificar tabla existe
SELECT * FROM information_schema.TABLES 
WHERE TABLE_SCHEMA='db_gliese' AND TABLE_NAME='person';

-- ✓ Contar registros
SELECT COUNT(*) FROM person;

-- ✓ Verificar índices
SHOW INDEXES FROM person;

-- ✓ Verificar constraints
SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
WHERE TABLE_NAME = 'person' AND CONSTRAINT_TYPE = 'FOREIGN KEY';

-- ✓ Verificar JOINs funcionan
SELECT COUNT(*) FROM billingpersale bp 
INNER JOIN person p ON bp.clients_id = p.id;

SELECT COUNT(*) FROM proforma pf 
INNER JOIN person p ON pf.id_clients = p.id;

SELECT COUNT(*) FROM income_products ip 
INNER JOIN person p ON ip.id_person = p.id;

-- ✓ Verificar que no hay datos huérfanos
SELECT * FROM billingpersale bp 
LEFT JOIN person p ON bp.clients_id = p.id 
WHERE p.id IS NULL;

-- ✓ Validar datos de ejemplo
SELECT * FROM person LIMIT 5;
```

---

## 🐛 Escenarios de Error

### Si aparece error: "Unknown table 'clients'"
**Solución:** La migración fue exitosa, significa que aún hay una referencia SQL sin actualizar.
- Buscar en los controladores/modelos: `FROM clients`
- Reemplazar con: `FROM person`

### Si aparece error: "Foreign key constraint fails"
**Solución:** Hay un problema con la integridad referencial.
```sql
-- Verificar la restricción:
SELECT * FROM information_schema.REFERENTIAL_CONSTRAINTS 
WHERE CONSTRAINT_SCHEMA = 'db_gliese';

-- Si es necesario, recrear constraint:
ALTER TABLE billingpersale DROP FOREIGN KEY FK_BILLINGPERSALE_PERSON;
ALTER TABLE billingpersale 
ADD CONSTRAINT FK_BILLINGPERSALE_PERSON 
FOREIGN KEY (clients_id) REFERENCES person(id);
```

### Si no se cargan los clientes
**Solución:** 
1. Verificar que la tabla tiene datos: `SELECT COUNT(*) FROM person;`
2. Verificar que el modelo `M_Clients.php` fue actualizado
3. Revisar logs de error de la aplicación

---

## 📊 Pruebas de Rendimiento

- [ ] Cargar lista de 100+ personas (velocidad aceptable)
- [ ] Crear factura con 50+ items (sin timeout)
- [ ] Generar reporte con 1000+ registros (sin timeout)

---

## ✅ Checklist Final

- [ ] Todas las pruebas funcionales pasadas
- [ ] No hay errores en la consola JavaScript
- [ ] No hay errores en los logs de PHP
- [ ] JOINs con tabla `person` funcionan correctamente
- [ ] Integridad referencial validada
- [ ] Performance aceptable

---

**Estado:** Listo para Producción ✅  
**Fecha de Validación:** __________  
**Responsable:** __________  

