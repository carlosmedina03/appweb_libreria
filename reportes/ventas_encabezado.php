<?php
// ============================================================
// RESPONSABLE: Vista de Reporte de Ventas por Encabezado (Tickets) con filtros.
// REQUERIMIENTO: "3.2 Ventas por rango... Columnas: Folio, Fecha, Cajero..."
// ============================================================ 
// 1. Ejecutar Query 4 de consultas_base.sql.
// 2. Totales al final: Importe facturado, Número de tickets, Ticket promedio.

// BACKEND ABAJO (NO BORRAR)f
// REQUERIMIENTO: "Filtros obligatorios: fecha inicio, fecha fin"
// REQUERIMIENTO: "Totales al final: Importe facturado, Número tickets, Promedio"
// ---------------------------------------------------------

require_once '../config/db.php';
require_once '../includes/security_guardr.php';

// 1. Fechas por defecto (Mes actual si no envían nada)
$fecha_ini_input = $_GET['inicio'] ?? date('Y-m-01');
$fecha_fin_input = $_GET['fin'] ?? date('Y-m-d');
$filtro_cajero = isset($_GET['cajero']) ? intval($_GET['cajero']) : 0;

// Se ajusta para usar el formato de base de datos en el SQL
$fecha_ini_db = $fecha_ini_input . ' 00:00:00';
$fecha_fin_db = $fecha_fin_input . ' 23:59:59';


// BACKEND: Obtener lista de cajeros para el filtro
$res_cajeros = $mysqli->query("SELECT id, nombre_completo FROM usuarios WHERE activo = 1 ORDER BY nombre_completo");
$cajeros = [];
while($row = $res_cajeros->fetch_assoc()) {
    $cajeros[] = $row;
}


// 2. Query (Basado en Consultas Base 3.2: Ventas por Encabezado)
$sql = "SELECT v.id as folio, v.fecha_hora, u.nombre_completo as cajero, v.subtotal, v.iva, v.total 
        FROM ventas v 
        JOIN usuarios u ON v.id_usuario = u.id 
        WHERE v.fecha_hora BETWEEN '$fecha_ini_db' AND '$fecha_fin_db'"; 

if ($filtro_cajero > 0) {
    $sql .= " AND v.id_usuario = $filtro_cajero";
}

$sql .= " ORDER BY v.fecha_hora DESC";

$resultado = $mysqli->query($sql);

// 3. Preparar Dataset y Calcular Totales
$ventas = [];
$suma_subtotal = 0;
$suma_iva = 0;
$suma_total_facturado = 0;

while ($row = $resultado->fetch_assoc()) {
    $suma_subtotal += $row['subtotal'];
    $suma_iva += $row['iva'];
    $suma_total_facturado += $row['total'];
    $ventas[] = $row;
}

$num_tickets = count($ventas);
// Cálculo del ticket promedio
$ticket_promedio = ($num_tickets > 0) ? ($suma_total_facturado / $num_tickets) : 0;
?>

<?php
$titulo_reporte = "REPORTE DE VENTAS POR ENCABEZADO (TICKETS)";
ob_start();
?>

<div class="card filtros-print mb-20">
    <h3 class="mb-15">Filtros de Ventas por Período</h3>
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
                <label for="cajero">Cajero (Opcional)</label>
                <select id="cajero" name="cajero" class="filter-input">
                    <option value="0">--- Todos los Cajeros ---</option>
                    <?php foreach ($cajeros as $cajero): ?>
                    <option value="<?php echo $cajero['id']; ?>" <?php if ($cajero['id'] == $filtro_cajero) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cajero['nombre_completo']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn-general w-150">
                Generar Reporte
            </button>
            <button type="button" class="btn-general w-150 btn-print">
                Imprimir / PDF
            </button>
            <?php 
            $csv_url = '../reportes/exportar.php?tipo=ventas' . 
                       '&inicio=' . urlencode($fecha_ini_input) . 
                       '&fin=' . urlencode($fecha_fin_input) .
                       '&cajero=' . urlencode($filtro_cajero);
            ?>
            <a href="<?php echo $csv_url; ?>" class="btn-general w-150">
                Exportar CSV
            </a>
        </div>
    </form>
</div>

<div class="card">
    <p class="font-bold text-sm">
        Total de Tickets Encontrados: **<?php echo $num_tickets; ?>**
    </p>
    
    <div class="table-responsive">
    <table>
        <thead>
            <tr class="bg-green"> 
                <th class="w-100">Folio</th>
                <th class="w-150">Fecha/Hora</th>
                <th>Cajero</th>
                <th class="w-120 text-right">Subtotal</th>
                <th class="w-100 text-right">IVA</th>
                <th class="w-120 text-right">Total Venta</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($ventas) > 0): ?>
                <?php foreach ($ventas as $venta): ?>
                <tr> 
                    <td><?php echo htmlspecialchars($venta['folio']); ?></td>
                    <td><?php echo date('d/m/Y H:i:s', strtotime($venta['fecha_hora'])); ?></td>
                    <td><?php echo htmlspecialchars($venta['cajero']); ?></td>
                    <td class="text-right">$<?php echo number_format($venta['subtotal'], 2); ?></td>
                    <td class="text-right">$<?php echo number_format($venta['iva'], 2); ?></td>
                    <td class="text-right font-bold">$<?php echo number_format($venta['total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No se encontraron ventas con los filtros aplicados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right font-bold bg-light-green">
                    **TOTAL FACTURADO**
                </td>
                <td class="text-right font-bold bg-light-green">
                    $<?php echo number_format($suma_total_facturado, 2); ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-right font-bold bg-light-gray">
                    **NÚMERO DE TICKETS**
                </td>
                <td class="text-right font-bold bg-light-gray">
                    <?php echo number_format($num_tickets, 0); ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-right font-bold bg-gray">
                    **TICKET PROMEDIO**
                </td>
                <td class="text-right font-bold bg-gray">
                    $<?php echo number_format($ticket_promedio, 2); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean();
require_once 'plantilla.php'; 
?>