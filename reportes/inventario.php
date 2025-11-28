<?php
// ============================================================
// RESPONSABLE: Rol 2 (Maquetación A4) y Rol 4 (Datos)
// REQUERIMIENTO: "Reporte 3.1 Inventario actual... Filtros: q, solo activos"
// ============================================================
// 1. Usar Query 3 de consultas_base.sql.
// 2. Mostrar tabla con encabezados que resalten y líneas zebra.
// 3. Numeración de página en pie.

// BACKEND ABAJO (NO BORRAR)
// REQUERIMIENTO: "Filtros obligatorios: q (código/nombre), solo activos"
// ---------------------------------------------------------
require_once '../config/db.php';
require_once '../includes/auth.php'; // Protegido

// 1. Recibir Filtros
$filtro_q = isset($_GET['q']) ? $mysqli->real_escape_string($_GET['q']) : '';
$solo_activos = isset($_GET['activos']) ? true : false;

// 2. Construir Query (Basado en Consultas Base 3.1)
$sql = "SELECT l.codigo, l.titulo as nombre, l.precio_venta as precio, e.cantidad as existencia, l.estatus 
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

// 3. Ejecutar y preparar Dataset
$resultado = $mysqli->query($sql);
$productos = [];
$total_existencias = 0;

while ($row = $resultado->fetch_assoc()) {
    $row['estado_str'] = ($row['estatus'] == 1) ? 'ACTIVO' : 'INACTIVO';
    $total_existencias += $row['existencia'];
    $productos[] = $row;
}

$total_items = count($productos);

// AHORA EL ROL 2 (UX) USARÁ $productos, $total_items y $total_existencias EN EL HTML
?>