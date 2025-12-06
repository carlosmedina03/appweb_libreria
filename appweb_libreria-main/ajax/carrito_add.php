<?php
// REQUERIMIENTO: "Escaneo que agrega/incrementa en carrito en sesión"
// ==========================================
session_start();

if (!isset($_POST['id']) || !isset($_POST['titulo']) || !isset($_POST['precio'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos']);
    exit;
}

$id = $_POST['id'];
$titulo = $_POST['titulo'];
$precio = floatval($_POST['precio']);
$cantidad = 1; // Por defecto suma 1 al escanear

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Lógica de incremento
if (isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id]['cantidad']++;
    $_SESSION['carrito'][$id]['importe'] = $_SESSION['carrito'][$id]['cantidad'] * $precio;
} else {
    $_SESSION['carrito'][$id] = [
        'id' => $id,
        'titulo' => $titulo,
        'precio' => $precio,
        'cantidad' => 1,
        'importe' => $precio
    ];
}

echo json_encode(['status' => 'ok', 'carrito' => $_SESSION['carrito']]);
?>