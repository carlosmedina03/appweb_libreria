<?php
// ============================================================
// RESPONSABLE: Rol 4 (Lógica) y Rol 2 (UI)d
// REQUERIMIENTO: "Compras... Capturan encabezado... y detalle (producto, cantidad, costo)"
// ============================================================
require_once 'includes/security_guard.php'; // Guard: Solo Admins
require_once 'config/db.php';

// TODO:
// 1. Tabla dinámica (JS) para agregar productos al listado de compra.
// 2. Botón "Guardar Compra" -> ajax/confirmar_compra.php.

// BACKEND ACA (NO BORRAR)
// REQUERIMIENTO: "Compras. Capturan encabezado (fecha, proveedor...)"

// Obtener lista de proveedores para el <select> del HTML
// El de UX usará la variable $proveedores en un foreach
$sql_prov = "SELECT id, nombre FROM proveedores WHERE estatus = 1 ORDER BY nombre";
$res_prov = $mysqli->query($sql_prov);
$proveedores = [];
while ($row = $res_prov->fetch_assoc()) {
    $proveedores[] = $row;
}

// AHORA VIENE EL HTML DEL ROL 2...
// Nota para UX: Usar foreach($proveedores as $p) para llenar el <select name="proveedor">
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
        <h2>Registro de Orden de Compra</h2>

        <div class="card">
            <h3>Datos de la Compra</h3>
            <form id="form-compra-encabezado">
                <div class="grid-2">
                    <div>
                        <label for="fecha">Fecha de Pedido</label>
                        <input type="date" id="fecha" name="fecha" required value="2025-12-02">
                    </div>
                    <div>
                        <label for="proveedor">Proveedor</label>
                        <select id="proveedor" name="proveedor" required>
                            <option value="">-- Seleccione un proveedor --</option>
                            <option value="1">Editorial Planeta</option>
                            <option value="2">Penguin Random House</option>
                            <option value="3">Fondo de Cultura Económica</option>
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
                        <td>Cien Años de Soledad</td>
                        <td>LIB001</td>
                        <td><input type="number" value="10" class="w-60"></td>
                        <td>$150.00</td>
                        <td>$1,500.00</td>
                        <td><button class="btn-secondary">X</button></td>
                    </tr>
                     <tr>
                        <td>El Principito</td>
                        <td>LIB002</td>
                        <td><input type="number" value="20" class="w-60"></td>
                        <td>$80.00</td>
                        <td>$1,600.00</td>
                        <td><button class="btn-secondary">X</button></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="text-right text-xl font-bold mt-15">
                Total Compra: <span id="total-compra-display">$3,100.00</span>
            </div>

            <button id="btn-guardar-compra" class="btn mt-20">
                Guardar Orden de Compra
            </button>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script>
    // LÓGICA JAVASCRIPT (Rol 2)
    // 1. Manejar el click de #btn-agregar-item (Ej: Abrir modal o agregar fila estática).
    // 2. Calcular subtotales y actualizar #total-compra-display.
    // 3. Manejar el click de #btn-guardar-compra y realizar la llamada AJAX a ajax/confirmar_compra.php (Rol 4).
    document.getElementById('btn-guardar-compra').addEventListener('click', function() {
        if (confirm('¿Confirma la creación de esta Orden de Compra?')) {
            alert('Guardando orden (Simulado).');
        }
    });
    </script>
  </body>
</html>