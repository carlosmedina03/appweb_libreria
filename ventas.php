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

// BACKEND ABAJO (NO BORRAR)
// REQUERIMIENTO: "Ventas... cajero (desde sesión)"
require_once 'includes/auth.php'; 
// Nota: No restringimos rol porque Admin y Operador pueden vender.

// Inicializar carrito vacío si es la primera vez que entra
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Datos del cajero para mostrar en pantalla (opcional, pero útil para UX)
$cajero_nombre = $_SESSION['user']['nombre_completo'];
$cajero_id = $_SESSION['user']['id'];

// AHORA VIENE EL HTML DEL ROL 2...
// El resto de la lógica (buscar producto, agregar) se hace via AJAX con los archivos que ya te di.
?>