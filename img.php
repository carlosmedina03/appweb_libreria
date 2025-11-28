<?php
// REQUERIMIENTO: "Logo (ruta o binario servido por img.php)" y "Catálogo con imagen en BD"
// ==========================================
require_once 'config/db.php';

$tipo = $_GET['tipo'] ?? 'producto'; // 'producto' o 'logo'
$id   = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tipo === 'logo') {
    // 1. Buscar Logo de la empresa
    $sql = "SELECT logo_empresa FROM configuracion WHERE id = 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($contenido);
    $stmt->fetch();
    
    if ($contenido) {
        header("Content-type: image/png"); // Asumimos PNG o JPEG
        echo $contenido;
    } else {
        // Imagen por defecto si no hay logo
        header("Content-type: image/png");
        readfile("assets/img/logo_temp.png"); 
    }

} else {
    // 2. Buscar Imagen de Producto
    $sql = "SELECT contenido, tipo_mime FROM imagenes_libro WHERE id_libro = ? AND es_principal = 1 LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($contenido, $mime);
    
    if ($stmt->fetch()) {
        header("Content-type: " . $mime);
        echo $contenido;
    } else {
        // Imagen placeholder si no tiene foto
        header("Content-type: image/png");
        // Asegúrate de tener una imagen gris llamada placeholder.png en assets/img
        readfile("assets/img/logo_temp.png"); 
    }
}
?>