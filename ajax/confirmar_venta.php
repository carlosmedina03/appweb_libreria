<?php
// ============================================================
// RESPONSABLE: Rol 4 (Back-End)
// REQUERIMIENTO: "Transacciones (INSERT encabezado/detalles + UPDATE existencias)"
// ============================================================
require_once '../config/db.php';

// 1. $mysqli->begin_transaction();
// 2. Validar stock suficiente (Query 3.1).
// 3. INSERT en ventas.
// 4. INSERT en detalle_ventas.
// 5. UPDATE existencias (restar).
// 6. Si todo bien: $mysqli->commit(); devolver ID venta.
// 7. Si falla: $mysqli->rollback();
?>