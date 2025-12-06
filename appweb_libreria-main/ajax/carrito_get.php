<?php
// ajax/carrito_get.php
// RESPONSABLE: Rol 4 (Backend)
// REQUERIMIENTO: Funcionalidad implícita de gestión de carrito.
// Devuelve el estado actual del carrito en la sesión.

session_start();

// Si el carrito no existe, devuelve un array vacío.
$carrito_data = $_SESSION['carrito'] ?? [];

header('Content-Type: application/json');
echo json_encode(['carrito' => $carrito_data]);
?>