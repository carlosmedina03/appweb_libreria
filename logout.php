<?php
// ============================================================
// RESPONSABLE: Rol 5 (Sesiones)
// REQUERIMIENTO: "Política de Acceso... y logout"
// ============================================================
session_start();
session_destroy();
header("Location: index.php");
exit;
?>