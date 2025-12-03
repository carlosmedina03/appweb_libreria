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
        <a href="compras.php">Compras</a>
        <a href="devoluciones.php">Devoluciones</a>
        <?php if ($rol === 'admin'): ?>
        <a href="usuarios.php">Usuario</a>
        <a href="productos.php">Productos</a>
        <a href="reportes/compras.php">Reportes compra</a>
        <a href="reportes/devoluciones.php">Reportes devoluciones</a>
        <a href="reportes/inventario.php">Reportes inventario</a>
        <a href="reportes/ventas_detalle.php">Reportes detalle</a>
        <a href="reportes/ventas_encabezado.php">Reportes encabezado</a>
        <?php endif; ?>
        <a href="index.php">Salir</a>
      </div>
    
    </div>

    <div class="container main-content">
        <div class="flex-between mb-15">
            <h2>Administración de Usuarios</h2>
            <button class="btn w-auto" onclick="alert('Abrir modal de creación (Simulado)')">+ Nuevo Usuario</button>
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
                    <tr>
                        <td>1</td>
                        <td>Administrador Principal</td>
                        <td>admin</td>
                        <td>Administrador</td>
                        <td>
                            <span class="text-green font-bold">Activo</span>
                        </td>
                        <td>
                            <a class="text-red" style="text-decoration: none; cursor: pointer;">Editar</a>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Juan Pérez</td>
                        <td>juanp</td>
                        <td>Operador</td>
                        <td>
                            <span class="text-green font-bold">Activo</span>
                        </td>
                        <td>
                            <a class="text-red" style="text-decoration: none; cursor: pointer;">Editar</a>
                            | <a class="text-gray" style="text-decoration: none; cursor: pointer;">Desactivar</a>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>María López</td>
                        <td>marial</td>
                        <td>Diseñador</td>
                        <td>
                            <span class="text-gray">Inactivo</span>
                        </td>
                        <td>
                            <a class="text-red" style="text-decoration: none; cursor: pointer;">Editar</a>
                            | <a class="text-gray" style="text-decoration: none; cursor: pointer;">Desactivar</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="js/main.js"></script>
  </body>
</html>