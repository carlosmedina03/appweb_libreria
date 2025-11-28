<?php
// ============================================================
// RESPONSABLE: Rol 2 (Diseño) y Rol 4 (Datos)
// REQUERIMIENTO: "3.2 Ventas por rango... Columnas: Folio, Fecha, Cajero..."
// ============================================================
// 1. Ejecutar Query 4 de consultas_base.sql.
// 2. Totales al final: Importe facturado, Número de tickets, Ticket promedio.

// BACKEND ABAJO (NO BORRAR)
// REQUERIMIENTO: "Filtros obligatorios: fecha inicio, fecha fin"
// REQUERIMIENTO: "Totales al final: Importe facturado, Número tickets, Promedio"
// ---------------------------------------------------------
require_once '../config/db.php';
require_once '../includes/auth.php';

// 1. Fechas por defecto (Mes actual si no envían nada)
$fecha_ini = $_GET['inicio'] ?? date('Y-m-01 00:00:00');
$fecha_fin = $_GET['fin'] ?? date('Y-m-t 23:59:59');
$filtro_cajero = isset($_GET['cajero']) ? intval($_GET['cajero']) : 0;

// 2. Query (Basado en Consultas Base 3.2)
$sql = "SELECT v.id as folio, v.fecha_hora, u.nombre_completo as cajero, v.subtotal, v.iva, v.total 
        FROM ventas v 
        JOIN usuarios u ON v.id_usuario = u.id 
        WHERE v.fecha_hora BETWEEN '$fecha_ini' AND '$fecha_fin'";

if ($filtro_cajero > 0) {
    $sql .= " AND v.id_usuario = $filtro_cajero";
}

$sql .= " ORDER BY v.fecha_hora DESC";

$resultado = $mysqli->query($sql);

// 3. Preparar Dataset y Calcular Totales
$ventas = [];
$suma_total_facturado = 0;

while ($row = $resultado->fetch_assoc()) {
    $suma_total_facturado += $row['total'];
    $ventas[] = $row;
}

$num_tickets = count($ventas);
$ticket_promedio = ($num_tickets > 0) ? ($suma_total_facturado / $num_tickets) : 0;

// AHORA EL ROL 2 (UX) TIENE TODO LISTO PARA PINTAR LA TABLA
?>