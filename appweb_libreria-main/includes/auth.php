<?php
session_start();
// includes/auth.php

$host = 'localhost';
$dbname = 'libreria_db';    
$username_db = 'root';
$password_db = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = trim($_POST['user']);
    $pass_input = trim($_POST['pass']);

    // Buscar usuario
    $sql = "SELECT id, nombre_completo, username, password, rol FROM usuarios WHERE username = :user AND activo = 1 LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user' => $user_input]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Verificar Contraseña
        if (password_verify($pass_input, $usuario['password'])) {
            
            // Seguridad de Sesión
            session_regenerate_id(true);

            // Guardar datos de sesión
            $_SESSION['user'] = [
                'id'       => $usuario['id'],
                'username' => $usuario['username'],
                'nombre'   => $usuario['nombre_completo'],
                'rol'      => $usuario['rol'] 
            ];

            // CORREGIDO: Ahora sí vamos al Dashboard
            header("Location: ../dashboard.php");
            exit();

        } else {
            $_SESSION['error_mensaje'] = "Contraseña incorrecta.";
            header("Location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['error_mensaje'] = "Credenciales incorrectas.";
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>