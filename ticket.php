<?php
// ============================================================
// RESPONSABLE: Rol 4 (Back-End) y Rol 2 (UX-UI) y Rol 6 (Hardware)d
// REQUERIMIENTO: "Ticket 80x40... márgenes 0... monocromo"
// ============================================================
// 1. Recibir ID de venta ($_GET['folio']).
// 2. Obtener datos del negocio, cajero, líneas y totales.
// 3. Renderizar HTML específico para impresora térmica.
// 4. CSS debe estar en /css/ticket.css (@page size: 80mm 40mm).
// 5. NO incluir encabezados/pies del navegador.f

//BACKEND (NO BORRAR)
// REQUERIMIENTO: "Datos obligatorios a suministrar por el backend... Logo, Datos negocio, Folio..."
require_once 'config/db.php';
require_once 'includes/functions.php';
session_start();

if (!isset($_GET['folio'])) die("Folio no especificado");
$folio = intval($_GET['folio']);
$tipo = $_GET['tipo'] ?? 'venta'; // Por defecto es venta, puede ser 'devolucion'

// 1. Datos del Negocio
$sql_conf = "SELECT * FROM configuracion WHERE id = 1";
$negocio = $mysqli->query($sql_conf)->fetch_assoc();

$titulo_ticket = "TICKET DE VENTA";

if ($tipo === 'devolucion') {
    $titulo_ticket = "COMPROBANTE DE DEVOLUCIÓN";
    // 2. Encabezado de Devolución
    $sql_encabezado = "SELECT d.*, u.username as cajero, d.id_venta as folio_original
                       FROM devoluciones d 
                       JOIN usuarios u ON d.id_usuario = u.id 
                       WHERE d.id = $folio";
    $encabezado = $mysqli->query($sql_encabezado)->fetch_assoc();

    if (!$encabezado) die("Devolución no encontrada");

    // 3. Detalles de Devolución
    $sql_det = "SELECT dd.cantidad, dd.monto_reembolsado as importe, l.titulo, 0 as precio_unitario
                FROM detalle_devoluciones dd
                JOIN libros l ON dd.id_libro = l.id
                WHERE dd.id_devolucion = $folio";
    $detalles = $mysqli->query($sql_det);

} else { // Es una venta normal
    // 2. Encabezado de Venta
    $sql_encabezado = "SELECT v.*, u.username as cajero 
                  FROM ventas v 
                  JOIN usuarios u ON v.id_usuario = u.id 
                  WHERE v.id = $folio";
    $encabezado = $mysqli->query($sql_encabezado)->fetch_assoc();

    if (!$encabezado) die("Venta no encontrada");

    // 3. Detalles de Venta
    $sql_det = "SELECT dv.*, l.titulo 
                FROM detalle_ventas dv 
                JOIN libros l ON dv.id_libro = l.id 
                WHERE dv.id_venta = $folio";
    $detalles = $mysqli->query($sql_det);
}

/**
 * Función inteligente para formatear una línea de producto para el ticket.
 * Se ajusta a nombres largos y alinea columnas perfectamente.
 * @param string $nombre El nombre del producto.
 * @param int $cantidad La cantidad vendida.
 * @param float $precio El precio unitario.
 * @param float $importe El importe total de la línea.
 * @param int $anchoTotal El número total de caracteres del ticket (aprox. 40-48 para 80mm).
 * @return string El texto de la línea formateado.
 */
function imprimir_linea($nombre, $cantidad, $precio, $importe, $anchoTotal = 48) {
    $anchoPrecio = 11; // Ancho para el importe
    $anchoCantidad = 5; // Ancho para la cantidad
    $espacioEntreCols = ""; // Espacio entre columnas
    $anchoNombre = $anchoTotal - $anchoPrecio - $anchoCantidad - 3;

    // Formatear datos
    $cantidadStr = str_pad($cantidad, $anchoCantidad, " ", STR_PAD_LEFT);
    $importeStr = str_pad('$' . number_format($importe, 2), $anchoPrecio, " ", STR_PAD_LEFT);

    // Ajustar nombre del producto si es muy largo
    $lineasNombre = wordwrap($nombre, $anchoNombre, "\n", true);
    $lineas = explode("\n", $lineasNombre);

    $lineaPrincipal = str_pad($lineas[0], $anchoNombre) .  $espacioEntreCols . $cantidadStr . $espacioEntreCols . $importeStr;

    // Si el nombre ocupa más de una línea
    if (count($lineas) > 1) {
        for ($i = 1; $i < count($lineas); $i++) {
            $lineaPrincipal .= "\n" . str_pad($lineas[$i], $anchoTotal);
        }
    }
    return $lineaPrincipal;
}

// AHORA VIENE EL HTML DEL ROL 2 (UX)...
//FRONTEND ABAJO
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $titulo_ticket; ?> #<?php echo $folio; ?></title>
    <link rel="stylesheet" href="css/ticket.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <script src="js/main.js"></script>
  </head>
  
  <body>
    <div class="ticket" data-folio="<?php echo str_pad($folio, 8, '0', STR_PAD_LEFT); ?>">
      <div class="ticket-logo-container">
        <img src="img.php?tipo=logo" alt="Logo" class="ticket-logo">
        <h1 class="ticket-title"><?php echo htmlspecialchars($negocio['razon_social']); ?></h1>
        <p class="ticket-info">
          <?php echo htmlspecialchars($negocio['domicilio']); ?>
          <br>
          Tel: <?php echo htmlspecialchars($negocio['telefono'] ?? ''); ?>
        </p>
        <div class="dashed-line"></div>
      </div>
      
      <div>
        <p class="ticket-header-text" style="font-weight: bold;"><?php echo $titulo_ticket; ?>: <?php echo $encabezado['id']; ?></p>
        <?php if ($tipo === 'devolucion'): ?>
            <p class="ticket-header-text">SOBRE VENTA ORIGINAL: #<?php echo $encabezado['folio_original']; ?></p>
        <?php endif; ?>
        <p class="ticket-header-text">FECHA: <?php echo date('d/m/Y H:i', strtotime($encabezado['fecha_hora'])); ?></p>
        <p class="ticket-subheader-text">CAJERO: <?php echo htmlspecialchars($encabezado['cajero']); ?></p>
        <div class="dashed-line"></div>
      </div>

      <div class="detalle-productos">
        <div class="ticket-table-header">
            <span>PRODUCTO</span>
            <span>CANT/TOTAL</span>
        </div>
        <pre><?php
          while ($item = $detalles->fetch_assoc()) {
              echo imprimir_linea(
                  htmlspecialchars($item['titulo']),
                  $item['cantidad'],
                  $item['precio_unitario'],
                  $item['importe']
              );
              echo "\n";
          }
        ?></pre>
      </div>

      <div class="dashed-line"></div>

      <div class="text-right">
        <?php if ($tipo === 'venta'): ?>
            <p class="ticket-header-text">SUBTOTAL: $<?php echo number_format($encabezado['subtotal'], 2); ?></p>
            <p class="ticket-header-text">IVA (16%): $<?php echo number_format($encabezado['iva'], 2); ?></p>
            <h2 class="ticket-total">TOTAL: $<?php echo number_format($encabezado['total'], 2); ?></h2>
        <?php else: ?>
            <h2 class="ticket-total">TOTAL REEMBOLSADO: $<?php echo number_format($encabezado['total_reembolsado'], 2); ?></h2>
        <?php endif; ?>
      </div>

      <div class="ticket-center">
        <div class="dashed-line"></div>
        <p class="ticket-header-text"><?php echo htmlspecialchars($negocio['mensaje_ticket'] ?? '¡Gracias por su compra!'); ?></p>
        <p style="margin: 2px 0 0 0;">(Powered by Sistema MDL)</p>

        <div style="margin-top: 10px;">
          <svg id="codigoBarrasTicket"></svg>
        </div>

      </div>

      <div class="no-print ticket-center" style="margin-top: 20px;">
          <button class="btn btn-close-window">Cerrar Ticket</button>
      </div>
    </div>
  </body>
</html>