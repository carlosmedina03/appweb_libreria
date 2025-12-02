<?php
// ============================================================
// RESPONSABLE: Rol 4 (Lógica) y Rol 2 (UI)d
// REQUERIMIENTO: "Ventas... captura por código o buscador... retorno automático"
// ============================================================
require_once 'includes/auth.php'; // Guard: Solo operadores y admins

// TODO:
// 1. Input autofocus para el lector de código de barras.
// 2. Tabla visual del "Carrito de compras" actual (desde $_SESSION).
// 3. Botón "Confirmar Venta" -> llama a ajax/confirmar_venta.php.
// 4. Al terminar, abrir ticket.php en ventana nueva (window.open).

// BACKEND ABAJO (NO BORRAR)
// REQUERIMIENTO: "Ventas... cajero (desde sesión)"
require_once 'includes/auth.php'; 
// Nota: No restringimos rol porque Admin y Operador pueden vender.

// Inicializar carrito vacío si es la primera vez que entra
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Datos del cajero para mostrar en pantalla (opcional, pero útil para UX)
//$cajero_nombre = $_SESSION['user']['nombre_completo'];
//$cajero_id = $_SESSION['user']['id'];

// AHORA VIENE EL HTML DEL ROL 2...
// El resto de la lógica (buscar producto, agregar) se hace via AJAX con los archivos que ya te di.
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Punto de Venta</title>
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


    <div class="container" style="max-width: 1000px; margin-top: 20px;">
      <h2>Punto de Venta</h2>
      <p style="font-size: 14px; color: #555;">Atendido por: **<?php echo htmlspecialchars($cajero_nombre); ?>**</p>
      
      <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="text" 
          id="codigo" 
          name="codigo"
          placeholder="Escanear código de barras o ingresar manual..." 
          autofocus
          style="flex-grow: 1; width: auto;">
        <button class="btn" style="width: 150px;">Buscar</button> 
      </div>

      <div class="card">
        <h3>Carrito de Venta</h3>
        <table>
          <thead>
            <tr>
              <th>Producto</th>
              <th style="width: 10%;">Cant.</th>
              <th style="width: 15%;">Precio Unit.</th>
              <th style="width: 15%;">Subtotal</th>
            </tr>
          </thead>
          <tbody id="tabla-carrito">
            <tr><td colspan="4" style="text-align: center;">Escanea un producto para empezar.</td></tr>
          </tbody>
        </table>
      </div>

      <div style="text-align: right; font-size: 24px; font-weight: bold; color: #C82B1D; margin-top: 20px;">
        Total: <span id="total-display">$0.00</span>
      </div>

      <button id="btn-cobrar" class="btn" style="margin-top: 15px;">
        Confirmar Venta y Cobrar
      </button>

    </div>
    
    <script src="js/main.js"></script>
    <script src="js/ventas.js"></script>
  </body>
</html>