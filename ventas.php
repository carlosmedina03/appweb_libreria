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
$cajero_nombre = $_SESSION['user']['nombre'];
$cajero_id = $_SESSION['user']['id'];

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


    <div class="container main-content">
      <h2>Punto de Venta</h2>
      <p class="text-sm text-gray">Atendido por: <strong>Cajero 1</strong></p>
      
      <div class="flex-row mb-20">
        <input type="text" 
          id="codigo" 
          name="codigo"
          placeholder="Escanear código de barras o ingresar manual..." 
          autofocus
          class="flex-grow w-auto">
        <button class="btn w-150">Buscar</button> 
      </div>

      <div class="card">
        <h3>Carrito de Venta</h3>
        <table>
          <thead>
            <tr>
              <th>Producto</th>
              <th class="col-10">Cant.</th>
              <th class="col-15">Precio Unit.</th>
              <th class="col-15">Subtotal</th>
            </tr>
          </thead>
          <tbody id="tabla-carrito">
            <!-- Placeholder data for cart -->
            <tr>
                <td>Cien Años de Soledad</td>
                <td class="text-center">1</td>
                <td class="text-right">$250.00</td>
                <td class="text-right">$250.00</td>
            </tr>
            <tr>
                <td>El Principito</td>
                <td class="text-center">2</td>
                <td class="text-right">$150.00</td>
                <td class="text-right">$300.00</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="text-right text-2xl font-bold text-red mt-20">
        Total: <span id="total-display">$550.00</span>
      </div>

      <button id="btn-cobrar" class="btn mt-15">
        Confirmar Venta y Cobrar
      </button>

    </div>
    
    <script src="js/main.js"></script>
    <script src="js/ventas.js"></script>
  </body>
</html>