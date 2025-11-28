<?php
// ============================================================
// RESPONSABLE: Rol 4 (Back-End) y Rol 2 (UX-UI) y Rol 6 (Hardware)
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