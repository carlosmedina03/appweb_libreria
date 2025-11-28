<?php
// ============================================================
// RESPONSABLE: Rol 2 (Diseño) y Rol 4 (Datos)
// REQUERIMIENTO: "3.4 Compras por rango... Columnas: Folio, Fecha, Proveedor..."
// ============================================================
// 1. Ejecutar Query 6 de consultas_base.sql.
// 2. Filtros obligatorios: Fecha inicio, Fecha fin.

// BACKEND ABAJO (NO BORRAR)
require_once '../config/db.php';
require_once '../includes/auth.php';

$fecha_ini = $_GET['inicio'] ?? date('Y-m-01 00:00:00');
$fecha_fin = $_GET['fin'] ?? date('Y-m-t 23:59:59');

$sql = "SELECT c.id as folio, c.fecha_hora, p.nombre as proveedor, c.total_compra 
        FROM compras c 
        JOIN proveedores p ON c.id_proveedor = p.id
        WHERE c.fecha_hora BETWEEN '$fecha_ini' AND '$fecha_fin'
        ORDER BY c.fecha_hora DESC";

$res = $mysqli->query($sql);
$compras = [];
$total_comprado = 0;

while ($row = $res->fetch_assoc()) {
    $total_comprado += $row['total_compra'];
    $compras[] = $row;
}
?>