<?php
// ============================================================
// RESPONSABLE: Rol 2 (Diseño) y Rol 4 (Datos)
// REQUERIMIENTO: "3.3 Detalle de ventas... Columnas: Fecha, Folio, Código..."
// ============================================================
// 1. Ejecutar Query 5 de consultas_base.sql.
// 2. Totales al final: Unidades vendidas, Importe total.

// BACKEND ABAJO (NO BORRAR)
// REQUERIMIENTO: "3.3 Detalle de ventas... Columnas: ..., Importe línea"
// ---------------------------------------------------------
require_once '../config/db.php';
require_once '../includes/auth.php';

$fecha_ini = $_GET['inicio'] ?? date('Y-m-01 00:00:00');
$fecha_fin = $_GET['fin'] ?? date('Y-m-t 23:59:59');

// Query (Basado en Consultas Base 3.3)
$sql = "SELECT v.fecha_hora, v.id as folio, l.codigo, l.titulo as nombre, 
               dv.cantidad, dv.precio_unitario, dv.importe as importe_linea
        FROM detalle_ventas dv
        JOIN ventas v ON dv.id_venta = v.id
        JOIN libros l ON dv.id_libro = l.id
        WHERE v.fecha_hora BETWEEN '$fecha_ini' AND '$fecha_fin'
        ORDER BY v.fecha_hora DESC";

$resultado = $mysqli->query($sql);

$detalles = [];
$suma_unidades = 0;
$suma_importe = 0;

while ($row = $resultado->fetch_assoc()) {
    $suma_unidades += $row['cantidad'];
    $suma_importe += $row['importe_linea'];
    $detalles[] = $row;
}
?>