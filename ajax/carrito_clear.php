<?php
// ajax/carrito_clear.php
// RESPONSABLE: Rol 4 (Backend)
// REQUERIMIENTO: Funcionalidad implícita de gestión de carrito (Cancelar Venta).

session_start();

// Vacía el carrito por completo
unset($_SESSION['carrito']);

header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'msg' => 'Carrito vaciado con éxito.']);
?>