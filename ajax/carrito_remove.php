<?php
// ajax/carrito_remove.php
// RESPONSABLE: Rol 4 (Backend)
// REQUERIMIENTO: Funcionalidad implícita de gestión de carrito.
session_start();

if (!isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'ID de producto no especificado']);
    exit;
}

$id = $_POST['id'];

// Verificar que el carrito y el producto existan antes de eliminar
if (isset($_SESSION['carrito']) && isset($_SESSION['carrito'][$id])) {
    unset($_SESSION['carrito'][$id]);
}

// Devolver siempre el estado actual del carrito (incluso si está vacío)
echo json_encode(['status' => 'ok', 'carrito' => $_SESSION['carrito'] ?? []]);
?>