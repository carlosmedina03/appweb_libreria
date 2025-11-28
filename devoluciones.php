<?php
// ============================================================
// RESPONSABLE: Rol 4 (Lógica) y Rol 2 (UI)
// REQUERIMIENTO: "Devoluciones. Sobre una venta registrada, se seleccionan líneas..."
// ============================================================
require_once 'includes/auth.php';

// TODO:
// 1. Input para buscar folio de venta.
// 2. Mostrar las líneas de esa venta.
// 3. Checkbox/Input para seleccionar cantidad a devolver (Validar: nunca mayor a lo vendido).
// 4. Botón "Procesar Devolución" -> ajax/confirmar_devolucion.php.

// BACKEND ABAJO (NO BORRAR)
// REQUERIMIENTO: "Sobre una venta registrada, se seleccionan líneas..."
require_once 'config/db.php';
require_once 'includes/auth.php';

$venta_encontrada = null;
$detalles_venta = [];
$mensaje_error = "";

// Si el usuario envió el formulario de "Buscar Folio"
if (isset($_POST['btn_buscar_folio'])) {
    $folio_busqueda = intval($_POST['folio_input']);

    // 1. Buscar encabezado de venta
    $sql_v = "SELECT v.id, v.fecha_hora, v.total, u.nombre_completo as cajero 
              FROM ventas v 
              JOIN usuarios u ON v.id_usuario = u.id 
              WHERE v.id = $folio_busqueda";
    $res_v = $mysqli->query($sql_v);

    if ($res_v->num_rows > 0) {
        $venta_encontrada = $res_v->fetch_assoc();

        // 2. Buscar qué productos se vendieron en ese folio
        // Traemos también 'cantidad' para validar que no devuelva de más
        // Ojo: Traemos datos de 'devoluciones' previas si quisieras ser muy estricto, 
        // pero por ahora cumplimos con mostrar lo vendido.
        $sql_d = "SELECT dv.id_libro, dv.cantidad, dv.precio_unitario, dv.importe, l.titulo, l.codigo 
                  FROM detalle_ventas dv 
                  JOIN libros l ON dv.id_libro = l.id 
                  WHERE dv.id_venta = $folio_busqueda";
        $res_d = $mysqli->query($sql_d);
        
        while ($row = $res_d->fetch_assoc()) {
            $detalles_venta[] = $row;
        }
    } else {
        $mensaje_error = "Folio de venta #$folio_busqueda no encontrado.";
    }
}

// AHORA VIENE EL HTML DEL ROL 2...
// Nota para UX: 
// - Si $venta_encontrada existe, mostrar tabla con $detalles_venta.
// - Poner checkboxes o inputs numéricos para que el usuario diga cuánto devuelve.
// - El botón "Confirmar Devolución" debe llamar a JS -> ajax/confirmar_devolucion.php
?>