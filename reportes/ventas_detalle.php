<?php
// ============================================================
// RESPONSABLE: Rol 2 (Diseño) y Rol 4 (Datos)
// REQUERIMIENTO: "3.3 Detalle de ventas... Columnas: Fecha, Folio, Código..."
// ============================================================
// 1. Ejecutar Query 5 de consultas_base.sql.
// 2. Totales al final: Unidades vendidas, Importe total.

// BACKEND ABAJO (NO BORRAR)    
// REQUERIMIENTO: "3.3 Detalle de ventas... Columnas: ..., Importe línea"
// ---------------------------------------------------------
require_once '../config/db.php';
require_once '../includes/auth.php';

$fecha_ini = $_GET['inicio'] ?? date('Y-m-01 00:00:00');
$fecha_fin = $_GET['fin'] ?? date('Y-m-t 23:59:59');

// Query (Basado en Consultas Base 3.3)
$sql = "SELECT v.fecha_hora, v.id as folio, l.codigo, l.titulo as nombre, 
               dv.cantidad, dv.precio_unitario, dv.importe as importe_linea
        FROM detalle_ventas dv
        JOIN ventas v ON dv.id_venta = v.id
        JOIN libros l ON dv.id_libro = l.id
        WHERE v.fecha_hora BETWEEN '$fecha_ini' AND '$fecha_fin'
        ORDER BY v.fecha_hora DESC";

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

<div class="card filtros-print" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 10px;">Filtros: Detalle de Ventas por Período</h3>
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
        Líneas de Productos Vendidas Encontradas: <?php echo $total_lineas; ?>
    </p>
    
    <table>
        <thead>
            <tr style="background: #e67e22; color: white;"> <th style="width: 120px;">Fecha/Hora</th>
                <th style="width: 80px;">Folio</th>
                <th style="width: 100px;">Código</th>
                <th>Producto Vendido</th>
                <th style="width: 80px; text-align: right;">Cant.</th>
                <th style="width: 120px; text-align: right;">Precio Unitario</th>
                <th style="width: 120px; text-align: right;">Importe Línea</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_lineas > 0): ?>
                <?php foreach ($detalles as $d): ?>
                    <tr> 
                        <td><?php echo date('d/m/Y H:i', strtotime($d['fecha_hora'])); ?></td>
                        <td><?php echo htmlspecialchars($d['folio']); ?></td>
                        <td><?php echo htmlspecialchars($d['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($d['nombre']); ?></td>
                        <td style="text-align: right;"><?php echo number_format($d['cantidad'], 0); ?></td>
                        <td style="text-align: right;">$<?php echo number_format($d['precio_unitario'], 2); ?></td>
                        <td style="text-align: right; font-weight: bold;">$<?php echo number_format($d['importe_linea'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align: center;">No se encontraron detalles de ventas en el período seleccionado.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold; background: #fae6d3;">
                    TOTAL UNIDADES VENDIDAS
                </td>
                <td style="font-weight: bold; background: #fae6d3; text-align: right;">
                    <?php echo number_format($suma_unidades, 0); ?>
                </td>
                <td style="background: #fae6d3;"></td> <td style="background: #fae6d3;"></td> </tr>
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold; background: #f0f0f0;">
                    IMPORTE TOTAL (SUMA LÍNEAS)
                </td>
                <td style="font-weight: bold; background: #f0f0f0; text-align: right;">
                    $<?php echo number_format($suma_importe, 2); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean(); // Guarda el buffer en la variable
require_once 'plantilla.php'; // Asume que plantilla.php generará el HTML final
?>