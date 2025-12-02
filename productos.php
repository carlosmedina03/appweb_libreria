<?php
// ============================================================
// RESPONSABLE: Rol 4 (CRUD) y Rol 2 (UI)d|
// REQUERIMIENTO: "CRUD productos con imagen BLOB"
// ============================================================
require_once 'config/db.php';
//require_once 'includes/auth.php';

// Guard: require_admin(); // Descomentar cuando tengas Auth lista

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
        // LEER EL ARCHIVO TEMPORAL Y CONVERTIRLO A BINARIO
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
            // CORRECCIÓN: El SQL tiene 3 placeholders (?, ?, ?) y un 1 fijo.
            $sql_img = "INSERT INTO imagenes_libro (id_libro, contenido, tipo_mime, es_principal) VALUES (?, ?, ?, 1)";
            $stmt_img = $mysqli->prepare($sql_img);
            
            $null = NULL;
            // CORRECCIÓN: Usamos "ibs" (Int, Blob, String) para coincidir con los 3 placeholders.
            $stmt_img->bind_param("ibs", $id_libro, $null, $tipo_mime);
            
            // Enviamos el BLOB por paquetes (más seguro para archivos)
            $stmt_img->send_long_data(1, $imagen_binaria);
            $stmt_img->execute();
        }
        
        // 3. Insertar Existencia Inicial en 0
        $mysqli->query("INSERT INTO existencias (id_libro, cantidad) VALUES ($id_libro, 0)");

        $mysqli->commit();
        $mensaje = "Producto creado correctamente.";
    } catch (Exception $e) {
        $mysqli->rollback();
        // Verificar si es error de código duplicado
        if ($mysqli->errno === 1062) {
            $mensaje = "Error: El código '$codigo' ya existe.";
        } else {
            $mensaje = "Error: " . $e->getMessage();
        }
    }
}

// LISTAR PRODUCTOS (Para la tabla de abajo)
$productos = $mysqli->query("SELECT * FROM libros WHERE estatus = 1");

//FRONTEND ABAJO (Aquí tu compañero de UX pondrá la tabla HTML)
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

    <div class="container" style="max-width: 1200px; margin-top: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2>Gestión de Inventario (Productos)</h2>
        </div>

        <?php if ($mensaje): ?>
            <div class="<?php echo strpos($mensaje, 'Error') !== false ? 'error-message' : 'success-message'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <div class="card" style="margin-bottom: 30px;">
            <h3>Alta de Nuevo Producto</h3>
            <form method="POST" action="productos.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="crear">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="codigo">Código (ISBN/SKU)</label>
                        <input type="text" id="codigo" name="codigo" required placeholder="Ej: 978-0743273565">

                        <label for="titulo">Título del Libro</label>
                        <input type="text" id="titulo" name="titulo" required placeholder="Ej: Cien Años de Soledad">
                    </div>
                    <div>
                        <label for="precio">Precio de Venta</label>
                        <input type="number" id="precio" name="precio" required step="0.01" min="0" placeholder="Ej: 250.00">
                        
                        <label for="imagen">Imagen (Máx. 2MB)</label>
                        <input type="file" id="imagen" name="imagen" accept="image/*" style="width: 90%; padding: 7px 0; border: none;">
                    </div>
                </div>
                <button type="submit" style="margin-top: 15px;">Guardar Producto</button>
            </form>
        </div>

        <div class="card">
            <h3>Listado Actual</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">Img.</th>
                        <th style="width: 15%;">Código</th>
                        <th style="width: 35%;">Título</th>
                        <th style="width: 10%;">Precio Venta</th>
                        <th style="width: 10%;">Stock</th>
                        <th style="width: 25%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                    <tr class="<?php echo ($producto['cantidad'] ?? 0) <= 5 ? 'error-row' : ''; ?>">
                        <td><img src="includes/mostrar_imagen.php?id=<?php echo $producto['id']; ?>" alt="Portada" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;"></td>
                        <td><?php echo htmlspecialchars($producto['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($producto['titulo']); ?></td>
                        <td>$<?php echo number_format($producto['precio_venta'], 2); ?></td>
                        <td>
                            <?php 
                                echo $producto['cantidad'] ?? 0; 
                                if (($producto['cantidad'] ?? 0) <= 5) {
                                    echo ' <span style="color: #b00020; font-weight: bold;">(Bajo)</span>';
                                }
                            ?>
                        </td>
                        <td>
                            <a id=<?php echo $producto['id']; ?>" style="color:#C82B1D; text-decoration: none;">Editar</a> | 
                            <a href="productos.php?action=baja&id=<?php echo $producto['id']; ?>" onclick="return confirm('¿Desactivar?')" style="color:#555; text-decoration: none;">Desactivar</a> |
                            <button class="btn-secondary" style="width: auto; padding: 5px 10px; font-size: 13px;">Comprar Stock</button>
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