<?php
// ============================================================
// RESPONSABLE: Rol 2 (Diseño) y Rol 4 (Datos)
// REQUERIMIENTO: "3.2 Ventas por rango... Columnas: Folio, Fecha, Cajero..."
// ============================================================ 
// 1. Ejecutar Query 4 de consultas_base.sql.
// 2. Totales al final: Importe facturado, Número de tickets, Ticket promedio.

// BACKEND ABAJO (NO BORRAR)f
// REQUERIMIENTO: "Filtros obligatorios: fecha inicio, fecha fin"
// REQUERIMIENTO: "Totales al final: Importe facturado, Número tickets, Promedio"
// ---------------------------------------------------------
require_once '../config/db.php';
require_once '../includes/auth.php';

// 1. Fechas por defecto (Mes actual si no envían nada)
$fecha_ini = $_GET['inicio'] ?? date('Y-m-01 00:00:00');
$fecha_fin = $_GET['fin'] ?? date('Y-m-t 23:59:59');
$filtro_cajero = isset($_GET['cajero']) ? intval($_GET['cajero']) : 0;

// 2. Query (Basado en Consultas Base 3.2)
$sql = "SELECT v.id as folio, v.fecha_hora, u.nombre_completo as cajero, v.subtotal, v.iva, v.total 
        FROM ventas v 
        JOIN usuarios u ON v.id_usuario = u.id 
        WHERE v.fecha_hora BETWEEN '$fecha_ini' AND '$fecha_fin'";

if ($filtro_cajero > 0) {
    $sql .= " AND v.id_usuario = $filtro_cajero";
}

$sql .= " ORDER BY v.fecha_hora DESC";

$resultado = $mysqli->query($sql);

// 3. Preparar Dataset y Calcular Totales
$ventas = [];
$suma_total_facturado = 0;

while ($row = $resultado->fetch_assoc()) {
    $suma_total_facturado += $row['total'];
    $ventas[] = $row;
}

$num_tickets = count($ventas);
$ticket_promedio = ($num_tickets > 0) ? ($suma_total_facturado / $num_tickets) : 0;

// AHORA EL ROL 2 (UX) TIENE TODO LISTO PARA PINTAR LA TABLA
?>

<div class="card filtros-print" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 10px;">Filtros de Ventas por Período</h3>
    <form action="" method="GET">
        <div style="display: flex; gap: 20px; align-items: flex-end;">
            
            <div style="flex: 1;">
                <label for="inicio">Fecha Inicio</label>
                <input type="date" id="inicio" name="inicio" required 
                       value="<?php echo htmlspecialchars($fecha_ini_display); ?>" 
                       style="width: 100%; padding: 8px;">
            </div>
            
            <div style="flex: 1;">
                <label for="fin">Fecha Fin</label>
                <input type="date" id="fin" name="fin" required 
                       value="<?php echo htmlspecialchars($fecha_fin_display); ?>" 
                       style="width: 100%; padding: 8px;">
            </div>

            <div style="flex: 2;">
                <label for="cajero">Cajero (Opcional)</label>
                <select id="cajero" name="cajero" style="width: 100%; padding: 8px;">
                    <option value="0">--- Todos los Cajeros ---</option>
                    <?php foreach ($cajeros as $caj): ?>
                        <option value="<?php echo $caj['id']; ?>"
                                <?php echo ($filtro_cajero == $caj['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($caj['nombre_completo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" style="width: 150px; padding: 10px;">
                Generar Reporte
            </button>
            <button type="button" class="btn-secondary" onclick="window.print()" style="width: 150px; padding: 10px;">
                Imprimir / PDF
            </button>
        </div>
    </form>
</div>

<div class="card">
    <p style="font-size: 0.9em; font-weight: bold;">
        Total de Tickets Encontrados: <?php echo $num_tickets; ?>
    </p>
    
    <table>
        <thead>
            <tr style="background: #2ecc71; color: white;"> <th style="width: 100px;">Folio</th>
                <th style="width: 150px;">Fecha/Hora</th>
                <th>Cajero</th>
                <th style="width: 120px; text-align: right;">Subtotal</th>
                <th style="width: 100px; text-align: right;">IVA</th>
                <th style="width: 120px; text-align: right;">Total Venta</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($num_tickets > 0): ?>
                <?php foreach ($ventas as $v): ?>
                    <tr> 
                        <td><?php echo htmlspecialchars($v['folio']); ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($v['fecha_hora'])); ?></td>
                        <td><?php echo htmlspecialchars($v['cajero']); ?></td>
                        <td style="text-align: right;">$<?php echo number_format($v['subtotal'], 2); ?></td>
                        <td style="text-align: right;">$<?php echo number_format($v['iva'], 2); ?></td>
                        <td style="text-align: right; font-weight: bold;">$<?php echo number_format($v['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align: center;">No se encontraron ventas en el período seleccionado.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold; background: #e8f5e9;">
                    TOTAL FACTURADO
                </td>
                <td style="font-weight: bold; background: #e8f5e9; text-align: right;">
                    $<?php echo number_format($suma_total_facturado, 2); ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold; background: #f0f0f0;">
                    NÚMERO DE TICKETS
                </td>
                <td style="font-weight: bold; background: #f0f0f0; text-align: right;">
                    <?php echo number_format($num_tickets, 0); ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right; font-weight: bold; background: #e0e0e0;">
                    TICKET PROMEDIO
                </td>
                <td style="font-weight: bold; background: #e0e0e0; text-align: right;">
                    $<?php echo number_format($ticket_promedio, 2); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean(); // Guarda el buffer en la variable
require_once 'plantilla.php'; // Asume que plantilla.php generará el HTML final
?>