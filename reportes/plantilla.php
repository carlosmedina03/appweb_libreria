<?php
// ============================================================
// RESPONSABLE: Rol 2 (UX-UI Impresión)
// REQUERIMIENTO: "Cabecera del reporte (común)... Logo, Nombre negocio, Fecha gen"
// ============================================================
// Archivo para incluir en todos los reportes (header y footer).
// - Header: Logo izquierda, Título centrado.
// - Footer: "Página X de Y", Usuario que generó.
$usuario_gen = 'Administrador'; // Placeholder for static version
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de letras | Reportes</title>
    <link rel="stylesheet" href="../css/reportes.css"> 
</head>

<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="../assets/img/logo-maria-de-letras_v2.svg" alt="Logo de María de Letras">
        </div>
        <div class="navbar-menu">
            <a href="../ventas.php">Punto de ventas</a>
            <a href="../productos.php">Productos</a>
            <a href="../compras.php">Compras</a>
            <a href="../devoluciones.php">Devoluciones</a>
            <a href="../usuarios.php">Usuario</a>

            <a href="compras.php">Reportes compra</a>
            <a href="devoluciones.php">Reportes devoluciones</a>
            <a href="inventario.php">Reportes inventario</a>
            <a href="ventas_detalle.php">Reportes detalle</a>
            <a href="ventas_encabezado.php">Reportes encabezado</a>

            <a href="../index.php">Salir</a>
        </div>
    </div>

    <div class="container main-content-large">
        <!-- CABECERA DE REPORTE (Común para todos) -->
        <div class="report-header">
            <img src="../assets/img/logo-maria-de-letras_icon.svg" alt="Logo" style="height: 50px;">
            <h1 class="report-title"><?php echo htmlspecialchars($titulo_reporte ?? 'REPORTE'); ?></h1>
            <div class="report-meta">
                <p class="font-bold" style="margin: 0;">Librería María de Letras</p>
                <p style="margin: 0;">Fecha de Generación: <?php echo date('d/m/Y H:i:s'); ?></p>
            </div>
        </div>
        
        <!-- CONTENIDO ESPECÍFICO DEL REPORTE (Insertado desde el archivo principal) -->
        <?php echo $contenido_reporte; ?>

        <!-- PIE DE PÁGINA (Común para todos) -->
        <div class="report-footer">
            <p style="margin: 0;">Generado por: <?php echo htmlspecialchars($usuario_gen); ?></p>
            <p style="margin: 0;">Página 1 de 1</p> 
        </div>

    </div> <!-- Cierra el .container -->
</body>
</html>