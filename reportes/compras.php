<?php
// ============================================================
// RESPONSABLE: Rol 2 (Diseño) y Rol 4 (Datos)  
// REQUERIMIENTO: "3.4 Compras por rango... Columnas: Folio, Fecha, Proveedor..."
// ============================================================
// 1. Ejecutar Query 6 de consultas_base.sql.
// 2. Filtros obligatorios: Fecha inicio, Fecha fin.

// BACKEND ABAJO (NO BORRAR)
require_once '../config/db.php';
require_once '../includes/auth.php';

$fecha_ini = $_GET['inicio'] ?? date('Y-m-01 00:00:00');
$fecha_fin = $_GET['fin'] ?? date('Y-m-t 23:59:59');

$sql = "SELECT c.id as folio, c.fecha_hora, p.nombre as proveedor, c.total_compra 
        FROM compras c 
        JOIN proveedores p ON c.id_proveedor = p.id
        WHERE c.fecha_hora BETWEEN '$fecha_ini' AND '$fecha_fin'
        ORDER BY c.fecha_hora DESC";

$res = $mysqli->query($sql);
$compras = [];
$total_comprado = 0;

while ($row = $res->fetch_assoc()) {
    $total_comprado += $row['total_compra'];
    $compras[] = $row;
}
?>

        <div class="card filtros-print" style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 10px;">Filtros por Período</h3>
            <form action="compras.php" method="GET">
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
                Total de Órdenes de Compra Encontradas: <?php echo count($compras); ?>
            </p>
            
            <table>
                <thead>
                    <tr style="background: #3498db; color: white;">
                        <th style="width: 100px;">Folio</th>
                        <th style="width: 150px;">Fecha/Hora</th>
                        <th>Proveedor</th>
                        <th style="width: 150px; text-align: right;">Total Compra</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($compras) > 0): ?>
                        <?php foreach ($compras as $c): ?>
                            <tr> 
                                <td><?php echo htmlspecialchars($c['folio']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($c['fecha_hora'])); ?></td>
                                <td><?php echo htmlspecialchars($c['proveedor']); ?></td>
                                <td style="text-align: right; font-weight: bold;">$<?php echo number_format($c['total_compra'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center;">No se encontraron compras en el período seleccionado.</td></tr>
                    <?php endif; ?>
                </tbody>
                <!-- Fila de Totales Requeridos -->
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold; background: #f0f0f0;">
                            TOTAL COMPRADO
                        </td>
                        <td style="font-weight: bold; background: #f0f0f0; text-align: right;">
                            $<?php echo number_format($total_comprado, 2); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

<?php
$contenido_reporte = ob_get_clean(); // Guarda el buffer en la variable
require_once 'plantilla.php';
?>