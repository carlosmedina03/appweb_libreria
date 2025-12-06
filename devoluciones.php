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
    $folio_id = intval($folio_busqueda);

    // 1. Buscar encabezado de venta
    $sql_v = "SELECT v.id, v.fecha_hora, v.total, u.nombre_completo as cajero 
              FROM ventas v 
              JOIN usuarios u ON v.id_usuario = u.id 
              WHERE v.id = '$folio_id'";
    
    $res_v = $mysqli->query($sql_v);

    if ($res_v && $res_v->num_rows > 0) {
        $venta_encontrada = $res_v->fetch_assoc();
        $id_venta_encontrada = intval($venta_encontrada['id']);

        // 2. Buscar detalles
        // JOIN con libros para saber nombre y código
        $sql_d = "SELECT dv.id_libro, dv.cantidad, dv.precio_unitario, dv.importe, l.titulo, l.codigo 
                  FROM detalle_ventas dv 
                  JOIN libros l ON dv.id_libro = l.id 
                  WHERE dv.id_venta = $id_venta_encontrada";
        
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

            <div class="dropdown">
                <button class="dropbtn">Cajero ▾</button>
                <div class="dropdown-content">
                    <a href="dashboard.php">Inicio</a>
                    <a href="ventas.php">Punto de Venta</a>
                    <a href="devoluciones.php">Devoluciones</a>
                </div>
            </div>
            
            <?php if (isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'admin'): ?>
                <div class="dropdown">
                    <button class="dropbtn">Gestion ▾</button>
                    <div class="dropdown-content">
                        <a href="productos.php">Productos</a>
                        <a href="compras.php">Compras</a>
                        <a href="usuarios.php">Usuarios</a>
                    </div>
                </div>

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
                
            <?php endif; ?>
            
            <a href="includes/logout.php" class="cerrar-sesion">Cerrar Sesión</a>
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
                        placeholder="Ingresa Folio (Ej: 1001)" 
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
                Fecha: <strong><?php echo date('d/m/Y H:i', strtotime($venta_encontrada['fecha_hora'])); ?></strong> | 
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
                                <input type="checkbox" class="check-devolucion" data-id="<?php echo $item['id_libro']; ?>">
                            </td>
                            <td><?php echo htmlspecialchars($item['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                            <td style="text-align: center;"><?php echo $item['cantidad']; ?></td>
                            <td style="text-align: center;">
                                <input type="number" 
                                    class="input-cant-dev"
                                    id="cant_<?php echo $item['id_libro']; ?>"
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
                <div class="mt-15">
                    <label for="motivo_devolucion">Motivo de la Devolución</label>
                    <input type="text" id="motivo_devolucion" name="motivo_devolucion" placeholder="Ej: Defecto de fábrica, cliente se arrepintió..." style="width: 100%;">
                </div>
                <div class="text-right">
                    <button type="button" id="btn-procesar-devolucion" class="btn mt-15" style="background-color: #c0392b;">
                        Procesar Devolución Seleccionada
                    </button>
                </div>
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

        const btnProcesar = document.getElementById('btn-procesar-devolucion');
        if (btnProcesar) {
            btnProcesar.addEventListener('click', async function() {
                const itemsADevolver = [];
                document.querySelectorAll('.check-devolucion:checked').forEach(check => {
                    const id = check.getAttribute('data-id');
                    const cantidadInput = document.getElementById('cant_' + id);
                    itemsADevolver.push({
                        id_libro: parseInt(id),
                        cantidad: parseInt(cantidadInput.value)
                    });
                });

                if (itemsADevolver.length === 0) {
                    alert('Debe seleccionar al menos un producto para devolver.');
                    return;
                }

                const idVenta = document.getElementById('venta_id_origen').value;
                const motivo = document.getElementById('motivo_devolucion').value;

                if (confirm('¿Está seguro de procesar esta devolución? El stock será restaurado.')) {
                    try {
                        const response = await fetch('ajax/confirmar_devolucion.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                id_venta: parseInt(idVenta),
                                items: itemsADevolver,
                                motivo: motivo
                            })
                        });
                        const resultado = await response.json();
                        if (resultado.status === 'ok') {
                            alert(`Devolución registrada con éxito. Folio de devolución: ${resultado.folio}\nSe abrirá el comprobante para imprimir.`);
                            window.open(`ticket.php?folio=${resultado.folio}&tipo=devolucion`, '_blank');
                            window.location.href = 'devoluciones.php'; // Recargar para limpiar
                        } else {
                            alert('Error: ' + resultado.msg);
                        }
                    } catch (error) {
                        console.error('Error al procesar devolución:', error);
                        alert('Ocurrió un error de conexión.');
                    }
                }
            });
        }
    </script>
  </body>
</html>
            }
        });
    </script>
  </body>
</html>