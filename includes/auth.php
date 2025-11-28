<?php
// ============================================================
// RESPONSABLE: Rol 5 (Sesiones)
// REQUERIMIENTO: "Guards: requiere sesión; y 403 si rol ≠ admin"
// ============================================================
session_start();

// 1. Verificar si existe $_SESSION['user']. Si no, redirigir a index.php.
// 2. Función helper para verificar rol: function require_admin() {...}
// 3. Implementar session_regenerate_id(true) al login.
?>