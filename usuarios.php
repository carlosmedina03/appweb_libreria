<?php
// ============================================================
// RESPONSABLE: Rol 5 (Admin de usuarios) y Rol 2 (UI)d
// REQUERIMIENTO: "Admin gestiona... usuarios"
// ============================================================
require_once 'includes/auth.php';
// Guard: require_admin();

// TODO:
// 1. CRUD de usuarios (Crear, Editar, Baja lógica/Activo).
// 2. Al crear: usar password_hash($_POST['pass'], PASSWORD_DEFAULT).
// 3. Asignar rol (admin/operador).
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Usuarios</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/png" href="assets/img/logo-maria-de-letras_icon.svg">
  </head>

  <body>
    <div class="navbar">
      <div class="navbar-logo">
        <img src="assets/img/logo-maria-de-letras_v2.svg" alt="Logo de María de Letras">
      </div>
      <div class="navbar-menu">
        <a href="ventas.php">Punto de ventas</a>
        <a href="productos.php">Productos</a>
        <a href="compras.php">Compras</a>
        <a href="devoluciones.php">Devoluciones</a>
        <a href="usuarios.php">Usuario</a>

        <a href="reportes/compras.php">Reportes compra</a>
        <a href="reportes/devoluciones.php">Reportes devoluciones</a>
        <a href="reportes/inventario.php">Reportes inventario</a>
        <a href="reportes/ventas_detalle.php">Reportes detalle</a>
        <a href="reportes/ventas_encabezado.php">Reportes encabezado</a>

        <a href="includes/logout.php">Salir</a>
      </div>
    
    </div>

    <div class="container" style="max-width: 1000px; margin-top: 80px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2>Administración de Usuarios</h2>
            <button class="btn" style="width: auto;">+ Nuevo Usuario</button>
        </div>

        
        <div class="card">
            <h3>Listado de Empleados</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 25%;">Nombre Completo</th>
                        <th style="width: 20%;">Usuario</th>
                        <th style="width: 15%;">Rol</th>
                        <th style="width: 15%;">Estatus</th>
                        <th style="width: 20%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($user['usuario']); ?></td>
                        <td><?php echo ucfirst($user['rol']); ?></td>
                        <td>
                            <?php if ($user['estatus'] == 1): ?>
                                <span style="color: #0e7a0e; font-weight: bold;">Activo</span>
                            <?php else: ?>
                                <span style="color: #888;">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a id=<?php echo $user['id']; ?>" style="color:#C82B1D; text-decoration: none;">Editar</a>
                            | <a href="usuarios.php?action=baja&id=<?php echo $user['id']; ?>" onclick="return confirm('¿Desactivar?')" style="color:#555; text-decoration: none;">Desactivar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="js/main.js"></script>
  </body>
</html>