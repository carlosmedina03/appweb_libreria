<?php
// ventas.php

// 1. SEGURIDAD
// Usamos security_guard.php para validar que exista sesión.
// Permite acceso a Admin y Operador.
require_once 'includes/seguridad_basica.php';

// 2. VARIABLES DE SESIÓN
$rol = $_SESSION['user']['rol'];
$cajero_nombre = $_SESSION['user']['nombre'];
$cajero_id = $_SESSION['user']['id'];

// Inicializar carrito vacío si es la primera vez que entra
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
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
            <img src="assets/img/logo-maria-de-letras_v2.svg" alt="Logo">
        </div>

        <div class="navbar-menu">
            <a href="dashboard.php">Inicio</a>
            <a href="ventas.php">Punto de Venta</a>
            
            <?php if (isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'admin'): ?>
                <a href="productos.php">Productos</a>
                <a href="compras.php">Compras</a>
                <a href="devoluciones.php">Devoluciones</a>
                <a href="usuarios.php">Usuarios</a>

                <div class="dropdown">
                    <button class="dropbtn">Reportes ▾</button>
                    <div class="dropdown-content">
                        <a href="reportes/compras.php">Reportes Compra</a>
                        <a href="reportes/devoluciones.php">Reportes Devoluciones</a>
                        <a href="reportes/inventario.php">Reportes Inventario</a>
                        <a href="reportes/ventas_detalle.php">Reportes Detalle</a>
                        <a href="reportes/ventas_encabezado.php">Reportes Encabezado</a>
                    </div>
                </div>

            <?php else: ?>
                <a href="devoluciones.php">Devoluciones</a>
            <?php endif; ?>
            
            <a href="includes/logout.php" class="cerrar-sesion">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container main-content">
      <h2>Punto de Venta</h2>
      <p class="text-sm text-gray">Atendido por: <strong><?php echo htmlspecialchars($cajero_nombre); ?></strong></p>
      
      <div class="flex-row mb-20">
        <input type="text" 
          id="codigo" 
          name="codigo"
          placeholder="Escanear código de barras o ingresar manual..." 
          autofocus
          class="flex-grow w-auto">
        <button id="btn-buscar" class="btn w-150">Buscar</button> 
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
              <th class="col-5"></th>
            </tr>
          </thead>
          <tbody id="tabla-carrito">
            <tr>
                <td colspan="5" style="text-align: center; color: #777;">Escanea un producto para comenzar...</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="text-right text-2xl font-bold text-red mt-20">
        Total: <span id="total-display">$0.00</span>
      </div>

      <div class="flex-row mt-15" style="justify-content: flex-end; gap: 15px;">
        <button id="btn-cancelar" class="btn" style="background-color: #777;">
          Cancelar Venta
        </button>
        <button id="btn-cobrar" class="btn">
          Confirmar Venta y Cobrar
        </button>
      </div>

    </div>
    
    <script src="js/main.js"></script>
    <script src="js/ventas.js"></script>
  </body>
</html>