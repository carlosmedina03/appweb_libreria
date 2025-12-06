<?php
// index.php
session_start(); // NECESARIO: Iniciar sesión para recibir mensajes de error de auth.php

// Verificar si hay algún mensaje de error guardado (ej: "Contraseña incorrecta")
$error = '';
if (isset($_SESSION['error_mensaje'])) {
    $error = $_SESSION['error_mensaje'];
    unset($_SESSION['error_mensaje']); // Borramos el mensaje para que no salga al recargar
}
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Iniciar sesión</title>
    <link rel="stylesheet" href="css/styles.css"> 
    <link rel="icon" type="image/png" href="assets/img/logo-maria-de-letras_icon.svg">
  </head>

  <body>
    <div class="container-login">
      <div class="logo">
        <img src="assets/img/logo-maria-de-letras_v2.svg" alt="Logo de María de Letras">
        <h2>Iniciar Sesión</h2>
      </div>

      <?php if (!empty($error)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 5px; text-align: center;">
            <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="includes/auth.php" novalidate>
        
        <div style="margin-bottom: 15px;">
          <label for="user">Usuario</label><br>
          <input type="text" 
            id="user" 
            name="user" 
            required 
            autocomplete="username"
            placeholder="Ingresa tu usuario"
            style="width: 100%; padding: 8px;"> </div>

        <div style="margin-bottom: 15px;">
          <label for="pass">Contraseña</label><br>
          <input 
            type="password" 
            id="pass" 
            name="pass" 
            required 
            autocomplete="current-password"
            placeholder="Ingresa tu contraseña"
            style="width: 100%; padding: 8px;">
        </div>

        <button type="submit" class="btn-login">
          Ingresar
        </button>
      </form>
    </div>
  </body>
</html>