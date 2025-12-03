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

    <div class="container main-content-large">
        <div class="flex-between mb-15">
            <h2>Gestión de Inventario (Productos)</h2>
        </div>

        <div class="card mb-30">
            <h3>Alta de Nuevo Producto</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="crear">
                <div class="grid-2">
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
                        <input type="file" id="imagen" name="imagen" accept="image/*" class="w-full" style="padding: 7px 0; border: none;">
                    </div>
                </div>
                <button type="button" class="mt-15" onclick="alert('Producto guardado (Simulado)')">Guardar Producto</button>
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
                    <tr>
                        <td><img src="assets/img/logo-maria-de-letras_icon.svg" alt="Portada" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;"></td>
                        <td>LIB001</td>
                        <td>Cien Años de Soledad</td>
                        <td>$250.00</td>
                        <td>15</td>
                        <td>
                            <a class="text-red" style="text-decoration: none; cursor: pointer;">Editar</a> | 
                            <a class="text-gray" style="text-decoration: none; cursor: pointer;">Desactivar</a> |
                            <button class="btn-secondary w-auto" style="padding: 5px 10px; font-size: 13px;">Comprar Stock</button>
                        </td>
                    </tr>
                    <tr class="error-row">
                        <td><img src="assets/img/logo-maria-de-letras_icon.svg" alt="Portada" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;"></td>
                        <td>LIB002</td>
                        <td>El Principito</td>
                        <td>$150.00</td>
                        <td>
                            3 <span class="text-danger font-bold">(Bajo)</span>
                        </td>
                        <td>
                            <a class="text-red" style="text-decoration: none; cursor: pointer;">Editar</a> | 
                            <a class="text-gray" style="text-decoration: none; cursor: pointer;">Desactivar</a> |
                            <button class="btn-secondary w-auto" style="padding: 5px 10px; font-size: 13px;">Comprar Stock</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="js/main.js"></script>
  </body>
</html>