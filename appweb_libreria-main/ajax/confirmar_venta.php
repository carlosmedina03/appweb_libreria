<?php
// REQUERIMIENTO: "Transacciones (INSERT encabezado/detalles + UPDATE existencias), COMMIT/ROLLBACK"
// REQUERIMIENTO: "Inventario con reglas claras... Venta resta"
// ==========================================
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (empty($_SESSION['carrito']) || !isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Carrito vacío o sesión caducada']);
    exit;
}

$id_usuario = $_SESSION['user']['id'];
$carrito = $_SESSION['carrito'];

// Calcular totales (Backend siempre recalcula, no confiar en el front)
$subtotal = 0;
foreach ($carrito as $item) {
    $subtotal += $item['precio'] * $item['cantidad'];
}
$iva = $subtotal * 0.16;
$total = $subtotal + $iva;

// INICIO DE TRANSACCIÓN
$mysqli->begin_transaction();

try {
    // 1. Insertar Encabezado Venta
    $sql_venta = "INSERT INTO ventas (id_usuario, subtotal, iva, total, fecha_hora) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $mysqli->prepare($sql_venta);
    $stmt->bind_param("iddd", $id_usuario, $subtotal, $iva, $total);
    $stmt->execute();
    $id_venta = $mysqli->insert_id; // FOLIO GENERADO

    // 2. Procesar Detalles y Stock
    $sql_detalle = "INSERT INTO detalle_ventas (id_venta, id_libro, cantidad, precio_unitario, importe) VALUES (?, ?, ?, ?, ?)";
    $sql_stock   = "UPDATE existencias SET cantidad = cantidad - ? WHERE id_libro = ? AND cantidad >= ?"; // Validar no negativos
    
    $stmt_det = $mysqli->prepare($sql_detalle);
    $stmt_stk = $mysqli->prepare($sql_stock);

    foreach ($carrito as $prod) {
        // Insertar detalle
        $stmt_det->bind_param("iiidd", $id_venta, $prod['id'], $prod['cantidad'], $prod['precio'], $prod['importe']);
        $stmt_det->execute();

        // Descontar Stock y validar que alcance
        $stmt_stk->bind_param("iii", $prod['cantidad'], $prod['id'], $prod['cantidad']);
        $stmt_stk->execute();

        if ($mysqli->affected_rows === 0) {
            // Si affected_rows es 0, es porque el WHERE cantidad >= ? falló (Stock insuficiente)
            throw new Exception("Stock insuficiente para el producto: " . $prod['titulo']);
        }
    }

    // 3. Confirmar todo
    $mysqli->commit();
    
    // Limpiar carrito
    unset($_SESSION['carrito']);

    echo json_encode(['status' => 'ok', 'folio' => $id_venta]);

} catch (Exception $e) {
    // 4. Algo falló: Deshacer todo
    $mysqli->rollback();
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>