<?php
// dashboard.php
// 1. Incluimos el guard básico que verifica que exista sesión
require_once 'includes/seguridad_basica.php';

// 2. Extraemos datos para usar en el HTML
$usuario = $_SESSION['user'];
$nombre_usuario = $usuario['nombre'];
$rol = $usuario['rol'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/png" href="assets/img/logo-maria-de-letras_icon.svg">
    <style>
        /* Estilos rápidos para el grid del dashboard */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }
        .welcome-banner {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 5px solid #C82B1D;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="assets/img/logo-maria-de-letras_v2.svg" alt="Logo">
        </div>
        <div class="navbar-menu">
            <span>Hola, <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong> (<?php echo ucfirst($rol); ?>)</span>
            <a href="includes/logout.php" style="background-color: #333; padding: 5px 10px; border-radius: 4px; color: white; text-decoration: none;">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container" style="max-width: 1000px; margin-top: 20px;">
        
        <div class="welcome-banner">
            <h2>Panel de Control</h2>
            <p>Bienvenido al sistema de gestión.</p>
        </div>

        <div class="dashboard-grid">
            
            <div class="card">
                <h3>Punto de Venta</h3>
                <p>Realizar ventas, cobrar y emitir tickets.</p>
                <a href="ventas.php" class="btn w-full" style="display:block; text-align:center;">Ir a Caja</a>
            </div>

            <div class="card">
                <h3>Devoluciones</h3>
                <p>Gestionar devoluciones de productos.</p>
                <a href="devoluciones.php" class="btn-secondary w-full" style="display:block; text-align:center;">Ir a Devoluciones</a>
            </div>

            <?php if ($rol === 'admin'): ?>
                
                <div class="card admin-panel" style="grid-column: 1 / -1; background-color: #fff8f8; border: 1px solid #eec;">
                    <h3 style="color: #C82B1D;">Administración Global</h3>
                    <p style="font-size: 0.9em; color: #666;">Zona restringida para gestión del negocio.</p>
                    <hr class="mb-15" style="border-top: 1px solid #eee; margin: 15px 0;">
                    
                    <ul style="list-style-type: none; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <li>
                            <a href="usuarios.php" class="btn w-full">Gestionar Usuarios</a>
                        </li>
                        <li>
                            <a href="productos.php" class="btn w-full">Catálogo de Libros</a>
                        </li>
                        <li>
                            <a href="compras.php" class="btn w-full">Registrar Compras</a>
                        </li>
                        <li>
                            <a href="reportes/inventario.php" class="btn-secondary w-full">Reporte Inventario</a>
                        </li>
                        <li>
                            <a href="reportes/ventas_encabezado.php" class="btn-secondary w-full">Reporte Ventas</a>
                        </li>
                    </ul>
                </div>

            <?php endif; // Fin del bloque admin ?>

        </div>
    </div>

</body>
</html>