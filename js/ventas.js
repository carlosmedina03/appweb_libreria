// RESPONSABLE: Rol 2 (Front) y Rol 4 (Integración)
// REQUERIMIENTO: "Escaneo... y presiona Enter en un <input autofocus>"

// 1. Detectar evento 'submit' o 'change' del input código.
// 2. Fetch a 'ajax/buscar_producto.php?q=' + codigo.
// 3. Si encuentra: Fetch a 'ajax/carrito_add.php'.
// 4. Actualizar DOM de la tabla del carrito.
// 5. Manejar el evento del botón "Confirmar Venta" -> AJAX -> Imprimir.