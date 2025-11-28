<?php
// ============================================================
// RESPONSABLE: Rol 4 (Back-End)
// REQUERIMIENTO: "PHP + MySQLi"
// ============================================================
$host = 'localhost';
$user = 'root';
$pass = ''; // Ajustar según XAMPP
$db   = 'libreria_db';

$mysqli = new mysqli($host, $user, $pass, $db);
$mysqli->set_charset("utf8mb4");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}
?>