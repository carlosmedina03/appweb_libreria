<?php
// generar.php
$pass = "12345"; // La contraseña que quieres
$hash = password_hash($pass, PASSWORD_BCRYPT);

echo "<h1>Tu Hash para '12345' es:</h1>";
echo "<input type='text' value='$hash' style='width:500px; padding:10px; font-size:16px;'>";
echo "<br><br>Copia ese código largo y ponlo en tu base de datos.";
?>