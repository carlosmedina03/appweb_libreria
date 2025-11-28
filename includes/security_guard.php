<?php
// security_guard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Validar autenticación
if (!isset($_SESSION['user'])) {
    header('Location: auth.php'); // Corregido para apuntar al login
    exit;
}

// 2. Validar ROL ADMIN
if ($_SESSION['user']['rol'] !== 'admin') {
    http_response_code(403);
    echo "<div style='text-align:center; margin-top:50px;'>";
    echo "<h1 style='color:red;'>403 Acceso Prohibido</h1>";
    echo "<p>Tu rol de <strong>Operador</strong> no tiene permisos para ver esta sección.</p>";
    echo "<a href='../dashboard.php'>Volver al Dashboard</a>";
    echo "</div>";
    exit; 
}
?>