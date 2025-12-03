<?php
// devoluciones.php

// 1. SEGURIDAD (Rol 5)
// Usamos security_guard.php que valida sesión activa.
// Nota: Tanto Admin como Operador pueden hacer devoluciones, así que no expulsamos al operador.
require_once 'includes/seguridad_basica.php';
require_once 'config/db.php';

// 2. Variable para el Navbar
$rol = $_SESSION['user']['rol'];

$venta_encontrada = null;
$detalles_venta = [];
$mensaje_error = "";
$mensaje_exito = "";

// LÓGICA DE BÚSQUEDA (Backend)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folio_input'])) {
    $folio_busqueda = $mysqli->real_escape_string($_POST['folio_input']); // Sanitizar entrada

    // 1. Buscar encabezado de venta
    // Asegurarse de usar nombres de tablas correctos (ventas vs ventas_encabezado)
    $sql_v = "SELECT v.id, v.fecha, v.total, u.nombre_completo as cajero 
              FROM ventas v 
              JOIN usuarios u ON v.usuario_id = u.id 
              WHERE v.folio = '$folio_busqueda' OR v.id = '$folio_busqueda'";
    
    $res_v = $mysqli->query($sql_v);

    if ($res_v && $res_v->num_rows > 0) {
        $venta_encontrada = $res_v->fetch_assoc();
        $id_venta_encontrada = $venta_encontrada['id'];

        // 2. Buscar detalles
        // JOIN con items para saber nombre y código
        $sql_d = "SELECT dv.item_id, dv.cantidad, dv.precio_unitario, dv.importe, i.nombre, i.codigo 
                  FROM ventas_det dv 
                  JOIN items i ON dv.item_id = i.id 
                  WHERE dv.venta_id = $id_venta_encontrada";
        
        $res_d = $mysqli->query($sql_d);
        
        while ($row = $res_d->fetch_assoc()) {
            $detalles_venta[] = $row;
        }
    } else {
        $mensaje_error = "Folio de venta '$folio_busqueda' no encontrado.";
    }
}
?>

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
        <img src="assets/img/logo-maria-de-letras_v2.svg" alt="Logo">
      </div>
      <div class="navbar-menu">
        <a href="ventas.php">Punto de ventas</a>
        
        <?php if ($rol === 'admin'): ?>
            <a href="compras.php">Compras</a>
            <a href="devoluciones.php">Devoluciones</a>
            <a href="usuarios.php">Usuarios</a>
            <a href="productos.php">Productos</a>
            <a href="reportes/inventario.php">Reportes</a>
        <?php else: ?>
            <a href="devoluciones.php">Devoluciones</a>
        <?php endif; ?>
        
        <a href="includes/logout.php" style="background: #333; color: white;">Salir</a>
      </div>
    </div>

    <div class="container main-content-small">
        <h2>Gestión de Devoluciones</h2>

        <?php if (!empty($mensaje_error)): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                <?php echo htmlspecialchars($mensaje_error); ?>
            </div>
        <?php endif; ?>

        <div class="card mb-20">
            <h3>Buscar Venta por Folio</h3>
            <form method="POST" action="devoluciones.php">
                <div class="flex-row">
                    <input type="text" 
                        id="folio_input" 
                        name="folio_input" 
                        placeholder="Ingresa Folio (Ej: V-00001)" 
                        required 
                        class="flex-grow w-auto"
                        value="<?php echo isset($_POST['folio_input']) ? htmlspecialchars($_POST['folio_input']) : ''; ?>">
                    <button type="submit" class="btn w-150">Buscar Venta</button>
                </div>
            </form>
        </div>

        <?php if ($venta_encontrada): ?>
        <div class="card mt-20">
            <h3>Venta Encontrada (#<?php echo htmlspecialchars($venta_encontrada['id']); ?>)</h3>
            <p>
                Fecha: <strong><?php echo $venta_encontrada['fecha']; ?></strong> | 
                Total Venta: <strong>$<?php echo number_format($venta_encontrada['total'], 2); ?></strong> | 
                Cajero: <strong><?php echo htmlspecialchars($venta_encontrada['cajero']); ?></strong>
            </p>
            <hr>
            
            <form id="form-devolucion">
                <input type="hidden" id="venta_id_origen" value="<?php echo $venta_encontrada['id']; ?>">

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
                        <?php foreach ($detalles_venta as $item): ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" class="check-devolucion" data-id="<?php echo $item['item_id']; ?>">
                            </td>
                            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                            <td style="text-align: center;"><?php echo $item['cantidad']; ?></td>
                            <td style="text-align: center;">
                                <input type="number" 
                                    class="input-cant-dev"
                                    id="cant_<?php echo $item['item_id']; ?>"
                                    min="1" 
                                    max="<?php echo $item['cantidad']; ?>" 
                                    value="1" 
                                    disabled
                                    style="width: 60px; padding: 5px; text-align: center;">
                            </td>
                            <td style="text-align: right;">$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <button type="button" id="btn-procesar-devolucion" class="btn mt-15" style="background-color: #ffc107; color: #000;">
                    Procesar Devolución Seleccionada
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="js/main.js"></script>
    <script>
        // Pequeño script para habilitar el input numérico solo si se marca el checkbox
        document.querySelectorAll('.check-devolucion').forEach(check => {
            check.addEventListener('change', function() {
                const id = this.getAttribute('data-id');
                const input = document.getElementById('cant_' + id);
                input.disabled = !this.checked;
                if (!this.checked) input.value = 1;
            });
        });

        document.getElementById('btn-procesar-devolucion')?.addEventListener('click', function() {
            if (confirm('¿Está seguro de procesar esta devolución? El stock será restaurado.')) {
                alert('Lógica AJAX pendiente (Rol 4).');
            }
        });
    </script>
  </body>
</html>