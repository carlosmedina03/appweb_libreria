<?php
// REQUERIMIENTO: "Escaneo... buscar por items.codigo"
// ==========================================
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isset($_GET['q'])) {
    json_response(['error' => 'Falta parámetro q'], 400);
}

$q = sanear($mysqli, $_GET['q']);

// Buscamos por código EXACTO (para escáner) o por nombre (LIKE)
// REQUERIMIENTO: "Búsqueda LIKE"
$sql = "SELECT l.id, l.codigo, l.titulo, l.precio_venta, e.cantidad as stock 
        FROM libros l 
        LEFT JOIN existencias e ON l.id = e.id_libro
        WHERE l.estatus = 1 AND (l.codigo = '$q' OR l.titulo LIKE '%$q%')
        LIMIT 10";

$res = $mysqli->query($sql);
$productos = [];

while ($row = $res->fetch_assoc()) {
    $productos[] = $row;
}

echo json_encode($productos);
?>