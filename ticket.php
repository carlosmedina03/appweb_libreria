<?php
// ============================================================
// RESPONSABLE: Rol 4 (Back-End) y Rol 2 (UX-UI) y Rol 6 (Hardware)d
// REQUERIMIENTO: "Ticket 80x40... márgenes 0... monocromo"
// ============================================================
// 1. Recibir ID de venta ($_GET['folio']).
// 2. Obtener datos del negocio, cajero, líneas y totales.
// 3. Renderizar HTML específico para impresora térmica.
// 4. CSS debe estar en /css/ticket.css (@page size: 80mm 40mm).
// 5. NO incluir encabezados/pies del navegador.

//BACKEND (NO BORRAR)
// REQUERIMIENTO: "Datos obligatorios a suministrar por el backend... Logo, Datos negocio, Folio..."
require_once 'config/db.php';
require_once 'includes/functions.php';
session_start();

if (!isset($_GET['folio'])) die("Folio no especificado");
$folio = intval($_GET['folio']);

// 1. Datos del Negocio
$sql_conf = "SELECT * FROM configuracion WHERE id = 1";
$negocio = $mysqli->query($sql_conf)->fetch_assoc();

// 2. Encabezado de Venta
$sql_venta = "SELECT v.*, u.username as cajero 
              FROM ventas v 
              JOIN usuarios u ON v.id_usuario = u.id 
              WHERE v.id = $folio";
$venta = $mysqli->query($sql_venta)->fetch_assoc();

if (!$venta) die("Venta no encontrada");

// 3. Detalles (Líneas)
$sql_det = "SELECT dv.*, l.titulo 
            FROM detalle_ventas dv 
            JOIN libros l ON dv.id_libro = l.id 
            WHERE dv.id_venta = $folio";
$detalles = $mysqli->query($sql_det);

// AHORA VIENE EL HTML DEL ROL 2 (UX)...
//FRONTEND ABAJO
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket de Venta #<?php echo $folio; ?></title>
    <link rel="stylesheet" href="css/ticket.css">
  </head>

  <body onload="window.print()">
    <div class="ticket">
      <div style="text-align: center;">
        <img src="assets/img/logo-maria-de-letras_v2_monocromo.svg" alt="Logo" style="width: 50mm; margin-bottom: 5px;">
        <h1 style="font-size: 14pt; margin: 0;"><?php echo htmlspecialchars($negocio['nombre']); ?></h1>
        <p style="font-size: 8pt; margin: 2px 0;">
          <?php echo htmlspecialchars($negocio['direccion']); ?>
          <br>
          Tel: <?php echo htmlspecialchars($negocio['telefono']); ?>
        </p>
        <div style="border-top: 1px dashed black; margin: 5px 0;"></div>
      </div>
      
      <div style="font-size: 9pt;">
        <p style="margin: 0;">**FOLIO DE VENTA:** <?php echo $venta['id']; ?></p>
        <p style="margin: 0;">**FECHA:** <?php echo date('d/m/Y H:i', strtotime($venta['fecha_hora'])); ?></p>
        <p style="margin: 0 0 5px 0;">**CAJERO:** <?php echo htmlspecialchars($venta['cajero']); ?></p>
        <div style="border-top: 1px dashed black; margin: 5px 0;"></div>
      </div>

      <div style="font-size: 8pt;">
        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr>
              <th style="text-align: left; width: 55%; padding: 1px 0;">PRODUCTO</th>
              <th style="text-align: center; width: 10%; padding: 1px 0;">CNT</th>
              <th style="text-align: right; width: 15%; padding: 1px 0;">PRECIO</th>
              <th style="text-align: right; width: 20%; padding: 1px 0;">IMPORTE</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $subtotal = 0;
            while ($item = $detalles->fetch_assoc()): 
                $subtotal += $item['importe'];
            ?>
            <tr>
              <td style="text-align: left; padding: 1px 0;"><?php echo htmlspecialchars(substr($item['titulo'], 0, 25)); ?></td>
              <td style="text-align: center; padding: 1px 0;"><?php echo $item['cantidad']; ?></td>
              <td style="text-align: right; padding: 1px 0;"><?php echo number_format($item['precio_unitario'], 2); ?></td>
              <td style="text-align: right; padding: 1px 0;"><?php echo number_format($item['importe'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <div style="border-top: 1px dashed black; margin: 5px 0;"></div>

      <div style="font-size: 10pt; text-align: right;">
        <p style="margin: 0;">**SUBTOTAL:** $<?php echo number_format($subtotal, 2); ?></p>
        <p style="margin: 0;">**IVA (16%):** $<?php echo number_format($venta['iva'], 2); ?></p>
        <h2 style="margin: 5px 0 10px 0; font-size: 14pt;">**TOTAL:** $<?php echo number_format($venta['total'], 2); ?></h2>
      </div>

      <div style="text-align: center; font-size: 8pt; margin-top: 10px;">
        <div style="border-top: 1px dashed black; margin: 5px 0;"></div>
        <p style="margin: 0;"><?php echo htmlspecialchars($negocio['mensaje_final']); ?></p>
        <p style="margin: 2px 0 0 0;">(Powered by Sistema MDL)</p>
      </div>

      <div class="no-print" style="text-align: center; margin-top: 20px;">
          <button onclick="window.close()" class="btn" style="width: 80%; background: #555;">Cerrar Ticket</button>
      </div>
    </div>
  </body>
</html>