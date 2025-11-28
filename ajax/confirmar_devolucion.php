<?php
// ============================================================
// RESPONSABLE: Rol 4 (Back-End Transacciones)
// REQUERIMIENTO: "Devolución de venta vuelve a sumar... sin exceder lo vendido"
// ============================================================
require_once '../config/db.php';

// 1. Validar que la cantidad a devolver <= cantidad vendida - devuelta previamente.
// 2. $mysqli->begin_transaction();
// 3. INSERT devoluciones.
// 4. INSERT detalle_devoluciones.
// 5. UPDATE existencias SET cantidad = cantidad + ? (RESTITUIR STOCK).
// 6. $mysqli->commit();
?>