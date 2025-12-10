<?php
// ============================================================
// RESPONSABLE: Rol 7 (POS Offline-First (PWA) con sincronización diferida)
// REQUERIMIENTO: "Descarga de Catálogo para Modo Offline"
// DESCRIPCIÓN: Obtiene productos y stock actual para llenar la IndexedDB local
// ============================================================

require_once '../config/db.php';
header('Content-Type: application/json');

// 1. CONSULTA DE PRODUCTOS ACTIVOS
// Se hace un LEFT JOIN con existencias para saber cuánto stock hay disponible
// Seleccionamos 'codigo' porque será nuestra llave de búsqueda offline
$sql = "SELECT l.id, l.codigo, l.titulo, l.precio_venta, e.cantidad as stock 
        FROM libros l 
        LEFT JOIN existencias e ON l.id = e.id_libro
        WHERE l.estatus = 1"; // Solo traemos lo que se puede vender

$result = $mysqli->query($sql);

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

// 2. ENVÍO DE DATOS
// Se envía todo el array en formato JSON para que el JS lo procese
echo json_encode($productos);
?>