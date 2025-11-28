<?php
// ============================================================
// RESPONSABLE: Rol 4 (Lógica) y Rol 2 (UI)
// REQUERIMIENTO: "Compras... Capturan encabezado... y detalle (producto, cantidad, costo)"
// ============================================================
require_once 'includes/auth.php';
// Guard: require_admin(); // Solo admin puede registrar compras.

// TODO:
// 1. Formulario para seleccionar Proveedor.
// 2. Tabla dinámica (JS) para agregar productos al listado de compra.
// 3. Botón "Guardar Compra" -> ajax/confirmar_compra.php.

// BACKEND ACA (NO BORRAR)
// REQUERIMIENTO: "Compras. Capturan encabezado (fecha, proveedor...)"
require_once 'config/db.php';
require_once 'includes/auth.php';

// 1. Guard: Solo Admin puede registrar compras de inventario
if ($_SESSION['user']['rol'] !== 'admin') {
    header("Location: dashboard.php"); // O mostrar error 403
    exit;
}

// 2. Obtener lista de proveedores para el <select> del HTML
// El de UX usará la variable $proveedores en un foreach
$sql_prov = "SELECT id, nombre FROM proveedores WHERE estatus = 1 ORDER BY nombre";
$res_prov = $mysqli->query($sql_prov);
$proveedores = [];
while ($row = $res_prov->fetch_assoc()) {
    $proveedores[] = $row;
}

// AHORA VIENE EL HTML DEL ROL 2...
// Nota para UX: Usar foreach($proveedores as $p) para llenar el <select name="proveedor">
?>