<?php
// compras.php

// 1. SEGURIDAD (Rol 5)
// Este include hace dos cosas:
// a) Verifica que haya sesión.
// b) Verifica que el rol sea 'admin'. Si es operador, lo expulsa.
require_once 'includes/security_guard.php'; 

require_once 'config/db.php';

// 2. DEFINICIÓN DE VARIABLES PARA VISTA
// Necesario para que el Navbar sepa qué mostrar
$rol = $_SESSION['user']['rol']; 

// BACKEND (Lógica de Proveedores)
// Obtener lista de proveedores para el <select> del HTML
$proveedores = [];
// Verificamos que la conexión exista antes de consultar
if (isset($mysqli)) {
    $sql_prov = "SELECT id, nombre FROM proveedores WHERE estatus = 1 ORDER BY nombre";
    if ($res_prov = $mysqli->query($sql_prov)) {
        while ($row = $res_prov->fetch_assoc()) {
            $proveedores[] = $row;
        }
    }
}
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Órdenes de Compra</title>
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
        
        <?php if ($rol === 'admin'): ?>
            <a href="compras.php">Compras</a>
            <a href="devoluciones.php">Devoluciones</a>
            <a href="usuarios.php">Usuarios</a> <a href="productos.php">Productos</a>
            
            <a href="reportes/compras.php">Reportes</a>
        <?php else: ?>
            <a href="devoluciones.php">Devoluciones</a>
        <?php endif; ?>
        
        <a href="includes/logout.php" style="background: #333; color: white;">Salir</a>
      </div>
    </div>

    <div class="container main-content">
        <h2>Registro de Orden de Compra</h2>

        <div class="card">
            <h3>Datos de la Compra</h3>
            <form id="form-compra-encabezado">
                <div class="grid-2">
                    <div>
                        <label for="fecha">Fecha de Pedido</label>
                        <input type="date" id="fecha" name="fecha" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <label for="proveedor">Proveedor</label>
                        <select id="proveedor" name="proveedor" required>
                            <option value="">-- Seleccione un proveedor --</option>
                            <?php foreach ($proveedores as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="card mt-20">
            <h3>Detalle de Productos a Comprar</h3>
            
            <div class="flex-row mb-15">
                <input type="text" 
                    id="input-producto-compra" 
                    placeholder="Buscar producto por título o código..." 
                    class="flex-grow w-auto">
                <button type="button" id="btn-agregar-item" class="btn w-150">Agregar Item</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="col-35">Producto</th>
                        <th class="col-15">Código</th>
                        <th class="col-15">Cantidad Pedida</th>
                        <th class="col-15">Costo Unitario</th>
                        <th class="col-10">Subtotal</th>
                        <th class="col-10"></th>
                    </tr>
                </thead>
                <tbody id="tabla-detalle-compra">
                    <tr>
                        <td colspan="6" style="text-align:center; color:#888;">Agrega productos para comenzar la orden</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="text-right text-xl font-bold mt-15">
                Total Compra: <span id="total-compra-display">$0.00</span>
            </div>

            <button id="btn-guardar-compra" class="btn mt-20">
                Guardar Orden de Compra
            </button>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script>
    document.getElementById('btn-guardar-compra').addEventListener('click', function() {
        if (confirm('¿Confirma la creación de esta Orden de Compra?')) {
            alert('Lógica de guardado pendiente (AJAX).');
        }
    });
    </script>
  </body>
</html>