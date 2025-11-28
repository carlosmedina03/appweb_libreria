<?php
// REQUERIMIENTO: "Exportación CSV (obligatoria)... con BOM"
// ==========================================
require_once '../config/db.php';

if (!isset($_GET['tipo'])) die("Tipo de reporte no especificado");

$tipo = $_GET['tipo'];
$filename = "reporte_" . $tipo . "_" . date('Ymd_Hi') . ".csv";

// Headers para forzar descarga
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Salida
$output = fopen('php://output', 'w');

// BOM para que Excel lea acentos correctamente
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Lógica según reporte (Usando tus Consultas Base)
switch ($tipo) {
    case 'inventario':
        // Encabezados CSV
        fputcsv($output, ['Codigo', 'Nombre', 'Precio', 'Existencia', 'Estado']);
        
        $sql = "SELECT l.codigo, l.titulo, l.precio_venta, e.cantidad, 
                CASE l.estatus WHEN 1 THEN 'ACTIVO' ELSE 'INACTIVO' END 
                FROM libros l JOIN existencias e ON l.id = e.id_libro WHERE l.estatus=1";
        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) fputcsv($output, $row);
        break;

    case 'ventas':
        fputcsv($output, ['Folio', 'Fecha', 'Cajero', 'Subtotal', 'IVA', 'Total']);
        // Query de tu archivo consultas_base.sql
        $sql = "SELECT v.id, v.fecha_hora, u.nombre_completo, v.subtotal, v.iva, v.total 
                FROM ventas v JOIN usuarios u ON v.id_usuario = u.id ORDER BY v.fecha_hora DESC";
        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) fputcsv($output, $row);
        break;
}

fclose($output);
exit;
?>