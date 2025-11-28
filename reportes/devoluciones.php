<?php
// ============================================================
// RESPONSABLE: Rol 2 (Diseño) y Rol 4 (Datos)
// REQUERIMIENTO: "3.5 Devoluciones... Motivo, Cantidad devuelta..."
// ============================================================
// 1. Ejecutar Query 7 de consultas_base.sql.
// 2. Mostrar motivo de devolución.

// BACKEND ABAJO (NO BORRAR)
require_once '../config/db.php';
require_once '../includes/auth.php';

$fecha_ini = $_GET['inicio'] ?? date('Y-m-01 00:00:00');
$fecha_fin = $_GET['fin'] ?? date('Y-m-t 23:59:59');

$sql = "SELECT d.fecha_hora, d.id_venta as folio_venta, l.codigo, l.titulo as nombre,
               dd.cantidad as cant_dev, dd.monto_reembolsado, d.motivo
        FROM devoluciones d
        JOIN detalle_devoluciones dd ON d.id = dd.id_devolucion
        JOIN libros l ON dd.id_libro = l.id
        WHERE d.fecha_hora BETWEEN '$fecha_ini' AND '$fecha_fin'
        ORDER BY d.fecha_hora DESC";

$res = $mysqli->query($sql);
$devoluciones = [];
$total_unidades_dev = 0;
$total_monto_dev = 0;

while ($row = $res->fetch_assoc()) {
    $total_unidades_dev += $row['cant_dev'];
    $total_monto_dev += $row['monto_reembolsado'];
    $devoluciones[] = $row;
}
?>