# 📊 Campos de `historial_clinico` usados por cada método de cálculo

Este documento lista todos los campos que cada método de composición corporal está utilizando actualmente en el código, para confirmar con la nutricionista si son correctos o si faltan/sobran campos.

---

## 🔵 **MÉTODO 2 COMPONENTES** (Masa Magra / Masa Adiposa)
**Fórmula:** Durnin-Womersley simplificada

### Campos utilizados:
1. **`peso_actual`** (kg) - ✅ REQUERIDO
2. **`altura_actual`** (cm) - ✅ REQUERIDO
3. **`pliegue_tricipital`** (mm) - ✅ REQUERIDO
4. **`pliegue_subescapular`** (mm) - ✅ REQUERIDO
5. **`pliegue_suprailíaco`** (mm) - ✅ REQUERIDO
6. **`pliegue_bicipital`** (mm) - ⚠️ Se usa en suma pero no es estrictamente requerido

### Campos del paciente (tabla `pacientes`):
- **`fecha_nacimiento`** - Para calcular edad
- **`genero`** (M/F) - Para ajustar fórmula según género

### ⚠️ **PREGUNTA PARA NUTRICIONISTA:**
- ¿El método 2 componentes usa estos 4 pliegues o necesita otros adicionales?
- ¿Se necesita `pliegue_bicipital` o con 3 pliegues (tricipital, subescapular, suprailíaco) es suficiente?

---

## 🟢 **MÉTODO 4 COMPONENTES** (Grasa / Músculo / Hueso / Residual)
**Fórmula:** Fisionutdep / De Rose

### Campos utilizados:
1. **`peso_actual`** (kg) - ✅ REQUERIDO
2. **`altura_actual`** (cm) - ✅ REQUERIDO
3. **`altura_sentado`** (cm) - ✅ REQUERIDO
4. **`diametro_humero`** (cm) - ✅ REQUERIDO (para masa ósea)
5. **`diametro_femur`** (cm) - ✅ REQUERIDO (para masa ósea)
6. **`circunferencia_brazo_contraido`** (cm) - ⚠️ Se usa en `calcularMasaOsea()` pero no está validado como requerido
7. **`circunferencia_pantorrilla`** (cm) - ⚠️ Se usa en `calcularMasaOsea()` pero no está validado como requerido

### Campos usados indirectamente (vía método 2 componentes):
- Todos los pliegues del método 2 componentes (para calcular grasa)

### ⚠️ **PREGUNTAS PARA NUTRICIONISTA:**
- ¿El método 4 componentes necesita `circunferencia_brazo_contraido` y `circunferencia_pantorrilla` para calcular masa ósea?
- ¿Se necesita `circunferencia_muneca`? (Según respuestas anteriores, SÍ es necesaria para 4 componentes)
- ¿La fórmula de De Rose que estamos usando es correcta o necesita otros campos?

---

## 🟡 **MÉTODO 5 COMPONENTES** (Grasa / Músculo / Hueso / Residual / Piel)
**Fórmula:** Francis Holway

### Campos utilizados:
1. **`peso_actual`** (kg) - ✅ REQUERIDO
2. **`altura_actual`** (cm) - ✅ REQUERIDO
3. **`altura_sentado`** (cm) - ✅ REQUERIDO (vía método 4 componentes)
4. **`diametro_humero`** (cm) - ✅ REQUERIDO (vía método 4 componentes)
5. **`diametro_femur`** (cm) - ✅ REQUERIDO (vía método 4 componentes)

### Campos usados indirectamente:
- Todos los campos del método 4 componentes (porque 5 componentes se basa en 4 componentes)

### ⚠️ **NOTA:**
El método 5 componentes actualmente calcula la masa de piel usando una aproximación basada en superficie corporal (fórmula de Du Bois). No usa campos específicos de `historial_clinico` para esto.

### ⚠️ **PREGUNTAS PARA NUTRICIONISTA:**
- ¿El método 5 componentes necesita campos adicionales específicos para calcular la masa de piel?
- ¿La aproximación actual (superficie corporal) es suficiente o hay una fórmula más precisa que requiera otros datos?

---

## 🔴 **MÉTODO SOMATOTIPO** (Heath-Carter)
**Fórmula:** Heath-Carter (Endomorfia, Mesomorfia, Ectomorfia)

### Campos utilizados:
1. **`peso_actual`** (kg) - ✅ REQUERIDO
2. **`altura_actual`** (cm) - ✅ REQUERIDO
3. **`altura_sentado`** (cm) - ✅ REQUERIDO
4. **`pliegue_tricipital`** (mm) - ✅ REQUERIDO (para endomorfia)
5. **`pliegue_subescapular`** (mm) - ✅ REQUERIDO (para endomorfia)
6. **`pliegue_suprailíaco`** (mm) - ✅ REQUERIDO (para endomorfia)
7. **`diametro_humero`** (cm) - ✅ REQUERIDO (para mesomorfia)
8. **`diametro_femur`** (cm) - ✅ REQUERIDO (para mesomorfia)
9. **`circunferencia_brazo_contraido`** (cm) - ✅ REQUERIDO (para mesomorfia)
10. **`circunferencia_pantorrilla`** (cm) - ✅ REQUERIDO (para mesomorfia)

### ⚠️ **PREGUNTAS PARA NUTRICIONISTA:**
- ¿El método Heath-Carter que estamos usando es el estándar o hay variaciones?
- ¿Necesita otros pliegues o mediciones adicionales?
- ¿La fórmula de endomorfia, mesomorfia y ectomorfia que tenemos es correcta?

---

## 📝 **RESUMEN DE CAMPOS POR MÉTODO**

### Método 2 Componentes:
- peso_actual
- altura_actual
- pliegue_tricipital
- pliegue_subescapular
- pliegue_suprailíaco
- pliegue_bicipital (opcional)

### Método 4 Componentes:
- peso_actual
- altura_actual
- altura_sentado
- diametro_humero
- diametro_femur
- circunferencia_brazo_contraido (¿requerido?)
- circunferencia_pantorrilla (¿requerido?)
- circunferencia_muneca (¿requerido? - según respuestas anteriores SÍ)
- Todos los pliegues del método 2 componentes

### Método 5 Componentes:
- Todos los campos del método 4 componentes
- (No requiere campos adicionales específicos para piel)

### Método Somatotipo:
- peso_actual
- altura_actual
- altura_sentado
- pliegue_tricipital
- pliegue_subescapular
- pliegue_suprailíaco
- diametro_humero
- diametro_femur
- circunferencia_brazo_contraido
- circunferencia_pantorrilla

---

## ⚠️ **CAMPOS QUE SE MENCIONAN EN `verificarDatosDisponibles()` PERO NO SE USAN EN EL CÁLCULO:**

### Método 5 Componentes:
- `diametro_biacromial` - Se menciona como requerido pero NO se usa en el cálculo real
- `diametro_bi_iliocristal` - Se menciona como requerido pero NO se usa en el cálculo real

**⚠️ PREGUNTA:** ¿Estos diámetros son necesarios para el método 5 componentes o se pueden quitar de la validación?

---

## ✅ **CAMPOS RECIÉN AGREGADOS (según respuestas anteriores):**
- `pliegue_supraespinal` - ¿Se usa en algún método?
- `circunferencia_muneca` - Confirmado necesario para 4 componentes
- `circunferencia_antebrazo_maximo` - ¿Se usa en algún método?
- `circunferencia_muslo_maximo` - ¿Se usa en algún método?
- `masa_osea` - ¿Se guarda el resultado calculado o solo se muestra?

---

## 📋 **CHECKLIST PARA CONFIRMAR CON NUTRICIONISTA:**

- [ ] Método 2 componentes: ¿Los 4 pliegues son correctos o faltan/sobran?
- [ ] Método 4 componentes: ¿Se necesita `circunferencia_muneca`? (Ya confirmado SÍ)
- [ ] Método 4 componentes: ¿Se necesita `circunferencia_brazo_contraido` y `circunferencia_pantorrilla`?
- [ ] Método 5 componentes: ¿Se necesitan `diametro_biacromial` y `diametro_bi_iliocristal`?
- [ ] Método 5 componentes: ¿La aproximación de masa de piel es suficiente?
- [ ] Somatotipo: ¿La fórmula Heath-Carter es correcta o necesita ajustes?
- [ ] ¿Los nuevos campos (`pliegue_supraespinal`, `circunferencia_antebrazo_maximo`, `circunferencia_muslo_maximo`) se usan en algún método?
- [ ] ¿Se debe guardar `masa_osea` calculada en `historial_clinico` o solo mostrarla?
