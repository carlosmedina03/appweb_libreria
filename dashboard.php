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
    <meta name="viewport" content="width=device-width, initial-scale=1">
            <div class="card">
                <h3>Punto de Venta</h3>
                <p>Realizar ventas y devoluciones</p>
                <a href="ventas.php" class="btn w-full">Ir a Caja</a>
            </div>

            <div class="card">
                <h3>Consultas</h3>
                <p>Buscar libros y precios</p>
                <a href="#" class="btn w-full">Buscador</a>
            </div>

            <!-- Admin Panel Section -->
            <div class="card admin-panel" style="grid-column: 1 / -1;">
                <h3>Administración</h3>
                <hr class="mb-15">
                <ul style="list-style-type: none; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                    <li><a href="usuarios.php" class="btn-secondary btn w-full">Gestionar Usuarios</a></li>
                    <li><a href="productos.php" class="btn-secondary btn w-full">Catálogo de Libros</a></li>
                    <li><a href="compras.php" class="btn-secondary btn w-full">Registrar Compras</a></li>
                    <li><a href="#" class="btn-secondary btn w-full">Reportes y Cierres</a></li>
                    <li><a href="#" class="btn-secondary btn w-full">Configuración del Ticket</a></li>
                </ul>
            </div>

        </div>
    </div>

</body>
</html>
                <h3>Punto de Venta</h3>
                <p>Realizar ventas y devoluciones</p>
                <a href="ventas.php" class="btn w-full">Ir a Caja</a>
            </div>

            <div class="card">
                <h3>Consultas</h3>
                <p>Buscar libros y precios</p>
                <a href="#" class="btn w-full">Buscador</a>
            </div>

            <!-- Admin Panel Section -->
            <div class="card admin-panel" style="grid-column: 1 / -1;">
                <h3>Administración</h3>
                <hr class="mb-15">
                <ul style="list-style-type: none; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                    <li><a href="usuarios.php" class="btn-secondary btn w-full">Gestionar Usuarios</a></li>
                    <li><a href="productos.php" class="btn-secondary btn w-full">Catálogo de Libros</a></li>
                    <li><a href="compras.php" class="btn-secondary btn w-full">Registrar Compras</a></li>
                    <li><a href="#" class="btn-secondary btn w-full">Reportes y Cierres</a></li>
                    <li><a href="#" class="btn-secondary btn w-full">Configuración del Ticket</a></li>
                </ul>
            </div>

        </div>
    </div>

</body>
</html>