<?php
// =================================================================
// ARCHIVO: exportar.php
// OBJETIVO: Generar y forzar la descarga de archivos CSV con filtros,
//           asegurando que los encabezados coincidan con el front-end.
// =================================================================
require_once '../config/db.php';

if (!isset($_GET['tipo'])) {
    die("Tipo de reporte no especificado");
}

$tipo = $_GET['tipo'];
$filename = "reporte_" . $tipo . "_" . date('Ymd_Hi') . ".csv";

// Headers para forzar descarga
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Salida al buffer de PHP
$output = fopen('php://output', 'w');

// BOM (Byte Order Mark) para que Excel lea acentos correctamente
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Lógica para construir la consulta y aplicar filtros
switch ($tipo) {

    case 'inventario':
        // 1. Recoger y sanitizar Filtros
        $filtro_q = isset($_GET['q']) ? $mysqli->real_escape_string($_GET['q']) : '';
        $solo_activos = isset($_GET['activos']) && $_GET['activos'] == '1' ? true : false;
        
        // 2. Encabezados CSV (Sincronizado con inventario.php)
        fputcsv($output, ['Código', 'Título del Libro', 'Precio Venta', 'Stock Actual', 'Estado']);
        
        // 3. Construir SQL con filtros
        $sql = "SELECT l.codigo, l.titulo, l.precio_venta, e.cantidad, 
                CASE l.estatus WHEN 1 THEN 'ACTIVO' ELSE 'INACTIVO' END 
                FROM libros l 
                JOIN existencias e ON l.id = e.id_libro 
                WHERE 1=1";
        
        if ($filtro_q != '') {
            $sql .= " AND (l.codigo LIKE '%$filtro_q%' OR l.titulo LIKE '%$filtro_q%')";
        }
        if ($solo_activos) {
            $sql .= " AND l.estatus = 1";
        }
        $sql .= " ORDER BY l.titulo";

        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;

    case 'ventas':
        // 1. Recoger y sanitizar Filtros
        $fecha_ini_input = $_GET['inicio'] ?? date('Y-m-01');
        $fecha_fin_input = $_GET['fin'] ?? date('Y-m-d');
        $filtro_cajero = isset($_GET['cajero']) ? intval($_GET['cajero']) : 0;

        $fecha_ini_db = $fecha_ini_input . ' 00:00:00';
        $fecha_fin_db = $fecha_fin_input . ' 23:59:59';

        // 2. Encabezados CSV (Sincronizado con ventas_rango.php)
        fputcsv($output, ['Folio', 'Fecha/Hora', 'Cajero', 'Subtotal', 'IVA', 'Total Venta']);
        
        // 3. Construir SQL con filtros
        $sql = "SELECT v.id, v.fecha_hora, u.nombre_completo, v.subtotal, v.iva, v.total 
                FROM ventas v 
                JOIN usuarios u ON v.id_usuario = u.id 
                WHERE v.fecha_hora BETWEEN '$fecha_ini_db' AND '$fecha_fin_db'";
        
        if ($filtro_cajero > 0) {
            $sql .= " AND v.id_usuario = $filtro_cajero";
        }

        $sql .= " ORDER BY v.fecha_hora DESC";

        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;

    case 'detalle_ventas':
        // 1. Recoger y sanitizar Filtros
        $fecha_ini_input = $_GET['inicio'] ?? date('Y-m-01');
        $fecha_fin_input = $_GET['fin'] ?? date('Y-m-d');
        $filtro_producto = isset($_GET['producto']) ? $mysqli->real_escape_string($_GET['producto']) : ''; 

        $fecha_ini_db = $fecha_ini_input . ' 00:00:00';
        $fecha_fin_db = $fecha_fin_input . ' 23:59:59';
        
        // 2. Encabezados CSV (Sincronizado con detalle_ventas.php, manteniendo campos clave)
        // Nota: El reporte web solo muestra 6 columnas, el CSV exporta más detalle.
        fputcsv($output, ['Folio', 'Fecha/Hora', 'Código Producto', 'Nombre Producto', 'Cant.', 'Precio Unit.', 'Subtotal Línea']);

        // 3. Construir SQL con filtros
        $sql = "SELECT v.id as folio, v.fecha_hora, l.codigo, l.titulo as nombre, 
                dv.cantidad, dv.precio_unitario, dv.importe as importe_linea
                FROM detalle_ventas dv
                JOIN ventas v ON dv.id_venta = v.id
                JOIN libros l ON dv.id_libro = l.id
                WHERE v.fecha_hora BETWEEN '$fecha_ini_db' AND '$fecha_fin_db'"; 

        if ($filtro_producto != '') {
            $sql .= " AND (l.titulo LIKE '%$filtro_producto%' OR l.codigo LIKE '%$filtro_producto%')";
        }

        $sql .= " ORDER BY v.fecha_hora DESC";

        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;

    case 'devoluciones':
        // 1. Recoger y sanitizar Filtros
        $fecha_ini_input = $_GET['inicio'] ?? date('Y-m-01');
        $fecha_fin_input = $_GET['fin'] ?? date('Y-m-d');

        $fecha_ini_db = $fecha_ini_input . ' 00:00:00';
        $fecha_fin_db = $fecha_fin_input . ' 23:59:59';

        // 2. Encabezados CSV (Sincronizado con devoluciones.php)
        fputcsv($output, ['Folio Venta', 'Fecha Devolución', 'Código Producto', 'Nombre Producto', 'Cant. Dev.', 'Monto Devuelto', 'Motivo']);
        
        // 3. Construir SQL con filtros
        $sql = "SELECT d.fecha_hora, d.id_venta, l.codigo, l.titulo,
                        dd.cantidad, dd.monto_reembolsado, d.motivo
                FROM devoluciones d
                JOIN detalle_devoluciones dd ON d.id = dd.id_devolucion
                JOIN libros l ON dd.id_libro = l.id
                WHERE d.fecha_hora BETWEEN '$fecha_ini_db' AND '$fecha_fin_db'
                ORDER BY d.fecha_hora DESC";

        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;

    case 'compras':
        fputcsv($output, ['Folio Compra', 'Fecha/Hora', 'Proveedor', 'Total Compra']);
        $sql = "SELECT c.id, c.fecha_hora, p.nombre, c.total_compra 
                FROM compras c 
                JOIN proveedores p ON c.id_proveedor = p.id 
                ORDER BY c.fecha_hora DESC";
        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;
        
    default:
        die("Tipo de reporte no válido");
}

fclose($output);
exit;
?>