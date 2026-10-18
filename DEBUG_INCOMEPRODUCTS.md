# 🔍 GUÍA DE DEBUGGING: Error "Campos Incompletos"

## ❌ PROBLEMA
Al presionar "Guardar", aparece la notificación "Campos incompletos" aunque ya rellenaste todos los campos.

---

## 🔧 SOLUCIÓN: PASOS PARA DEBUGGING

### **PASO 1: Abre la Consola del Navegador**
1. Presiona **F12** en tu navegador
2. Haz clic en la pestaña **"Consola"**
3. Mantén abierta esta consola mientras trabajas

### **PASO 2: Llena los formularios**
- Cliente: Selecciona cualquiera
- Tipo de Comprobante: Selecciona Factura o Boleta
- Método de Pago: Selecciona Efectivo, Tarjeta, etc
- Serie: Escribe "FAC" o similar
- Número de Serie: Escribe "001"
- Fecha: Selecciona cualquier fecha

### **PASO 3: Agrega al menos UN producto**
- Haz clic en "Agregar productos"
- Selecciona un producto
- Verifica que aparece en la tabla

### **PASO 4: Presiona GUARDAR**
- Haz clic en botón "Guardar"
- **MIRA LA CONSOLA** (F12)

### **PASO 5: Busca en la Consola el Log**
Deberías ver algo como:

```
=== VALORES CAPTURADOS ===
id_client: "5" | Tipo: string
id_voucher_type: "1" | Tipo: string
id_payment_type: "2" | Tipo: string
id_payment_shape: "1" | Tipo: string
series: "FAC" | Tipo: string
number_serial: "001" | Tipo: string
expiration_date: "2026-03-30" | Tipo: string
Cantidad de productos en tabla: 1
```

---

## 🐛 INTERPRETACIÓN DE RESULTADOS

### ✅ Si los valores se ven así:
```
id_client: "5"
id_voucher_type: "1"
```
→ El problema NO es JavaScript, es que algo no se está guardando correctamente

### ❌ Si ves esto:
```
id_client: "" | (vacío)
id_voucher_type: null
id_payment_type: undefined
```
→ El problema es que los selectores NO están encontrando los elementos

---

## 🔍 CASOS POSIBLES Y SOLUCIONES

### **CASO 1: id_client está vacío**
**Problema:** El select no tiene valor
**Solución:**
```javascript
// Verifica selectores alternativos:
console.log("Todos los selects:", $("select").length);
console.log("Selects con name='name':", $("select[name='name']").length);
console.log("Valor select name='name':", $("select[name='name']").val());
console.log("Select2 value:", $("select[name='name']").select2("val"));
```

### **CASO 2: Series o Número de serie está vacío**
**Problema:** Los inputs no tienen valor
**Solución:**
```javascript
console.log("Input series:", $("input[name='series']").val());
console.log("Input number_serial:", $("input[name='number_serial']").val());
```

### **CASO 3: Fecha está vacío**
**Problema:** El date picker no tiene valor
**Solución:**
```javascript
console.log("Input expiration_date:", $("input[name='expiration_date']").val());
console.log("Input expiration_date visibility:", $("input[name='expiration_date']").is(":visible"));
```

---

## 📋 PASOS ADICIONALES DE DEBUGGING

### **1. Inspecciona el HTML en Consola**
Copia esto en la consola:
```javascript
console.log("=== HTML INSPECTION ===");
console.log("Form:", $("#create_income_products_details_form").html());
console.log("Select name='name':", $("select[name='name']").outerHtml());
```

### **2. Verifica que Select2 está inicializado**
```javascript
console.log("¿Select2 iniciado?", $("select[name='name']").data("select2") !== undefined);
```

### **3. Haz click DIRECTAMENTE en el botón Guardar y observa:**
```javascript
// Ejecuta esto en la consola para ver el estado de los inputs
$("select[name='name']").each(function() {
    console.log("Select value:", $(this).val(), "HTML:", $(this).html());
});
```

---

## 🚀 SOLUCIÓN RÁPIDA PARA PROBAR

Si encuentras que los valores así están:

```javascript
id_client: undefined
id_payment_type: undefined
```

**Copia esto en la consola y ejecuta:**

```javascript
// Buscar todos los nombres de atributos en los inputs
$("select").each(function() {
    console.log("Found select with name:", $(this).attr("name"), "value:", $(this).val());
});

$("input").each(function() {
    console.log("Found input with name:", $(this).attr("name"), "value:", $(this).val());
});
```

Esto te mostrará EXACTAMENTE qué se está recibiendo.

---

## 📞 SI NECESITAS AYUDA

1. Abre **Consola (F12)**
2. Presiona **Guardar**
3. Copia TODO lo que aparezca en la consola
4. Comparte conmigo ese texto

Así podré ver exactamente qué valores se están capturando y dónde está el problema.

---

## ✅ CHECKLIST RÁPIDO

- [ ] ¿Abriste F12 y miras la consola?
- [ ] ¿Llenaste el Cliente?
- [ ] ¿Llenaste Tipo de Comprobante?
- [ ] ¿Llenaste Método de Pago?
- [ ] ¿Llenaste la Forma de Pago (pm_description)?
- [ ] ¿Llenaste Serie (FAC, BOL)?
- [ ] ¿Llenaste Número de Serie (001, 002)?
- [ ] ¿Seleccionaste una Fecha?
- [ ] ¿Agregaste al menos UN producto?
- [ ] ¿Presionaste GUARDAR y miraste la consola?

Si todo pasó el checklist, háblame qué ves en la consola.
