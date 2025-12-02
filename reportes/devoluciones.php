<?php
// ============================================================
// RESPONSABLE: Rol 2 (Diseño) y Rol 4 (Datos)  
// REQUERIMIENTO: "3.5 Devoluciones... Motivo, Cantidad devuelta..."
// ============================================================
// 1. Ejecutar Query 7 de consultas_base.sql.
// 2. Mostrar motivo de devolución.

// BACKEND ABAJO (NO BORRAR)
require_once '../config/db.php';
require_once '../includes/auth.php';

$fecha_ini = $_GET['inicio'] ?? date('Y-m-01 00:00:00');
$fecha_fin = $_GET['fin'] ?? date('Y-m-t 23:59:59');

$sql = "SELECT d.fecha_hora, d.id_venta as folio_venta, l.codigo, l.titulo as nombre,
               dd.cantidad as cant_dev, dd.monto_reembolsado, d.motivo
        FROM devoluciones d
        JOIN detalle_devoluciones dd ON d.id = dd.id_devolucion
        JOIN libros l ON dd.id_libro = l.id
        WHERE d.fecha_hora BETWEEN '$fecha_ini' AND '$fecha_fin'
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

        <div class="card filtros-print" style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 10px;">Filtros por Período</h3>
            <form action="devoluciones.php" method="GET">
                <div style="display: flex; gap: 20px; align-items: flex-end;">
                    
                    <div style="flex: 1;">
                        <label for="inicio">Fecha Inicio</label>
                        <input type="date" id="inicio" name="inicio" required 
                               value="<?php echo date('Y-m-d', strtotime($fecha_ini)); ?>" 
                               style="width: 100%;">
                    </div>
                    
                    <div style="flex: 1;">
                        <label for="fin">Fecha Fin</label>
                        <input type="date" id="fin" name="fin" required 
                               value="<?php echo date('Y-m-d', strtotime($fecha_fin)); ?>" 
                               style="width: 100%;">
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

        <!-- TABLA PRINCIPAL DEL REPORTE -->
        <div class="card">
            <p style="font-size: 0.9em; font-weight: bold;">
                Total de Devoluciones Encontradas: <?php echo count($devoluciones); ?>
            </p>
            
            <table>
                <thead>
                    <tr style="background: #e74c3c; color: white;">
                        <th style="width: 120px;">Fecha/Hora</th>
                        <th style="width: 80px;">Folio Venta</th>
                        <th style="width: 100px;">Código</th>
                        <th>Título del Libro</th>
                        <th style="width: 80px; text-align: right;">Cant. Dev.</th>
                        <th style="width: 120px; text-align: right;">Monto Reembolsado</th>
                        <th style="width: 250px;">Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($devoluciones) > 0): ?>
                        <?php foreach ($devoluciones as $d): ?>
                            <tr> 
                                <td><?php echo date('d/m/Y H:i', strtotime($d['fecha_hora'])); ?></td>
                                <td><?php echo htmlspecialchars($d['folio_venta']); ?></td>
                                <td><?php echo htmlspecialchars($d['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($d['nombre']); ?></td>
                                <td style="text-align: right;"><?php echo htmlspecialchars($d['cant_dev']); ?></td>
                                <td style="text-align: right; font-weight: bold;">$<?php echo number_format($d['monto_reembolsado'], 2); ?></td>
                                <td><?php echo htmlspecialchars($d['motivo']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center;">No se encontraron devoluciones en el período seleccionado.</td></tr>
                    <?php endif; ?>
                </tbody>
                <!-- Fila de Totales Requeridos -->
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold; background: #f0f0f0;">
                            TOTALES
                        </td>
                        <td style="font-weight: bold; background: #f0f0f0; text-align: right;">
                            <?php echo $total_unidades_dev; ?>
                        </td>
                        <td colspan="2" style="font-weight: bold; background: #f0f0f0; text-align: right;">
                            $<?php echo number_format($total_monto_dev, 2); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

<?php
$contenido_reporte = ob_get_clean(); // Guarda el buffer en la variable
require_once 'plantilla.php';
?>