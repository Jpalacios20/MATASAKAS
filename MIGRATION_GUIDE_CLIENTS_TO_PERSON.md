# GUÍA COMPLETA: Migración Segura de Tabla "clients" a "person"
**Fecha:** 2026-05-05

---

## 📋 RESUMEN DE CAMBIOS REALIZADOS

### 1. ✅ Archivos de Base de Datos
- **Backup creado:** `clients_backup_2026_05_05` (tabla completa)
- **Script de migración:** `MIGRATION_CLIENTS_TO_PERSON_2026-05-05.sql`
- **Archivo principal actualizado:** `db_gliese_hotelero.sql`
  - Tabla renombrada: `clients` → `person`
  - Índices actualizados
  - Constraints actualizados
  - AUTO_INCREMENT actualizado

---

### 2. ✅ Archivos PHP Actualizados

#### **Modelos (8 archivos):**
1. ✅ `M_Billingpersale.php` - 4 queries actualizadas
2. ✅ `M_Billingpersale_Details.php` - 2 queries actualizadas
3. ✅ `M_Clients.php` - 8 queries actualizadas
4. ✅ `M_Report_Billing.php` - 1 query actualizada
5. ✅ `M_Income_Products.php` - 3 queries actualizadas
6. ✅ `M_income_Accessory.php` - 3 queries actualizadas
7. ✅ `M_Customer_Report.php` - 1 query actualizada
8. ✅ `M_Income_Products_Pending.php` - 1 query actualizada

#### **Controladores (3 archivos):**
- `C_Clients.php` - No requiere cambios (usa métodos de modelos)
- `C_Report_Billing.php` - No requiere cambios (usa métodos de modelos)
- `C_Report_Monthly.php` - No requiere cambios (usa métodos de modelos)

---

## 🚀 INSTRUCCIONES DE EJECUCIÓN

### **Paso 1: Hacer Backup de la Base de Datos Actual**
```bash
# Desde MySQL/phpMyAdmin:
# Exportar la base de datos completa ANTES de ejecutar la migración
```

### **Paso 2: Ejecutar Script de Migración SQL**
```sql
-- Abrir el archivo: MIGRATION_CLIENTS_TO_PERSON_2026-05-05.sql
-- En phpMyAdmin o MySQL Workbench:
-- 1. Copiar todo el contenido del script
-- 2. Pegar en la pestaña SQL
-- 3. Ejecutar (Ctrl+Enter)
```

### **Paso 3: Validar que la Migración fue Exitosa**
```sql
-- Verificar que la tabla fue renombrada:
SELECT TABLE_NAME FROM information_schema.TABLES 
WHERE TABLE_SCHEMA='db_gliese' AND TABLE_NAME='person';

-- Resultado esperado: debería mostrar "person"

-- Contar registros en la nueva tabla:
SELECT COUNT(*) as total_registros FROM person;

-- Verificar que los índices existen:
SHOW INDEXES FROM person;

-- Verificar integridad referencial:
SELECT * FROM billingpersale LIMIT 1;
```

---

## ⚠️ LISTA DE VERIFICACIÓN ANTES DE CAMBIOS EN PRODUCCIÓN

### **Antes de ejecutar:**
- [ ] Backup completo de la base de datos realizado
- [ ] Código PHP actualizado en el servidor (los cambios ya están en los archivos)
- [ ] Revisar que no hay conexiones activas a la base de datos

### **Después de la migración:**
- [ ] Probar login de usuarios
- [ ] Verificar que se cargan lista de clientes/personas
- [ ] Realizar una venta/facturación (para verificar JOIN con billingpersale)
- [ ] Revisar reportes de clientes
- [ ] Verificar ingresos de productos

---

## 📝 CAMBIOS ESPECÍFICOS EN QUERIES SQL

### **Patrones Actualizados:**
```sql
-- ANTES:
FROM clients c
INNER JOIN clients c ON ...

-- DESPUÉS:
FROM person c
INNER JOIN person c ON ...
```

### **Tablas con Claves Foráneas Actualizadas:**
1. `billingpersale` - columna `clients_id` referencia a `person.id`
2. `proforma` - columna `id_clients` referencia a `person.id`
3. `income_products` - columna `id_person` referencia a `person.id`
4. `referralguide` - columna `id_clients` referencia a `person.id`

---

## 🔍 VALIDACIÓN DE INTEGRIDAD REFERENCIAL

```sql
-- Verificar que todas las claves foráneas funcionan correctamente:
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_NAME = 'person';
```

---

## 🆘 EN CASO DE PROBLEMAS

### **Si algo falla:**
1. **No ejecutar más cambios**
2. **Restaurar el backup:** `clients_backup_2026_05_05`
3. **Contactar al equipo de desarrollo**

### **Comando para restaurar (si algo sale mal):**
```sql
-- Copiar datos del backup a la tabla original:
INSERT INTO clients SELECT * FROM clients_backup_2026_05_05;

-- O renombrar la tabla de vuelta:
ALTER TABLE person RENAME TO clients;
```

---

## 📊 CAMBIOS RESUMIDOS

| Tipo | Cantidad | Estado |
|------|----------|--------|
| Modelos PHP | 8 | ✅ Actualizados |
| Controladores PHP | 3 | ✅ No requieren cambios |
| Queries SQL | 20+ | ✅ Actualizadas |
| Índices DB | 2 | ✅ Actualizados |
| Constraints FK | 4 | ✅ Actualizados |

---

## ✅ FIRMA DE APROBACIÓN

**Fecha de Implementación:** 2026-05-05  
**Responsable:** Sistema de Migración Automática  
**Estado:** Listo para Producción  

---

*Este documento debe guardarse para auditoría y referencia futura.*
