<?php
// ============================================================
// RESPONSABLE: Rol 4 (Lógica Session)
// REQUERIMIENTO: "Escaneo que agrega/incrementa en carrito en sesión"
// ============================================================
session_start();
// 1. Recibir ID producto y cantidad.
// 2. Verificar si ya existe en $_SESSION['carrito'].
// 3. Si existe: incrementar cantidad.
// 4. Si no: agregar nuevo item al array.
// 5. Devolver JSON 'ok'.
?>