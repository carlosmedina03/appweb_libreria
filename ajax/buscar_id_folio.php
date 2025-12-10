<?php
// ============================================================
// RESPONSABLE: Rol 7 (POS Offline-First (PWA) con sincronización diferida)
// REQUERIMIENTO: "Interoperabilidad Offline-Online"
// DESCRIPCIÓN: Traduce folios de texto (ej. "OFF-123") a IDs numéricos reales de la BD
// ============================================================

require_once '../config/db.php';
header('Content-Type: application/json');

// 1. VALIDACIÓN DE ENTRADA
if (!isset($_GET['folio'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Falta parámetro folio']);
    exit;
}

// 2. DETECCIÓN AUTOMÁTICA DE CONEXIÓN
// Se adapta a la variable que use el sistema base ($mysqli, $conexion o $conn)
if (isset($mysqli)) $db = $mysqli;
elseif (isset($conexion)) $db = $conexion;
elseif (isset($conn)) $db = $conn;

// 3. CONSULTA DE TRADUCCIÓN
// Limpiamos la entrada para evitar inyección SQL
$folio = $db->real_escape_string($_GET['folio']);

// Buscamos el ID numérico asociado al folio de texto
$sql = "SELECT id FROM ventas WHERE folio = '$folio' LIMIT 1";
$res = $db->query($sql);

// 4. RESPUESTA AL FRONTEND
if ($res && $res->num_rows > 0) {
    $fila = $res->fetch_assoc();
    // Devolvemos el ID real para que el formulario de devoluciones lo use
    echo json_encode(['status' => 'ok', 'id_real' => $fila['id']]);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'No encontrado']);
}
?>