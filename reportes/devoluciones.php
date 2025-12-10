<?php
// ============================================================
// RESPONSABLE: Vista de Reporte de Devoluciones con filtros.
// REQUERIMIENTO: "3.5 Devoluciones... Motivo, Cantidad devuelta..."
// ============================================================
// 1. Ejecutar Query 7 de consultas_base.sql.
// 2. Mostrar motivo de devolución.

// BACKEND ABAJO (NO BORRAR)

require_once '../config/db.php';
require_once '../includes/security_guardr.php';

// Variables de filtro para retener el valor en el input
$fecha_ini_input = $_GET['inicio'] ?? date('Y-m-01');
$fecha_fin_input = $_GET['fin'] ?? date('Y-m-d');
// Se ajusta para usar el formato de base de datos en el SQL
$fecha_ini_db = $fecha_ini_input . ' 00:00:00';
$fecha_fin_db = $fecha_fin_input . ' 23:59:59';


$sql = "SELECT d.fecha_hora, d.id_venta as folio_venta, l.codigo, l.titulo as nombre,
                dd.cantidad as cant_dev, dd.monto_reembolsado, d.motivo
        FROM devoluciones d
        JOIN detalle_devoluciones dd ON d.id = dd.id_devolucion
        JOIN libros l ON dd.id_libro = l.id
        WHERE d.fecha_hora BETWEEN '$fecha_ini_db' AND '$fecha_fin_db'
        ORDER BY d.fecha_hora DESC";

$res = $mysqli->query($sql);
$devoluciones = [];
$total_unidades_dev = 0;
$total_monto_dev = 0;

while ($row = $res->fetch_assoc()) {
    $total_unidades_dev += $row['cant_dev'];
    $total_monto_dev += $row['monto_reembolsado'];
    $devoluciones[] = $row;
}
?>
<?php
$titulo_reporte = "REPORTE DE DEVOLUCIONES";
ob_start();
?>

<div class="card filtros-print mb-20">
    <h3 class="mb-15">Filtros de Devoluciones</h3>
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
            
            <button type="submit" class="btn-general w-150">
                Generar Reporte
            </button>
            <button type="button" class="btn-general w-150 btn-print">
                Imprimir / PDF
            </button>

            <?php 
            // Esto llama a tu archivo exportar.php terminado
            $csv_url = '../reportes/exportar.php?tipo=devoluciones' . 
                       '&inicio=' . urlencode($fecha_ini_input) . 
                       '&fin=' . urlencode($fecha_fin_input);
            ?>
            <a href="<?php echo $csv_url; ?>" class="btn-general w-150">
                Exportar CSV
            </a>
        </div>
    </form>
</div>

<div class="card">
    <p class="font-bold text-sm">
        Total de Devoluciones (ítems): **<?php echo count($devoluciones); ?>**
    </p>
    
    <div class="table-responsive">
    <table>
        <thead>
            <tr class="bg-green"> 
                <th class="w-100">Folio Venta</th>
                <th class="w-150">Fecha Devolución</th>
                <th>Producto</th>
                <th class="w-100 text-center">Cant.</th>
                <th class="w-120 text-right">Monto Devuelto</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($devoluciones) > 0): ?>
                <?php foreach ($devoluciones as $dev): ?>
                <tr> 
                    <td><?php echo htmlspecialchars($dev['folio_venta']); ?></td>
                    <td><?php echo date('d/m/Y H:i:s', strtotime($dev['fecha_hora'])); ?></td>
                    <td><?php echo htmlspecialchars($dev['nombre']); ?></td>
                    <td class="text-center"><?php echo number_format($dev['cant_dev'], 0); ?></td>
                    <td class="text-right">$<?php echo number_format($dev['monto_reembolsado'], 2); ?></td>
                    <td><?php echo htmlspecialchars($dev['motivo']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No se encontraron devoluciones en el período seleccionado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right font-bold bg-light-green">
                    **TOTAL UNIDADES DEVUELTAS**
                </td>
                <td class="text-center font-bold bg-light-green">
                    <?php echo number_format($total_unidades_dev, 0); ?>
                </td>
                <td class="text-right font-bold bg-light-green">
                    $<?php echo number_format($total_monto_dev, 2); ?>
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean();
require_once 'plantilla.php';
?>