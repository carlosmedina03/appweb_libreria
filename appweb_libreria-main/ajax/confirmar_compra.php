<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Validar sesión y permisos (Solo admin suele comprar, o según reglas de negocio)
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'msg' => 'No autorizado']);
    exit;
}

// Recibir JSON raw (asumiendo que el Front manda JSON)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['proveedor']) || empty($input['items'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Datos de compra incompletos']);
    exit;
}

$id_usuario = $_SESSION['user']['id'];
$id_proveedor = intval($input['proveedor']);
$items = $input['items']; // Array de {id_libro, cantidad, costo}

$total_compra = 0;
foreach ($items as $item) {
    $total_compra += $item['cantidad'] * $item['costo'];
}

$mysqli->begin_transaction();

try {
    // 1. Insertar Encabezado Compra
    $sql_compra = "INSERT INTO compras (id_proveedor, id_usuario, total_compra, fecha_hora) VALUES (?, ?, ?, NOW())";
    $stmt = $mysqli->prepare($sql_compra);
    $stmt->bind_param("iid", $id_proveedor, $id_usuario, $total_compra);
    $stmt->execute();
    $id_compra = $mysqli->insert_id;

    // 2. Insertar Detalles y Aumentar Stock
    $sql_det = "INSERT INTO detalle_compras (id_compra, id_libro, cantidad, costo_unitario) VALUES (?, ?, ?, ?)";
    $sql_upd = "UPDATE existencias SET cantidad = cantidad + ? WHERE id_libro = ?";
    
    $stmt_det = $mysqli->prepare($sql_det);
    $stmt_upd = $mysqli->prepare($sql_upd);

    foreach ($items as $prod) {
        // Insertar detalle
        $stmt_det->bind_param("iiid", $id_compra, $prod['id_libro'], $prod['cantidad'], $prod['costo']);
        $stmt_det->execute();

        // Aumentar Stock
        $stmt_upd->bind_param("ii", $prod['cantidad'], $prod['id_libro']);
        $stmt_upd->execute();
    }

    $mysqli->commit();
    echo json_encode(['status' => 'ok', 'msg' => 'Compra registrada', 'folio' => $id_compra]);

} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['status' => 'error', 'msg' => 'Error en compra: ' . $e->getMessage()]);
}
?>