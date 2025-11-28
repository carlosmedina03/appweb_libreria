<?php
session_start();
// dashboard.php

// 1. GUARD DE SEGURIDAD
if (!isset($_SESSION['user'])) {
    // Si no hay sesión, mandar al Login (asumo que tu login es auth.php o index.php)
    header('Location: includes/auth.php'); 
    exit;
}

// Extraemos datos
$usuario = $_SESSION['user'];
$rol = $usuario['rol'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema POS</title>
    </head>
<body>

    <nav style="background: #eee; padding: 10px; display: flex; justify-content: space-between;">
        <span>Bienvenido, <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong> (<?php echo strtoupper($rol); ?>)</span>
        <a href="includes/auth.php">Cerrar Sesión</a>
    </nav>

    <div class="container">
        <h1>Panel de Control</h1>

        <div class="grid-menu">
            
            <div class="card">
                <h3>Punto de Venta</h3>
                <p>Realizar ventas y devoluciones</p>
                <a href="ventas.php" class="btn">Ir a Caja</a>
            </div>

            <div class="card">
                <h3>Consultas</h3>
                <p>Buscar libros y precios</p>
                <a href="buscador.php" class="btn">Buscador</a>
            </div>

            <?php if ($rol === 'admin'): ?>
                
                <hr> <h3>Administración</h3>
                
                <div class="card admin-panel">
                    <ul>
                        <li><a href="usuarios.php">Gestionar Usuarios</a></li>
                        <li><a href="libros.php">Catálogo de Libros</a></li>
                        <li><a href="compras.php">Registrar Compras</a></li>
                        <li><a href="reportes.php">Reportes y Cierres</a></li>
                        <li><a href="configuracion.php">Configuración del Ticket</a></li>
                    </ul>
                </div>

            <?php endif; ?>
        </div>
    </div>

</body>
</html>