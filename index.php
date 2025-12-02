<?php
// ============================================================
// RESPONSABLE: Rol 5 (Autenticación) y Rol 2 (UX-UI)
// REQUERIMIENTO: "El acceso exige inicio de sesión (usuario/contraseña)."
// ============================================================
// TODO:
// 1. Formulario HTML con inputs para 'username' y 'password'.
// 2. Enviar datos via POST.
// 3. Validar con password_verify (ver /database/seed.sql).
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
    <div class="container" style="max-width: 400px; margin-top: 80px;">
      <div class="logo" style="text-align: center;">
        <img src="assets/img/logo-maria-de-letras_v2.svg" alt="Logo de María de Letras" style="margin-bottom: 5px;">
        <h2 >Iniciar Sesión</h2>
      </div>

      <?php if (!empty($error)): ?>
        <div class="error-message" style="margin-bottom: 15px; padding: 10px; border: 1px solid #C82B1D; background-color: #fdd; color: #C82B1D; border-radius: 4px;">
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
            placeholder="Ingresa tu usuario">
        </div>

        <div style="margin-bottom: 15px;">
          <label for="pass">Contraseña</label><br>
          <input 
            type="password" 
            id="pass" 
            name="pass" 
            required 
            autocomplete="current-password"
            placeholder="Ingresa tu contraseña">
        </div>

        <button type="submit" class="btn" style="width: 100%;">
          Ingresar
        </button>
      </form>
    </div>
  </body>
</html>