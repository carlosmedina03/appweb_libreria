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
function imprimir_linea($nombre, $cantidad, $precio, $importe, $anchoTotal = 42) {
    $anchoPrecio = 9; // Ancho para el importe
    $anchoCantidad = 4; // Ancho para la cantidad
    $anchoNombre = $anchoTotal - $anchoPrecio - $anchoCantidad;

    // Formatear datos
    $cantidadStr = str_pad($cantidad, $anchoCantidad, " ", STR_PAD_LEFT);
    $importeStr = str_pad('$' . number_format($importe, 2), $anchoPrecio, " ", STR_PAD_LEFT);

    // Ajustar nombre del producto si es muy largo
    $lineasNombre = wordwrap($nombre, $anchoNombre, "\n", true);
    $lineas = explode("\n", $lineasNombre);

    $lineaPrincipal = str_pad($lineas[0], $anchoNombre) . $cantidadStr . $importeStr;

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
  </head>
  
  <body>
    <div class="ticket">
      <div style="text-align: center;">
        <img src="img.php?tipo=logo" alt="Logo" style="max-width: 160px; max-height: 80px; margin-bottom: 5px;">
        <h1 style="font-size: 12pt; margin: 0;"><?php echo htmlspecialchars($negocio['razon_social']); ?></h1>
        <p style="margin: 2px 0; font-size: 7.5pt;">
          <?php echo htmlspecialchars($negocio['domicilio']); ?>
          <br>
          Tel: <?php echo htmlspecialchars($negocio['telefono'] ?? ''); ?>
        </p>
        <div style="border-top: 1px dashed black; margin: 5px 0;"></div>
      </div>
      
      <div>
        <p style="margin: 0; font-weight: bold;"><?php echo $titulo_ticket; ?>: <?php echo $encabezado['id']; ?></p>
        <?php if ($tipo === 'devolucion'): ?>
            <p style="margin: 0;">SOBRE VENTA ORIGINAL: #<?php echo $encabezado['folio_original']; ?></p>
        <?php endif; ?>
        <p style="margin: 0;">FECHA: <?php echo date('d/m/Y H:i', strtotime($encabezado['fecha_hora'])); ?></p>
        <p style="margin: 0 0 5px 0;">CAJERO: <?php echo htmlspecialchars($encabezado['cajero']); ?></p>
        <div style="border-top: 1px dashed black; margin: 5px 0;"></div>
      </div>

      <div class="detalle-productos">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed black; margin-bottom: 3px; font-weight: bold;">
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

      <div style="border-top: 1px dashed black; margin: 5px 0;"></div>

      <div style="text-align: right;">
        <?php if ($tipo === 'venta'): ?>
            <p style="margin: 0;">SUBTOTAL: $<?php echo number_format($encabezado['subtotal'], 2); ?></p>
            <p style="margin: 0;">IVA (16%): $<?php echo number_format($encabezado['iva'], 2); ?></p>
            <h2 style="margin: 5px 0 10px 0; font-size: 11pt; font-weight: bold;">TOTAL: $<?php echo number_format($encabezado['total'], 2); ?></h2>
        <?php else: ?>
            <h2 style="margin: 5px 0 10px 0; font-size: 11pt; font-weight: bold;">TOTAL REEMBOLSADO: $<?php echo number_format($encabezado['total_reembolsado'], 2); ?></h2>
        <?php endif; ?>
      </div>

      <div style="text-align: center; margin-top: 10px;">
        <div style="border-top: 1px dashed black; margin: 5px 0;"></div>
        <p style="margin: 0;"><?php echo htmlspecialchars($negocio['mensaje_ticket'] ?? '¡Gracias por su compra!'); ?></p>
        <p style="margin: 2px 0 0 0;">(Powered by Sistema MDL)</p>
      </div>

      <div class="no-print" style="text-align: center; margin-top: 20px;">
          <button onclick="window.close()" class="btn" style="width: 80%; background: #555;">Cerrar Ticket</button>
      </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500); // 500ms de retardo para asegurar que todo cargue
        });
    </script>
  </body>
</html>