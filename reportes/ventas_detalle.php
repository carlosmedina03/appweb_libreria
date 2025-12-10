<?php
// ============================================================
// RESPONSABLE: Vista de Reporte Detallado de Ventas con filtros.
// REQUERIMIENTO: "3.3 Detalle de ventas... Columnas: Fecha, Folio, Código..."
// ============================================================
// 1. Ejecutar Query 5 de consultas_base.sql.
// 2. Totales al final: Unidades vendidas, Importe total.

// BACKEND ABAJO (NO BORRAR)    
// REQUERIMIENTO: "3.3 Detalle de ventas... Columnas: ..., Importe línea"
// ---------------------------------------------------------
require_once '../config/db.php';
require_once '../includes/security_guardr.php';

// Variables de filtro para retener el valor en el input
$fecha_ini_input = $_GET['inicio'] ?? date('Y-m-01');
$fecha_fin_input = $_GET['fin'] ?? date('Y-m-d');
$filtro_producto = isset($_GET['producto']) ? $mysqli->real_escape_string($_GET['producto']) : ''; 

// Se ajusta para usar el formato de base de datos en el SQL
$fecha_ini_db = $fecha_ini_input . ' 00:00:00';
$fecha_fin_db = $fecha_fin_input . ' 23:59:59';


// Query (Basado en Consultas Base 3.3)
$sql = "SELECT v.fecha_hora, v.id as folio, l.codigo, l.titulo as nombre, 
                dv.cantidad, dv.precio_unitario, dv.importe as importe_linea
        FROM detalle_ventas dv
        JOIN ventas v ON dv.id_venta = v.id
        JOIN libros l ON dv.id_libro = l.id
        WHERE v.fecha_hora BETWEEN '$fecha_ini_db' AND '$fecha_fin_db'"; 

if ($filtro_producto != '') {
    $sql .= " AND (l.titulo LIKE '%$filtro_producto%' OR l.codigo LIKE '%$filtro_producto%')";
}

$sql .= " ORDER BY v.fecha_hora DESC";


$resultado = $mysqli->query($sql);

$detalles = [];
$suma_unidades = 0;
$suma_importe = 0;

while ($row = $resultado->fetch_assoc()) {
    $suma_unidades += $row['cantidad'];
    $suma_importe += $row['importe_linea'];
    $detalles[] = $row;
}
?>
<?php
$titulo_reporte = "REPORTE DETALLADO DE VENTAS";
ob_start();
?>

<div class="card filtros-print mb-20">
    <h3 class="mb-15">Filtros de Detalle</h3>
    <form action="" method="GET">
        <div class="filters-container">
            
            <div class="filter-group">
                <label for="inicio">Fecha Inicio</label>
                <input type="date" id="inicio" name="inicio" required 
                       value="<?php echo htmlspecialchars($fecha_ini_input); ?>" 
                       class="filter-input">
            </div>
            
            <div class="filter-group">
                <label for="fin">Fecha Fin</label>
                <input type="date" id="fin" name="fin" required 
                       value="<?php echo htmlspecialchars($fecha_fin_input); ?>" 
                       class="filter-input">
            </div>

            <div class="filter-group-large">
                <label for="producto">Producto (Opcional)</label>
                <input type="text" id="producto" name="producto" placeholder="Nombre o Código del libro..." class="filter-input"
                       value="<?php echo htmlspecialchars($filtro_producto); ?>">
            </div>
            
            <button type="submit" class="btn-general w-150">
                Generar Reporte
            </button>
            <button type="button" class="btn-general w-150 btn-print">
                Imprimir / PDF
            </button>
            <?php 
            // Esto llama a tu archivo exportar.php terminado
            $csv_url = '../reportes/exportar.php?tipo=detalle_ventas' . 
                       '&inicio=' . urlencode($fecha_ini_input) . 
                       '&fin=' . urlencode($fecha_fin_input) .
                       '&producto=' . urlencode($filtro_producto);
            ?>
            <a href="<?php echo $csv_url; ?>" class="btn-general w-150">
                Exportar CSV
            </a>
        </div>
    </form>
</div>

<div class="card">
    <p class="font-bold text-sm">
        Mostrando **<?php echo count($detalles); ?>** líneas de detalle.
    </p>
    
    <div class="table-responsive">
    <table>
        <thead>
            <tr class="bg-green"> 
                <th class="w-100">Folio</th>
                <th class="w-150">Fecha/Hora</th>
                <th>Producto</th>
                <th class="w-100 text-center">Cant.</th>
                <th class="w-120 text-right">Precio Unit.</th>
                <th class="w-120 text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($detalles) > 0): ?>
                <?php foreach ($detalles as $detalle): ?>
                <tr> 
                    <td><?php echo htmlspecialchars($detalle['folio']); ?></td>
                    <td><?php echo date('d/m/Y H:i:s', strtotime($detalle['fecha_hora'])); ?></td>
                    <td><?php echo htmlspecialchars($detalle['nombre']); ?></td>
                    <td class="text-center"><?php echo number_format($detalle['cantidad'], 0); ?></td>
                    <td class="text-right">$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                    <td class="text-right">$<?php echo number_format($detalle['importe_linea'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No se encontraron detalles de venta con los filtros aplicados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right font-bold bg-light-green">
                    **TOTAL IMPORTE (LÍNEAS)**
                </td>
                <td class="text-right font-bold bg-light-green">
                    $<?php echo number_format($suma_importe, 2); ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-right font-bold bg-light-gray">
                    **TOTAL UNIDADES VENDIDAS**
                </td>
                <td class="text-right font-bold bg-light-gray">
                    <?php echo number_format($suma_unidades, 0); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean();
require_once 'plantilla.php';
?>