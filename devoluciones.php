<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Devoluciones</title>
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

        <a href="index.php">Salir</a>
      </div>
    
    </div>

    <div class="container main-content-small">
        <h2>Gestión de Devoluciones</h2>

        <div class="card mb-20">
            <h3>Buscar Venta por Folio</h3>
            <form method="POST" action="">
                <div class="flex-row">
                    <input type="number" 
                        id="folio_input" 
                        name="folio_input" 
                        placeholder="Ingresa Folio de Venta" 
                        required 
                        class="flex-grow w-auto" 
                        value="1001">
                    <button type="button" name="btn_buscar_folio" class="btn w-150">Buscar Venta</button>
                </div>
            </form>
        </div>

        <!-- Simulación de venta encontrada -->
        <div class="card mt-20">
            <h3>Venta Encontrada (#1001)</h3>
            <p>Fecha: <strong>01/12/2025 10:30:00</strong> | Total Venta: <strong>$250.00</strong> | Cajero: <strong>Juan Pérez</strong></p>
            <hr>
            
            <form id="form-devolucion">
                <table>
                    <thead>
                        <tr>
                            <th class="col-5">Devolver</th>
                            <th class="col-35">Producto</th>
                            <th class="col-15">Código</th>
                            <th class="col-10">Cant. Vendida</th>
                            <th class="col-15">Cant. a Devolver</th>
                            <th class="col-20">Precio Unitario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="checkbox" name="devolver_item[]" value="1">
                            </td>
                            <td>Cien Años de Soledad</td>
                            <td>LIB001</td>
                            <td>1</td>
                            <td>
                                <input type="number" 
                                    name="cantidad_devolver" 
                                    min="1" 
                                    max="1" 
                                    value="0" 
                                    class="w-60"
                                    style="padding: 5px; margin: 0;">
                            </td>
                            <td>$250.00</td>
                        </tr>
                    </tbody>
                </table>
                
                <button type="button" id="btn-procesar-devolucion" class="btn mt-15">
                    Procesar Devolución Seleccionada
                </button>
            </form>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script>
    // LÓGICA JAVASCRIPT: Manejar el evento del botón "Procesar Devolución" 
    // y realizar la llamada AJAX a ajax/confirmar_devolucion.php (Rol 4)
    document.getElementById('btn-procesar-devolucion').addEventListener('click', function() {
        if (confirm('¿Confirma que desea procesar la devolución?')) {
            // Aquí iría la lógica de recolección de datos del formulario y el fetch/AJAX
            alert('Procesando devolución (Simulado).');
        }
    });
    </script>
  </body>
</html>