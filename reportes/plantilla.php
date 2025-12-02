<?php
// ============================================================
// RESPONSABLE: Rol 2 (UX-UI Impresión) 
// REQUERIMIENTO: "Cabecera del reporte (común)... Logo, Nombre negocio, Fecha gen"
// ============================================================
// Archivo para incluir en todos los reportes (header y footer).
// - Header: Logo izquierda, Título centrado.
// - Footer: "Página X de Y", Usuario que generó.
$usuario_gen = $_SESSION['user']['nombre_completo'] ?? 'Usuario Desconocido';
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte | <?php echo htmlspecialchars($titulo_reporte ?? 'Reporte'); ?></title>
    <link rel="stylesheet" href="../css/styles.css"> 
    <style>
        /* Estilos específicos para impresión A4, si se requiere PDF o impresión física */
        @media print {
            body { 
                margin: 0; 
                padding: 10mm; /* Espacio para el borde de la hoja A4 */
                font-size: 10pt;
            }
            .navbar, .filtros-print {
                display: none; /* Oculta navegación y controles de formulario */
            }
        }
    </style>
</head>

<body>
    <div class="navbar">
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

                <a href="includes/logout.php">Salir</a>
            </div>
        
        </div>
    </div>

    <div class="container" style="max-width: 1200px; margin-top: 20px;">
        <!-- CABECERA DE REPORTE (Común para todos) -->
        <div style="border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <img src="../assets/img/logo-maria-de-letras_icon.svg" alt="Logo" style="height: 50px;">
            <h1 style="text-align: center; margin: 0; flex-grow: 1; font-size: 1.8em;"><?php echo htmlspecialchars($titulo_reporte ?? 'REPORTE'); ?></h1>
            <div style="text-align: right; font-size: 0.9em;">
                <p style="margin: 0; font-weight: bold;"><?php echo htmlspecialchars($negocio['nombre_negocio'] ?? 'Librería María de Letras'); ?></p>
                <p style="margin: 0;">Fecha de Generación: <?php echo date('d/m/Y H:i:s'); ?></p>
            </div>
        </div>
        
        <!-- CONTENIDO ESPECÍFICO DEL REPORTE (Insertado desde el archivo principal) -->
        <?php echo $contenido_reporte; ?>

        <!-- PIE DE PÁGINA (Común para todos) -->
        <div style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 30px; font-size: 0.8em; display: flex; justify-content: space-between;">
            <p style="margin: 0;">Generado por: <?php echo htmlspecialchars($usuario_gen); ?></p>
            <p style="margin: 0;">Pagina 1 de 1 (Placeholder)</p> 
        </div>

    </div> <!-- Cierra el .container -->
</body>
</html>