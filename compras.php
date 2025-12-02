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
        <h2>Registro de Orden de Compra</h2>

        <div class="card">
            <h3>Datos de la Compra</h3>
            <form id="form-compra-encabezado">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
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

        <div class="card" style="margin-top: 20px;">
            <h3>Detalle de Productos a Comprar</h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <input type="text" 
                    id="input-producto-compra" 
                    placeholder="Buscar producto por título o código..." 
                    style="flex-grow: 1; width: auto;">
                <button type="button" id="btn-agregar-item" class="btn" style="width: 150px;">Agregar Item</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 35%;">Producto</th>
                        <th style="width: 15%;">Código</th>
                        <th style="width: 15%;">Cantidad Pedida</th>
                        <th style="width: 15%;">Costo Unitario</th>
                        <th style="width: 10%;">Subtotal</th>
                        <th style="width: 10%;"></th>
                    </tr>
                </thead>
                <tbody id="tabla-detalle-compra">
                    <tr><td colspan="6" style="text-align: center; color: #888;">Agrega productos a la orden.</td></tr>
                </tbody>
            </table>
            
            <div style="text-align: right; font-size: 20px; font-weight: bold; margin-top: 15px;">
                Total Compra: <span id="total-compra-display">$0.00</span>
            </div>

            <button id="btn-guardar-compra" class="btn" style="margin-top: 20px;">
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
            alert('Guardando orden (Lógica AJAX pendiente de implementación por Rol 2/4).');
        }
    });
    </script>
  </body>
</html>