# Validación: Composición corporal 2, 4 y 5 componentes

## 1. Mapeo especificación → campos en BD

### 2 COMPONENTES (Kerr – Excel “KG_% DE MM Y MA - 2 COMPONENTES”)

| Especificación                    | Campo en BD                     | ¿Existe? |
|----------------------------------|----------------------------------|----------|
| **Pliegues:** tricipital, subescapular, supra espinal, abdominal, muslo medial, pantorrilla | pliegue_tricipital, pliegue_subescapular, pliegue_supraespinal, pliegue_abdominal, pliegue_muslo_medial, pliegue_pantorrilla_medial | Sí |
| **Circunferencias:** brazo relajado, antebrazo, tórax, muslo máximo, pantorrilla | circunferencia_brazo_relajado, circunferencia_antebrazo_maximo, circunferencia_torax, circunferencia_muslo_maximo, circunferencia_pantorrilla | Sí |

**Estado en código:** El servicio usaba **4 pliegues** (tricipital, subescapular, suprailíaco, bicipital) y **Durnin-Womersley**. El Excel usa **6 pliegues** + **5 perímetros** y metodología **Kerr** (Masa Adiposa y Masa Muscular Kerr).  
**Ajuste:** El cálculo de 2 componentes debe usar **suma de 6 pliegues** (tricipital, subescapular, supraespinal, abdominal, muslo_medial, pantorrilla_medial). La fórmula Kerr exacta depende del Excel; mientras tanto se mantiene D-W con suma de 6 pliegues. Las 5 circunferencias se usan cuando se implemente Kerr completo.

---

### 4 COMPONENTES (Fisionutdep – Excel “Composición Corporal Fisionutdep - 4 COMPONENTES”)

| Especificación                    | Campo en BD                     | ¿Existe? |
|----------------------------------|----------------------------------|----------|
| **Pliegues:** bicipital, tricipital, subescapular, supra ilíaco, pantorrilla, muslo, abdominal | pliegue_bicipital, pliegue_tricipital, pliegue_subescapular, pliegue_suprailíaco, pliegue_pantorrilla_medial, pliegue_muslo_anterior*, pliegue_abdominal | Sí (*“muslo” = muslo_anterior en Fisionutdep) |
| **Circunferencias:** cintura, cadera, pantorrilla, brazo relajado, brazo contraído, muñeca, muslo | circunferencia_cintura, circunferencia_cadera, circunferencia_pantorrilla, circunferencia_brazo_relajado, circunferencia_brazo_contraido, circunferencia_muneca, circunferencia_muslo_medio | Sí |
| **Diámetros:** húmero, muñeca, fémur | diametro_humero, diametro_muneca, diametro_femur | Sí |

**Estado en código:** Se usa método 2 componentes (antes con 4 pliegues) para grasa, De Rose para masa ósea, 24 % peso para residual y el resto como músculo.  
**Ajuste:** Garantizar que el cálculo de 4 componentes (y su verificación de datos) considere todos estos campos. La fórmula Fisionutdep (Siri, etc.) puede incorporarse después si se dispone del detalle del Excel.

---

### 5 COMPONENTES (Francis Holway – referencia “Antropgym Francis Holway - 5 COMPONENTES”)

| Especificación                    | Campo en BD                     | ¿Existe? |
|----------------------------------|----------------------------------|----------|
| **Pliegues:** tríceps, subescapular, suprailíaco, abdominal, muslo medial, pantorrilla | pliegue_tricipital, pliegue_subescapular, pliegue_suprailíaco, pliegue_abdominal, pliegue_muslo_medial, pliegue_pantorrilla_medial | Sí |
| **Circunferencias:** cabeza, brazo relajado, brazo contraído, antebrazo, tórax, cintura, cadera, muslo máximo, muslo medial, pantorrilla | circunferencia_cabeza, circunferencia_brazo_relajado, circunferencia_brazo_contraido, circunferencia_antebrazo_maximo, circunferencia_torax, circunferencia_cintura, circunferencia_cadera, circunferencia_muslo_maximo, circunferencia_muslo_medio, circunferencia_pantorrilla | Sí |
| **Diámetros:** biacromial, bi-iliocrestídio, tórax transverso, tórax anteroposterior, húmero, femoral | diametro_biacromial, diametro_bi_iliocristal, diametro_torax_transverso, diametro_torax_anteroposterior, diametro_humero, diametro_femur | Sí |

**Estado en código:** Se apoya en 4 componentes y añade masa de piel por superficie corporal.  
**Ajuste:** La verificación de datos disponibles debe exigir los campos de la tabla para 5 componentes. El cálculo ya usa 4 componentes como base; cuando las fórmulas de Holway estén definidas, se pueden reemplazar/ajustar.

---

## 2. Resumen de diferencias encontradas

| Método   | Qué teníamos                                                    | Qué pide la especificación / Excel                    |
|----------|------------------------------------------------------------------|--------------------------------------------------------|
| 2 comp   | 4 pliegues (tri, sub, suprailíaco, biceps), D-W, sin perímetros  | 6 pliegues (tri, sub, supraespinal, abdominal, muslo medial, pantorrilla) + 5 perímetros; Kerr |
| 4 comp   | Grasa vía 2 comp (4 pliegues), De Rose hueso, 24 % residual      | 7 pliegues, 7 circunferencias, 3 diámetros; Fisionutdep/Siri |
| 5 comp   | 4 comp + piel                                                    | Mismos inputs que tabla 5 comp; Holway               |

---

## 3. Cambios realizados en código

1. **2 componentes:**  
   - Se usa **suma de 6 pliegues**: tricipital, subescapular, supraespinal, abdominal, muslo_medial, pantorrilla_medial.  
   - Densidad y % grasa siguen con Durnin-Womersley hasta disponer de ecuaciones Kerr explícitas del Excel.

2. **verificarDatosDisponibles:**  
   - 2 comp: exige los 6 pliegues (+ peso, altura).  
   - 4 comp: exige pliegues, circunferencias y diámetros según tabla 4 comp.  
   - 5 comp: exige pliegues, circunferencias y diámetros según tabla 5 comp.

3. **4 y 5 componentes:**  
   - Usan los campos listados en las tablas al calcular y al verificar datos.  
   - Las fórmulas exactas de Fisionutdep y Holway se pueden añadir cuando se extraigan del Excel o de la bibliografía.

---

## 4. Pendiente para igualar al Excel

- **2 comp:** Implementar fórmulas **Kerr** (Masa Adiposa y Masa Muscular) usando 6 pliegues + 5 perímetros, a partir del archivo “KG_% DE MM Y MA - 2 COMPONENTES.xlsx”.
- **4 comp:** Revisar ecuaciones del archivo “Composición Corporal Fisionutdep - 4 COMPONENTES.xlsm” (Siri, área muscular brazo, masa grasa, ósea, residual) y alinear salidas (kg y %).
- **5 comp:** ✅ Implementado **modelo Kerr 5 componentes** (MG, MO, MR, MP, MM por diferencia). Pendiente: documentar relación con Holway si aplica.
- ~~**5 comp:** Definir ecuaciones Francis Holway~~ a partir de “Antropgym Francis Holway - 5 COMPONENTE.xlsx” y aplicarlas con los campos ya mapeados.
