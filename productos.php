<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Productos</title>
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

    <div class="container main-content-large">
        <div class="flex-between mb-15">
            <h2>Gestión de Inventario (Productos)</h2>
        </div>

        <div class="card mb-30">
            <h3>Alta de Nuevo Producto</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="crear">
                <div class="grid-2">
                    <div>
                        <label for="codigo">Código (ISBN/SKU)</label>
                        <input type="text" id="codigo" name="codigo" required placeholder="Ej: 978-0743273565">

                        <label for="titulo">Título del Libro</label>
                        <input type="text" id="titulo" name="titulo" required placeholder="Ej: Cien Años de Soledad">
                    </div>
                    <div>
                        <label for="precio">Precio de Venta</label>
                        <input type="number" id="precio" name="precio" required step="0.01" min="0" placeholder="Ej: 250.00">
                        
                        <label for="imagen">Imagen (Máx. 2MB)</label>
                        <input type="file" id="imagen" name="imagen" accept="image/*" class="w-full" style="padding: 7px 0; border: none;">
                    </div>
                </div>
                <button type="button" class="mt-15" onclick="alert('Producto guardado (Simulado)')">Guardar Producto</button>
            </form>
        </div>

        <div class="card">
            <h3>Listado Actual</h3>
            <table>
                <thead>
                    <tr>
                        <th class="col-5">Img.</th>
                        <th class="col-15">Código</th>
                        <th class="col-35">Título</th>
                        <th class="col-10">Precio Venta</th>
                        <th class="col-10">Stock</th>
                        <th class="col-25">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img src="assets/img/logo-maria-de-letras_icon.svg" alt="Portada" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;"></td>
                        <td>LIB001</td>
                        <td>Cien Años de Soledad</td>
                        <td>$250.00</td>
                        <td>15</td>
                        <td>
                            <a class="text-red" style="text-decoration: none; cursor: pointer;">Editar</a> | 
                            <a class="text-gray" style="text-decoration: none; cursor: pointer;">Desactivar</a> |
                            <button class="btn-secondary w-auto" style="padding: 5px 10px; font-size: 13px;">Comprar Stock</button>
                        </td>
                    </tr>
                    <tr class="error-row">
                        <td><img src="assets/img/logo-maria-de-letras_icon.svg" alt="Portada" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px;"></td>
                        <td>LIB002</td>
                        <td>El Principito</td>
                        <td>$150.00</td>
                        <td>
                            3 <span class="text-danger font-bold">(Bajo)</span>
                        </td>
                        <td>
                            <a class="text-red" style="text-decoration: none; cursor: pointer;">Editar</a> | 
                            <a class="text-gray" style="text-decoration: none; cursor: pointer;">Desactivar</a> |
                            <button class="btn-secondary w-auto" style="padding: 5px 10px; font-size: 13px;">Comprar Stock</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="js/main.js"></script>
  </body>
</html>