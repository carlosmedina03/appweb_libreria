<?php
// ============================================================
// RESPONSABLE: Rol 4 (Back-End Transacciones)
// REQUERIMIENTO: "Transacción... se incrementa el stock de cada producto"
// ============================================================
require_once '../config/db.php';

// 1. $mysqli->begin_transaction();
// 2. INSERT compras (encabezado).
// 3. INSERT detalle_compras (bucle).
// 4. UPDATE existencias SET cantidad = cantidad + ? (INCREMENTAR).
// 5. $mysqli->commit();
?>