<?php
// productos.php

// 1. INCLUIR LA SEGURIDAD (Rol 5)
// Este archivo valida:
// a) Que haya sesión iniciada.
// b) Que el rol sea 'admin'. Si es 'operador', lo bloquea con un 403.
require_once 'includes/security_guard.php';

// 2. Variables para la vista
$rol = $_SESSION['user']['rol']; // Necesario para el Navbar
$mensaje = "";

require_once 'config/db.php';

// PROCESAR FORMULARIO DE ALTA (Lógica Backend)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear') {
    $codigo = $_POST['codigo'];
    $titulo = $_POST['titulo'];
    $precio = $_POST['precio'];
    
    // Manejo de IMAGEN BLOB
    $imagen_binaria = null;
    $tipo_mime = 'image/jpeg';
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $tipo_mime = $_FILES['imagen']['type'];
        $imagen_binaria = file_get_contents($_FILES['imagen']['tmp_name']);
    }

    $mysqli->begin_transaction();
    try {
        // 1. Insertar item
        // Asegúrate de que tu tabla se llame 'items' o 'libros' según tu BD final.
        // Aquí uso 'items' que es lo estándar del proyecto, ajusta si usaste 'libros'.
        $sql = "INSERT INTO items (codigo, nombre, precio, activo) VALUES (?, ?, ?, 1)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssd", $codigo, $titulo, $precio);
        $stmt->execute();
        $id_item = $mysqli->insert_id;

        // 2. Insertar Imagen
        if ($imagen_binaria) {
            // Nota: Verifica si tienes tabla 'imagenes_item' o si guardas directo en 'items'.
            // Asumiendo tabla separada por tu código anterior:
            $sql_img = "INSERT INTO imagenes_item (item_id, contenido, tipo_mime) VALUES (?, ?, ?)";
            $stmt_img = $mysqli->prepare($sql_img);
            $null = NULL;
            $stmt_img->bind_param("ibs", $id_item, $null, $tipo_mime);
            $stmt_img->send_long_data(1, $imagen_binaria);
            $stmt_img->execute();
        }
        
        // 3. Insertar Existencia Inicial en 0
        $mysqli->query("INSERT INTO existencias (item_id, cantidad) VALUES ($id_item, 0)");

        $mysqli->commit();
        $mensaje = "Producto creado correctamente.";
    } catch (Exception $e) {
        $mysqli->rollback();
        if ($mysqli->errno === 1062) {
            $mensaje = "Error: El código '$codigo' ya existe.";
        } else {
            $mensaje = "Error: " . $e->getMessage();
        }
    }
}

// LISTAR PRODUCTOS (Para mostrar en la tabla real más adelante)
// $productos = $mysqli->query("SELECT * FROM items WHERE activo = 1");
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Productos</title>
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
        
        <?php if ($rol === 'admin'): ?>
            <a href="compras.php">Compras</a>
            <a href="productos.php">Productos</a>
            <a href="usuarios.php">Usuarios</a>
            <a href="devoluciones.php">Devoluciones</a>
            
            <a href="reportes/inventario.php">Reportes</a>
        <?php endif; ?>

        <a href="includes/logout.php" style="background: #333; color: white;">Salir</a>
      </div>
    </div>

    <div class="container main-content-large" style="margin-top: 20px;">
        <div class="flex-between mb-15">
            <h2>Gestión de Inventario (Productos)</h2>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div style="padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; 
                background-color: <?php echo strpos($mensaje, 'Error') !== false ? '#f8d7da' : '#d4edda'; ?>;
                color: <?php echo strpos($mensaje, 'Error') !== false ? '#721c24' : '#155724'; ?>;
                border: 1px solid <?php echo strpos($mensaje, 'Error') !== false ? '#f5c6cb' : '#c3e6cb'; ?>;">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <div class="card mb-30">
            <h3>Alta de Nuevo Producto</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="crear">
                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="codigo">Código (ISBN/SKU)</label><br>
                        <input type="text" id="codigo" name="codigo" required placeholder="Ej: 978-0743273565" style="width: 100%; padding: 8px;">

                        <br><br>
                        <label for="titulo">Título / Nombre</label><br>
                        <input type="text" id="titulo" name="titulo" required placeholder="Ej: Cien Años de Soledad" style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label for="precio">Precio de Venta</label><br>
                        <input type="number" id="precio" name="precio" required step="0.01" min="0" placeholder="Ej: 250.00" style="width: 100%; padding: 8px;">
                        
                        <br><br>
                        <label for="imagen">Imagen (Máx. 2MB)</label><br>
                        <input type="file" id="imagen" name="imagen" accept="image/*" class="w-full" style="padding: 7px 0;">
                    </div>
                </div>
                <button type="submit" class="btn" style="margin-top: 15px;">Guardar Producto</button>
            </form>
        </div>

        <div class="card">
            <h3>Listado Actual</h3>
            <table>
                <thead>
                    <tr>
                        <th class="col-5">Img.</th>
                        <th class="col-15">Código</th>
                        <th class="col-35">Título</th>
                        <th class="col-10">Precio</th>
                        <th class="col-10">Stock</th>
                        <th class="col-25">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #777;">
                            (Aquí se listarán los productos desde la BD cuando el backend termine la consulta)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    </body>
</html>