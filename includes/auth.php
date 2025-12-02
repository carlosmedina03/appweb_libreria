<?php
session_start();
// auth.php

// 1. Conexión a Base de Datos
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

$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $user_input = $_POST['user'];
    $pass_input = $_POST['pass'];

    // 2. Buscar usuario (Usamos los nombres de columna correctos según tu schema.sql)
    $sql = "SELECT id, nombre_completo, username, password, rol FROM usuarios WHERE username = :user AND activo = 1 LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user' => $user_input]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    

    if ($usuario) {
        
        // 3. Verificar Contraseña (CUMPLIENDO REQUERIMIENTO DE HASH)
        // Nota: Para crear el primer usuario en BD, deberás insertar el hash, no el texto plano.
        if (password_verify($pass_input, $usuario['password'])) {
            
            // 4. Seguridad de Sesión (Requerimiento)
            session_regenerate_id(true);

            // 5. Guardar estructura que espera el Dashboard
            $_SESSION['user'] = [
                'id'       => $usuario['id'],
                'username' => $usuario['username'],
                'nombre'   => $usuario['nombre_completo'],
                'rol'      => $usuario['rol'] 
            ];

            // 6. Redirección
            header("Location: ../dashboard.php");
            exit();

        } else {
            $mensaje_error = "Contraseña incorrecta.";
        }
    } else {
        $mensaje_error = "El usuario no existe o está inactivo.";
    }
}
?>