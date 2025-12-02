<?php
// ============================================================
// RESPONSABLE: Rol 4 (Lógica) y Rol 2 (UI)d
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

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Devoluciones</title>
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

    <div class="container" style="max-width: 900px; margin-top: 20px;">
        <h2>Gestión de Devoluciones</h2>

        <?php if ($mensaje_error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($mensaje_error); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-bottom: 20px;">
            <h3>Buscar Venta por Folio</h3>
            <form method="POST" action="devoluciones.php">
                <div style="display: flex; gap: 10px;">
                    <input type="number" 
                        id="folio_input" 
                        name="folio_input" 
                        placeholder="Ingresa Folio de Venta" 
                        required 
                        style="flex-grow: 1; width: auto;" 
                        value="<?php echo isset($_POST['folio_input']) ? htmlspecialchars($_POST['folio_input']) : ''; ?>">
                    <button type="submit" name="btn_buscar_folio" class="btn" style="width: 150px;">Buscar Venta</button>
                </div>
            </form>
        </div>

        <?php if ($venta_encontrada): ?>
            <div class="card" style="margin-top: 20px;">
                <h3>Venta Encontrada (#<?php echo $venta_encontrada['id']; ?>)</h3>
                <p>Fecha: **<?php echo $venta_encontrada['fecha_hora']; ?>** | Total Venta: **$<?php echo number_format($venta_encontrada['total'], 2); ?>** | Cajero: **<?php echo htmlspecialchars($venta_encontrada['cajero']); ?>**</p>
                <hr>
                
                <form id="form-devolucion">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">Devolver</th>
                                <th style="width: 35%;">Producto</th>
                                <th style="width: 15%;">Código</th>
                                <th style="width: 10%;">Cant. Vendida</th>
                                <th style="width: 15%;">Cant. a Devolver</th>
                                <th style="width: 20%;">Precio Unitario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles_venta as $detalle): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="devolver_item[]" value="<?php echo $detalle['id_libro']; ?>">
                                </td>
                                <td><?php echo htmlspecialchars($detalle['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['codigo']); ?></td>
                                <td><?php echo $detalle['cantidad']; ?></td>
                                <td>
                                    <input type="number" 
                                        name="cantidad_devolver[<?php echo $detalle['id_libro']; ?>]" 
                                        min="1" 
                                        max="<?php echo $detalle['cantidad']; ?>" 
                                        value="0" 
                                        style="width: 60px; padding: 5px; margin: 0;">
                                </td>
                                <td>$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <button type="button" id="btn-procesar-devolucion" class="btn" style="margin-top: 15px;">
                        Procesar Devolución Seleccionada
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="js/main.js"></script>
    <script>
    // LÓGICA JAVASCRIPT: Manejar el evento del botón "Procesar Devolución" 
    // y realizar la llamada AJAX a ajax/confirmar_devolucion.php (Rol 4)
    document.getElementById('btn-procesar-devolucion').addEventListener('click', function() {
        if (confirm('¿Confirma que desea procesar la devolución?')) {
            // Aquí iría la lógica de recolección de datos del formulario y el fetch/AJAX
            alert('Procesando devolución (Lógica AJAX pendiente de implementación por Rol 2/4).');
        }
    });
    </script>
  </body>
</html>