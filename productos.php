<?php
// ============================================================
// RESPONSABLE: Rol 4 (CRUD) y Rol 2 (UI)
// REQUERIMIENTO: "CRUD productos con imagen BLOB"
// ============================================================
require_once 'config/db.php';
require_once 'includes/auth.php';

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