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
require_once '../includes/security_guardr.php';

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
                       value="2025-12-01" 
                       class="filter-input">
            </div>
            
            <div class="filter-group">
                <label for="fin">Fecha Fin</label>
                <input type="date" id="fin" name="fin" required 
                       value="2025-12-01" 
                       class="filter-input">
            </div>

            <div class="filter-group-large">
                <label for="producto">Producto (Opcional)</label>
                <input type="text" id="producto" name="producto" placeholder="Nombre del libro..." class="filter-input">
            </div>
            
            <button type="button" class="btn w-150">
                Generar Reporte
            </button>
            <button type="button" class="btn w-150" onclick="window.print()">
                Imprimir / PDF
            </button>
        </div>
    </form>
</div>

<div class="card">
    <p class="font-bold text-sm">
        Mostrando detalle del día 01/12/2025
    </p>
    
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
            <!-- Venta 1001 -->
            <tr> 
                <td>1001</td>
                <td>01/12/2025 10:30:00</td>
                <td>Cien Años de Soledad</td>
                <td class="text-center">1</td>
                <td class="text-right">$250.00</td>
                <td class="text-right">$250.00</td>
            </tr>
            
            <!-- Venta 1002 -->
            <tr> 
                <td>1002</td>
                <td>01/12/2025 11:15:00</td>
                <td>El Principito</td>
                <td class="text-center">2</td>
                <td class="text-right">$150.00</td>
                <td class="text-right">$300.00</td>
            </tr>

            <!-- Venta 1003 -->
            <tr> 
                <td>1003</td>
                <td>02/12/2025 09:45:00</td>
                <td>Rayuela</td>
                <td class="text-center">1</td>
                <td class="text-right">$200.00</td>
                <td class="text-right">$200.00</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right font-bold bg-light-green">
                    TOTAL VENTAS
                </td>
                <td class="text-right font-bold bg-light-green">
                    $750.00
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-right font-bold bg-light-gray">
                    TOTAL UNIDADES VENDIDAS
                </td>
                <td class="text-right font-bold bg-light-gray">
                    4
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean();
require_once 'plantilla.php';
?>