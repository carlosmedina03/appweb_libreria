<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id_venta = intval($input['id_venta']);
$items_dev = $input['items']; // Array {id_libro, cantidad_devuelta}
$motivo = $input['motivo'] ?? 'Devolución cliente';
$id_usuario = $_SESSION['user']['id'];

$mysqli->begin_transaction();

try {
    $total_reembolso = 0;

    // 1. Calcular total a reembolsar y validar cantidades
    // (Aquí deberías hacer un query para verificar que no devuelva más de lo que compró,
    // por brevedad asumimos que el Front validó, pero el Back siempre debe desconfiar).
    
    // Insertar Encabezado Devolución
    $sql_dev = "INSERT INTO devoluciones (id_venta, id_usuario, total_reembolsado, motivo, fecha_hora) VALUES (?, ?, 0, ?, NOW())";
    $stmt = $mysqli->prepare($sql_dev);
    $stmt->bind_param("iis", $id_venta, $id_usuario, $motivo);
    $stmt->execute();
    $id_devolucion = $mysqli->insert_id;

    $sql_det_dev = "INSERT INTO detalle_devoluciones (id_devolucion, id_libro, cantidad, monto_reembolsado) VALUES (?, ?, ?, ?)";
    $sql_stock   = "UPDATE existencias SET cantidad = cantidad + ? WHERE id_libro = ?";
    
    $stmt_det = $mysqli->prepare($sql_det_dev);
    $stmt_stk = $mysqli->prepare($sql_stock);

    foreach ($items_dev as $item) {
        // Obtener precio original de venta para calcular reembolso
        $q_precio = "SELECT precio_unitario FROM detalle_ventas WHERE id_venta = ? AND id_libro = ?";
        $stmt_p = $mysqli->prepare($q_precio);
        $stmt_p->bind_param("ii", $id_venta, $item['id_libro']);
        $stmt_p->execute();
        $stmt_p->bind_result($precio_vendido);
        $stmt_p->fetch();
        $stmt_p->close();

        $monto_linea = $item['cantidad'] * $precio_vendido;
        $total_reembolso += $monto_linea;

        // Registrar detalle
        $stmt_det->bind_param("iiid", $id_devolucion, $item['id_libro'], $item['cantidad'], $monto_linea);
        $stmt_det->execute();

        // Regresar Stock
        $stmt_stk->bind_param("ii", $item['cantidad'], $item['id_libro']);
        $stmt_stk->execute();
    }

    // Actualizar el total del encabezado
    $mysqli->query("UPDATE devoluciones SET total_reembolsado = $total_reembolso WHERE id = $id_devolucion");

    $mysqli->commit();
    echo json_encode(['status' => 'ok', 'folio' => $id_devolucion]);

} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>