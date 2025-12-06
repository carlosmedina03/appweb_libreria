<?php
// ============================================================
// RESPONSABLE: Rol 4 (CRUD) y Rol 2 (UI)
// REQUERIMIENTO: "CRUD productos con imagen BLOB"
// ============================================================
require_once 'config/db.php';
require_once 'includes/security_guard.php';

// Variables para la vista
$rol = $_SESSION['user']['rol'];
$mensaje = "";

// PROCESAR FORMULARIO DE ALTA
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
        // 1. Insertar Libro
        $sql = "INSERT INTO libros (codigo, titulo, precio_venta, estatus) VALUES (?, ?, ?, 1)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssd", $codigo, $titulo, $precio);
        $stmt->execute();
        $id_libro = $mysqli->insert_id;

        // 2. Insertar Imagen (Si se subió)
        if ($imagen_binaria) {
            $sql_img = "INSERT INTO imagenes_libro (id_libro, contenido, tipo_mime, es_principal) VALUES (?, ?, ?, 1)";
            $stmt_img = $mysqli->prepare($sql_img);
            
            $null = NULL;
            $stmt_img->bind_param("ibs", $id_libro, $null, $tipo_mime);
            $stmt_img->send_long_data(1, $imagen_binaria);
            $stmt_img->execute();
        }
        
        // 3. Insertar Existencia Inicial en 0
        $mysqli->query("INSERT INTO existencias (id_libro, cantidad) VALUES ($id_libro, 0)");

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

// PROCESAR DESACTIVACIÓN DE PRODUCTO
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'desactivar') {
    $id_desactivar = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id_desactivar > 0) {
        try {
            $sql_desactivar = "UPDATE libros SET estatus = 0 WHERE id = ?";
            $stmt_desactivar = $mysqli->prepare($sql_desactivar);
            $stmt_desactivar->bind_param("i", $id_desactivar);
            $stmt_desactivar->execute();
            header("Location: productos.php"); // Redirige para limpiar la URL
            exit;
        } catch (Exception $e) {
            $mensaje = "Error al desactivar el producto: " . $e->getMessage();
        }
    }
}

// LISTAR PRODUCTOS
// Si necesitas mostrar stock, usa esto:
$sql_productos = "
    SELECT l.*, COALESCE(e.cantidad, 0) as cantidad 
    FROM libros l 
    LEFT JOIN existencias e ON l.id = e.id_libro 
    WHERE l.estatus = 1
    ORDER BY l.titulo
";
$productos = $mysqli->query($sql_productos);

// Si el stock ya funciona, déjalo como estaba:
// $productos = $mysqli->query("SELECT * FROM libros WHERE estatus = 1");
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
            <img src="assets/img/logo-maria-de-letras_v2.svg" alt="Logo">
        </div>

        <div class="navbar-menu">

            <div class="dropdown">
                <button class="dropbtn">Cajero ▾</button>
                <div class="dropdown-content">
                    <a href="dashboard.php">Inicio</a>
                    <a href="ventas.php">Punto de Venta</a>
                    <a href="devoluciones.php">Devoluciones</a>
                </div>
            </div>
            
            <?php if (isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'admin'): ?>
                <div class="dropdown">
                    <button class="dropbtn">Gestion ▾</button>
                    <div class="dropdown-content">
                        <a href="productos.php">Productos</a>
                        <a href="compras.php">Compras</a>
                        <a href="usuarios.php">Usuarios</a>
                    </div>
                </div>

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
                
            <?php endif; ?>
            
            <a href="includes/logout.php" class="cerrar-sesion">Cerrar Sesión</a>
        </div>

    </div>

    <div class="container main-content-large">
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
                        <label for="titulo">Título del Libro</label><br>
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
                        <th class="col-10">Precio Venta</th>
                        <th class="col-10">Stock</th>
                        <th class="col-25">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($productos && $productos->num_rows > 0): ?>
                        <?php while ($producto = $productos->fetch_assoc()): ?>
                            <tr>
                                <td><img src="img.php?tipo=producto&id=<?php echo $producto['id']; ?>" alt="Portada" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;"></td>
                                <td><?php echo htmlspecialchars($producto['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($producto['titulo']); ?></td>
                                <td>$<?php echo number_format($producto['precio_venta'], 2); ?></td>
                                <td><?php echo $producto['cantidad'] ?? 0; ?></td>
                                <td>
                                    <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="text-blue">Editar</a> | 
                                    <a href="productos.php?action=desactivar&id=<?php echo $producto['id']; ?>" onclick="return confirm('¿Estás seguro de que quieres desactivar este producto? No aparecerá en ventas.');" style="color: red;">Desactivar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #777;">
                                No hay productos registrados
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
  </body>
</html>