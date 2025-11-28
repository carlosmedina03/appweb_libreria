<?php
// ============================================================
// RESPONSABLE: Rol 4 (Lógica) y Rol 2 (UI)
// REQUERIMIENTO: "Ventas... captura por código o buscador... retorno automático"
// ============================================================
require_once 'includes/auth.php'; // Guard: Solo operadores y admins

// TODO:
// 1. Input autofocus para el lector de código de barras.
// 2. Tabla visual del "Carrito de compras" actual (desde $_SESSION).
// 3. Botón "Confirmar Venta" -> llama a ajax/confirmar_venta.php.
// 4. Al terminar, abrir ticket.php en ventana nueva (window.open).
?>