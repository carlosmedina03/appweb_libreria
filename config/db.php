<?php
// REQUERIMIENTO: "PHP + MySQLi"
// ==========================================

$host = 'localhost';
$user = 'root';     // Usuario por defecto de XAMPP
$pass = '';         // Contraseña por defecto de XAMPP (vacía)
$db   = 'libreria_db';

$mysqli = new mysqli($host, $user, $pass, $db);

// REQUERIMIENTO: "utf8mb4" (Para soportar acentos y caracteres especiales)
$mysqli->set_charset("utf8mb4");

if ($mysqli->connect_error) {
    die("Error crítico de conexión: " . $mysqli->connect_error);
}

echo "Conexión Exitosa"
?>