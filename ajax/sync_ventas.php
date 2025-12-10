<?php
// ============================================================
// RESPONSABLE: Rol 7 (POS Offline-First (PWA) con sincronización diferida)
// REQUERIMIENTO: "Sincronización Diferida de Ventas"
// DESCRIPCIÓN: Recibe JSON del navegador, inserta ventas, detalles y descuenta stock
// ============================================================

// 1. CONFIGURACIÓN DE ENTORNO
// Desactivamos errores visuales (HTML) para no corromper la respuesta JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
session_start();

// 2. VALIDACIÓN DE ARCHIVOS CRÍTICOS
if (!file_exists('../config/db.php')) {
    echo json_encode(['status' => 'error', 'msg' => 'Falta config/db.php']);
    exit;
}
require_once '../config/db.php';

// 3. DETECCIÓN ROBUSTA DE CONEXIÓN
// Garantiza que el script funcione sin importar cómo nombraron la variable en db.php
if (isset($mysqli)) { $db = $mysqli; } 
elseif (isset($conexion)) { $db = $conexion; } 
elseif (isset($conn)) { $db = $conn; } 
else {
    echo json_encode(['status' => 'error', 'msg' => 'Error: Variable de conexión no encontrada']);
    exit;
}

if ($db->connect_error) {
    echo json_encode(['status' => 'error', 'msg' => 'Error DB: ' . $db->connect_error]);
    exit;
}

// 4. RECEPCIÓN DE DATOS (PAYLOAD)
$input = file_get_contents("php://input");
$ventasPendientes = json_decode($input, true);

if (!$ventasPendientes) {
    echo json_encode(['status' => 'error', 'msg' => 'No se recibieron datos']);
    exit;
}

// Usamos el ID del usuario en sesión, o 1 (Admin) por defecto si la sesión caducó
$id_usuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 1;

try {
    // 5. INICIO DE TRANSACCIÓN (Atomicidad)
    // O se guarda todo o no se guarda nada
    $db->begin_transaction();

    // Preparación de consultas SQL para optimizar rendimiento en bucle
    
    // A. Insertar Venta (Cabecera) - Incluye la columna 'folio' para devoluciones
    $sql_venta = "INSERT INTO ventas (id_usuario, subtotal, iva, total, fecha_hora, folio) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_venta = $db->prepare($sql_venta);
    
    // B. Insertar Detalles (Productos vendidos)
    $sql_detalle = "INSERT INTO detalle_ventas (id_venta, id_libro, cantidad, precio_unitario, importe) VALUES (?, ?, ?, ?, ?)";
    $stmt_det = $db->prepare($sql_detalle);
    
    // C. Actualizar Stock (Restar inventario)
    $sql_stock = "UPDATE existencias SET cantidad = cantidad - ? WHERE id_libro = ?";
    $stmt_stk = $db->prepare($sql_stock);

    if (!$stmt_venta || !$stmt_det || !$stmt_stk) throw new Exception("Error preparando SQL: " . $db->error);

    // 6. PROCESAMIENTO DE CADA VENTA RECIBIDA
    foreach ($ventasPendientes as $venta) {
        $subtotal = 0;
        // Recalculamos totales en el servidor por seguridad
        foreach ($venta['productos'] as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }
        $iva = $subtotal * 0.16;
        $total = $subtotal + $iva;
        
        // Usamos la fecha en la que ocurrió la venta offline
        $fecha = isset($venta['fecha_local']) ? date('Y-m-d H:i:s', strtotime($venta['fecha_local'])) : date('Y-m-d H:i:s');
        
        // Recuperamos el folio generado en JS (ej: OFF-1723...)
        $folio = isset($venta['folio']) ? $venta['folio'] : 'SYNC-' . time();

        // Ejecutamos inserción de venta
        // Tipos: i(int), d(double), d, d, s(string), s
        $stmt_venta->bind_param("idddss", $id_usuario, $subtotal, $iva, $total, $fecha, $folio);
        
        if (!$stmt_venta->execute()) throw new Exception("Error al guardar venta: " . $stmt_venta->error);
        $id_nuevo = $db->insert_id; // Obtenemos el ID numérico generado por MySQL

        // 7. PROCESAMIENTO DE PRODUCTOS DE LA VENTA
        foreach ($venta['productos'] as $prod) {
            $importe = $prod['precio'] * $prod['cantidad'];
            
            // Guardar detalle
            $stmt_det->bind_param("iiidd", $id_nuevo, $prod['id'], $prod['cantidad'], $prod['precio'], $importe);
            $stmt_det->execute();

            // Descontar stock
            $stmt_stk->bind_param("ii", $prod['cantidad'], $prod['id']);
            $stmt_stk->execute();
        }
    }

    // 8. CONFIRMACIÓN
    $db->commit();
    echo json_encode(['status' => 'success', 'msg' => 'Sincronización correcta']);

} catch (Exception $e) {
    // Si algo falla, revertimos todos los cambios
    $db->rollback();
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>