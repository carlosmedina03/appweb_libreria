<?php
// ============================================================
// RESPONSABLE: Rol 4 (CRUD) y Rol 2 (UI)
// REQUERIMIENTO: "CRUD productos con imagen BLOB" - Parte de Edición
// ============================================================
require_once 'config/db.php';
require_once 'includes/security_guard.php';

// Variables para la vista
$rol = $_SESSION['user']['rol'];
$mensaje = "";
$producto = null;

// 1. OBTENER ID DEL PRODUCTO DE LA URL
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_producto <= 0) {
    header("Location: productos.php");
    exit;
}

// 2. PROCESAR FORMULARIO DE ACTUALIZACIÓN (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar') {
    $codigo = $_POST['codigo'];
    $titulo = $_POST['titulo'];
    $precio = $_POST['precio'];

    $mysqli->begin_transaction();
    try {
        // Actualizar datos del libro
        $sql_update = "UPDATE libros SET codigo = ?, titulo = ?, precio_venta = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql_update);
        $stmt->bind_param("ssdi", $codigo, $titulo, $precio, $id_producto);
        $stmt->execute();

        // Si se subió una nueva imagen, reemplazar la anterior
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $tipo_mime = $_FILES['imagen']['type'];
            $imagen_binaria = file_get_contents($_FILES['imagen']['tmp_name']);

            // Borrar imagen anterior si existe
            $mysqli->query("DELETE FROM imagenes_libro WHERE id_libro = $id_producto");

            // Insertar la nueva
            $sql_img = "INSERT INTO imagenes_libro (id_libro, contenido, tipo_mime, es_principal) VALUES (?, ?, ?, 1)";
            $stmt_img = $mysqli->prepare($sql_img);
            $null = NULL;
            $stmt_img->bind_param("ibs", $id_producto, $null, $tipo_mime);
            $stmt_img->send_long_data(1, $imagen_binaria);
            $stmt_img->execute();
        }

        $mysqli->commit();
        $mensaje = "Producto actualizado correctamente.";

    } catch (Exception $e) {
        $mysqli->rollback();
        if ($mysqli->errno === 1062) {
            $mensaje = "Error: El código '$codigo' ya pertenece a otro producto.";
        } else {
            $mensaje = "Error al actualizar: " . $e->getMessage();
        }
    }
}

// 3. OBTENER DATOS ACTUALES DEL PRODUCTO PARA MOSTRAR EN EL FORMULARIO (GET)
$sql_producto = "SELECT * FROM libros WHERE id = $id_producto";
$resultado = $mysqli->query($sql_producto);
if ($resultado && $resultado->num_rows > 0) {
    $producto = $resultado->fetch_assoc();
} else {
    // Si el producto no existe, redirigir
    header("Location: productos.php");
    exit;
}
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Producto | <?php echo htmlspecialchars($producto['titulo']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/png" href="assets/img/logo-maria-de-letras_icon.svg">
  </head>

  <body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="assets/img/logo-maria-de-letras_v2.svg" alt="Logo">
        </div>
        <div class="navbar-menu">
            <a href="dashboard.php">Inicio</a>
            <a href="ventas.php">Punto de Venta</a>
            <a href="productos.php">Productos</a>
            <a href="compras.php">Compras</a>
            <a href="devoluciones.php">Devoluciones</a>
            <a href="usuarios.php">Usuarios</a>
            <a href="includes/logout.php" class="cerrar-sesion">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container main-content-large" style="margin-top: 20px;">
        <div class="flex-between mb-15">
            <h2>Editando Producto: "<?php echo htmlspecialchars($producto['titulo']); ?>"</h2>
            <a href="productos.php" class="btn" style="background-color: #555;">Volver al Listado</a>
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
            <form method="POST" action="editar_producto.php?id=<?php echo $id_producto; ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="editar">
                <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="codigo">Código (ISBN/SKU)</label><br>
                        <input type="text" id="codigo" name="codigo" required value="<?php echo htmlspecialchars($producto['codigo']); ?>" style="width: 100%; padding: 8px;">

                        <br><br>
                        <label for="titulo">Título del Libro</label><br>
                        <input type="text" id="titulo" name="titulo" required value="<?php echo htmlspecialchars($producto['titulo']); ?>" style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label for="precio">Precio de Venta</label><br>
                        <input type="number" id="precio" name="precio" required step="0.01" min="0" value="<?php echo htmlspecialchars($producto['precio_venta']); ?>" style="width: 100%; padding: 8px;">
                        
                        <br><br>
                        <label for="imagen">Cambiar Imagen (Opcional)</label><br>
                        <input type="file" id="imagen" name="imagen" accept="image/*" class="w-full" style="padding: 7px 0;">
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 20px; margin-top: 15px;">
                    <button type="submit" class="btn">Guardar Cambios</button>
                    <div>
                        <p style="margin: 0; font-size: 12px; color: #555;">Imagen actual:</p>
                        <img src="img.php?tipo=producto&id=<?php echo $id_producto; ?>" alt="Portada actual" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                    </div>
                </div>
            </form>
        </div>
    </div>
    
  </body>
</html>