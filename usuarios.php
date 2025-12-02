<?php
// ============================================================
// RESPONSABLE: Rol 5 (Admin de usuarios) y Rol 2 (UI)d
// REQUERIMIENTO: "Admin gestiona... usuarios"
// ============================================================
require_once 'includes/security_guard.php'; // Guard: Solo Admins
require_once 'config/db.php';

$mensaje = "";
$error = "";

// LÓGICA DE BACKEND (CRUD)

// Procesar acciones (Crear, Editar, Desactivar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = $_POST['nombre_completo'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'operador';

    try {
        if ($action === 'crear') {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre_completo, username, password, rol) VALUES (?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssss", $nombre, $username, $pass_hash, $rol);
            $stmt->execute();
            $mensaje = "Usuario creado correctamente.";
        } elseif ($action === 'editar' && $id > 0) {
            if (!empty($password)) {
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombre_completo = ?, username = ?, password = ?, rol = ? WHERE id = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ssssi", $nombre, $username, $pass_hash, $rol, $id);
            } else {
                $sql = "UPDATE usuarios SET nombre_completo = ?, username = ?, rol = ? WHERE id = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("sssi", $nombre, $username, $rol, $id);
            }
            $stmt->execute();
            $mensaje = "Usuario actualizado correctamente.";
        }
    } catch (Exception $e) {
        if ($mysqli->errno === 1062) {
            $error = "Error: El nombre de usuario '$username' ya existe.";
        } else {
            $error = "Error al procesar la solicitud: " . $e->getMessage();
        }
    }
}

// Acción de desactivar por GET
if (isset($_GET['action']) && $_GET['action'] === 'baja' && isset($_GET['id'])) {
    $id_baja = intval($_GET['id']);
    if ($id_baja !== $_SESSION['user']['id']) { // Evitar que el admin se desactive a sí mismo
        $sql_baja = "UPDATE usuarios SET activo = 0 WHERE id = ?";
        $stmt_baja = $mysqli->prepare($sql_baja);
        $stmt_baja->bind_param("i", $id_baja);
        $stmt_baja->execute();
        header("Location: usuarios.php"); // Redirigir para limpiar la URL
        exit;
    }
}

// Obtener listado de usuarios para mostrar en la tabla
$resultado = $mysqli->query("SELECT id, nombre_completo, username, rol, activo FROM usuarios");
$usuarios = [];
while ($row = $resultado->fetch_assoc()) {
    $usuarios[] = $row;
}
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
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo ucfirst($user['rol']); ?></td>
                            <td>
                                <?php if ($user['activo'] == 1): ?>
                                    <span style="color: #0e7a0e; font-weight: bold;">Activo</span>
                                <?php else: ?>
                                    <span style="color: #888;">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a id="<?php echo $user['id']; ?>" style="color:#C82B1D; text-decoration: none; cursor: pointer;">Editar</a>
                                <?php if ($user['id'] !== $_SESSION['user']['id']): // No mostrar "Desactivar" para el usuario actual ?>
                                    | <a href="usuarios.php?action=baja&id=<?php echo $user['id']; ?>" onclick="return confirm('¿Está seguro de desactivar a este usuario?')" style="color:#555; text-decoration: none;">Desactivar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center;">No hay usuarios registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="js/main.js"></script>
  </body>
</html>