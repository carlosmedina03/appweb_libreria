<?php
require_once 'includes/security_guard.php'; // Guard: Solo Admins
require_once 'config/db.php';

// 1. DEFINIR ROL PARA EL NAVBAR
$rol = $_SESSION['user']['rol'];

$mensaje = "";
$error = "";

// LÓGICA DE BACKEND (CRUD)

// Procesar acciones (Crear, Editar, Desactivar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    // Sanitización básica
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = trim($_POST['nombre_completo'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol_input = $_POST['rol'] ?? 'operador';

    try {
        if ($action === 'crear') {
            if (!empty($nombre) && !empty($username) && !empty($password)) {
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (nombre_completo, username, password, rol, activo) VALUES (?, ?, ?, ?, 1)";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ssss", $nombre, $username, $pass_hash, $rol_input);
                $stmt->execute();
                $mensaje = "Usuario creado correctamente.";
            } else {
                $error = "Todos los campos son obligatorios.";
            }
        } 
        // Nota: La lógica de editar se implementaría recibiendo el ID, aquí lo simplificamos para el alta.
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
    // Evitar que el admin se desactive a sí mismo (Protección lógica)
    if ($id_baja !== $_SESSION['user']['id']) { 
        $sql_baja = "UPDATE usuarios SET activo = 0 WHERE id = ?";
        $stmt_baja = $mysqli->prepare($sql_baja);
        $stmt_baja->bind_param("i", $id_baja);
        $stmt_baja->execute();
        header("Location: usuarios.php"); // Limpiar URL
        exit;
    } else {
        $error = "No puedes desactivar tu propia cuenta.";
    }
}

// Obtener listado REAL de usuarios para mostrar en la tabla
$resultado = $mysqli->query("SELECT id, nombre_completo, username, rol, activo FROM usuarios ORDER BY id ASC");
$usuarios_db = [];
while ($row = $resultado->fetch_assoc()) {
    $usuarios_db[] = $row;
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
    <style>
        /* Estilos simples para el formulario que agregué */
        .form-container {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
    </style>
  </head>

  <body>
<div class="navbar">
        
        <div class="navbar-logo">
            <img src="assets/img/logo-maria-de-letras_v2.svg" alt="Logo">
        </div>

        <div class="navbar-menu">
            <a href="dashboard.php">Inicio</a>
            <a href="ventas.php">Punto de Venta</a>
            
            <?php if (isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'admin'): ?>
                <a href="productos.php">Productos</a>
                <a href="compras.php">Compras</a>
                <a href="devoluciones.php">Devoluciones</a>
                <a href="usuarios.php">Usuarios</a>

                <div class="dropdown">
                    <button class="dropbtn">Reportes ▾</button>
                    <div class="dropdown-content">
                        <a href="reportes/compras.php">Reportes Compra</a>
                        <a href="reportes/devoluciones.php">Reportes Devoluciones</a>
                        <a href="reportes/inventario.php">Reportes Inventario</a>
                        <a href="reportes/ventas_detalle.php">Reportes Detalle</a>
                        <a href="reportes/ventas_encabezado.php">Reportes Encabezado</a>
                    </div>
                </div>

            <?php else: ?>
                <a href="devoluciones.php">Devoluciones</a>
            <?php endif; ?>
            
            <a href="includes/logout.php" class="cerrar-sesion">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container main-content">
        <div class="flex-between mb-15">
            <h2>Administración de Usuarios</h2>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center;">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <h3>+ Nuevo Usuario</h3>
            <form method="POST" action="usuarios.php">
                <input type="hidden" name="action" value="crear">
                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre_completo" required style="width: 100%; padding: 8px;" placeholder="Ej: Juan Pérez">
                    </div>
                    <div>
                        <label>Rol</label>
                        <select name="rol" style="width: 100%; padding: 8px;">
                            <option value="operador">Operador (Vendedor)</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div>
                        <label>Usuario (Login)</label>
                        <input type="text" name="username" required style="width: 100%; padding: 8px;" autocomplete="off">
                    </div>
                    <div>
                        <label>Contraseña</label>
                        <input type="password" name="password" required style="width: 100%; padding: 8px;" autocomplete="new-password">
                    </div>
                </div>
                <button type="submit" class="btn" style="margin-top: 15px;">Crear Usuario</button>
            </form>
        </div>

        <div class="card">
            <h3>Listado de Empleados</h3>
            <table>
                <thead>
                    <tr>
                        <th class="col-5">ID</th>
                        <th class="col-25">Nombre Completo</th>
                        <th class="col-20">Usuario</th>
                        <th class="col-15">Rol</th>
                        <th class="col-15">Estatus</th>
                        <th class="col-20">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios_db)): ?>
                        <tr><td colspan="6" style="text-align:center;">No hay usuarios registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($usuarios_db as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo htmlspecialchars($u['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo ucfirst($u['rol']); ?></td>
                            <td>
                                <?php if ($u['activo'] == 1): ?>
                                    <span style="color: green; font-weight: bold;">Activo</span>
                                <?php else: ?>
                                    <span style="color: gray;">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                                    <?php if ($u['activo'] == 1): ?>
                                        <a href="usuarios.php?action=baja&id=<?php echo $u['id']; ?>" 
                                           onclick="return confirm('¿Seguro que deseas desactivar este usuario?');"
                                           style="color: red; text-decoration: none;">
                                           Desactivar
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #999;">Baja</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="font-size: 0.8em; color: #555;">(Tú)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="js/main.js"></script>
  </body>
</html>