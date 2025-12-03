<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Usuarios</title>
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

    <div class="container main-content">
        <div class="flex-between mb-15">
            <h2>Administración de Usuarios</h2>
            <button class="btn w-auto" onclick="alert('Abrir modal de creación (Simulado)')">+ Nuevo Usuario</button>
        </div>

        
        <div class="card">
            <h3>Listado de Empleados</h3>
            <table>
                <thead>
                    <tr>
                        <th class="col-5">ID</th>
                        <th class="col-25">Nombre Completo</th>
                        <th class="col-20">Usuario</th>
                        <th class="col-15">Rol</th>
                        <th class="col-15">Estatus</th>
                        <th class="col-20">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Administrador Principal</td>
                        <td>admin</td>
                        <td>Administrador</td>
                        <td>
                            <span class="text-green font-bold">Activo</span>
                        </td>
                        <td>
                            <a class="text-red" style="text-decoration: none; cursor: pointer;">Editar</a>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Juan Pérez</td>
                        <td>juanp</td>
                        <td>Operador</td>
                        <td>
                            <span class="text-green font-bold">Activo</span>
                        </td>
                        <td>
                            <a class="text-red" style="text-decoration: none; cursor: pointer;">Editar</a>
                            | <a class="text-gray" style="text-decoration: none; cursor: pointer;">Desactivar</a>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>María López</td>
                        <td>marial</td>
                        <td>Diseñador</td>
                        <td>
                            <span class="text-gray">Inactivo</span>
                        </td>
                        <td>
                            <a class="text-red" style="text-decoration: none; cursor: pointer;">Editar</a>
                            | <a class="text-gray" style="text-decoration: none; cursor: pointer;">Desactivar</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="js/main.js"></script>
  </body>
</html>