# 🧪 Guía de Tarjetas de Prueba - Mercado Pago Sandbox (Chile)

## ⚠️ Error Común: "Algo salió mal... No pudimos procesar tu pago"

Si estás viendo este error al intentar completar un pago en sandbox, es probable que estés usando datos de tarjeta incorrectos.

## ✅ Tarjetas de Prueba Válidas (Chile - CLP)

### 1. Tarjeta Aprobada (Pago Exitoso) ⭐ RECOMENDADA
```
Número de tarjeta: 5031 7557 3453 0604
CVV: 123
Vencimiento: 11/30 (o cualquier fecha futura)
Nombre del titular: APRO
Documento: 123456789
```

**Tarjetas alternativas para pago aprobado:**
- Crédito Mastercard: `5416 7526 0258 2580` (CVV: 123)
- Crédito Visa: `4168 8188 4444 7115` (CVV: 123)
- Débito Mastercard: `5241 0198 2664 6950` (CVV: 123)
- Débito Visa: `4023 6535 2391 4373` (CVV: 123)

### 2. Tarjeta Rechazada (Pago Rechazado)
```
Número de tarjeta: 5031 4332 1540 6351
CVV: 123
Vencimiento: 11/30 (o cualquier fecha futura)
Nombre del titular: OTHE
Documento: 123456789
```

### 3. Tarjeta Pendiente (Pago Pendiente)
```
Número de tarjeta: 5031 4332 1540 6351
CVV: 123
Vencimiento: 11/30 (o cualquier fecha futura)
Nombre del titular: CONT
Documento: 123456789
```

## 📋 Pasos para Probar un Pago

1. **Recibe el email con el botón de pago** (después de confirmar la cita)
2. **Haz clic en el botón "Pagar Ahora"**
3. **En la página de Mercado Pago, completa los datos:**
   - Usa una de las tarjetas de prueba listadas arriba
   - **CVV siempre debe ser `123`**
   - **Vencimiento debe ser una fecha futura** (ej: `11/30`)
   - El email del pagador se genera automáticamente (es un email de prueba)
4. **Haz clic en "Continuar" y luego en "Pagar"**

## 🔍 Verificación

- ✅ Si usas la tarjeta **Aprobada** (`5031 7557 3453 0604`), el pago debería completarse exitosamente
- ❌ Si usas la tarjeta **Rechazada** (`5031 4332 1540 6351` con titular `OTHE`), el pago será rechazado
- ⏳ Si usas la tarjeta **Pendiente** (`5031 4332 1540 6351` con titular `CONT`), el pago quedará pendiente

## ⚠️ Errores Comunes

### Error: "Algo salió mal... No pudimos procesar tu pago"
**Causa**: Estás usando una tarjeta que no es válida para sandbox
**Solución**: Usa una de las tarjetas de prueba listadas arriba

### Error: "Una de las partes con la que intentas hacer el pago es de prueba"
**Causa**: El email del pagador no es un email de prueba válido
**Solución**: El sistema ya genera automáticamente un email de prueba válido. Si persiste, verifica los logs.

### Error: "Tarjeta inválida"
**Causa**: El número de tarjeta no es una tarjeta de prueba válida
**Solución**: Usa exactamente uno de los números listados arriba

## 📚 Referencias

- [Documentación Oficial de Mercado Pago - Tarjetas de Prueba](https://www.mercadopago.cl/developers/en/docs/checkout-api-payments/additional-content/your-integrations/test/cards)
- [Panel de Desarrolladores de Mercado Pago](https://www.mercadopago.cl/developers/panel)
