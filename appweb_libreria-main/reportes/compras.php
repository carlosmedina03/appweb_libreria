<?php
// ============================================================
// RESPONSABLE: Rol 2 (Diseño) y Rol 4 (Datos)  
// REQUERIMIENTO: "3.4 Compras por rango... Columnas: Folio, Fecha, Proveedor..."
// ============================================================
// 1. Ejecutar Query 6 de consultas_base.sql.
// 2. Filtros obligatorios: Fecha inicio, Fecha fin.

// BACKEND ABAJO (NO BORRAR)
require_once '../config/db.php';
require_once '../includes/security_guardr.php';

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

<?php
$titulo_reporte = "REPORTE DE COMPRAS POR PERÍODO";
ob_start();
?>

<div class="card filtros-print mb-20">
    <h3 class="mb-15">Filtros de Compras</h3>
    <form action="" method="GET">
        <div class="filters-container">
            
            <div class="filter-group">
                <label for="inicio">Fecha Inicio</label>
                <input type="date" id="inicio" name="inicio" required 
                       value="2025-11-01" 
                       class="filter-input">
            </div>
            
            <div class="filter-group">
                <label for="fin">Fecha Fin</label>
                <input type="date" id="fin" name="fin" required 
                       value="2025-12-02" 
                       class="filter-input">
            </div>

            <div class="filter-group-large">
                <label for="proveedor">Proveedor (Opcional)</label>
                <select id="proveedor" name="proveedor" class="filter-input">
                    <option value="0">--- Todos los Proveedores ---</option>
                    <option value="1">Editorial Planeta</option>
                    <option value="2">Penguin Random House</option>
                </select>
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
        Total de Órdenes Encontradas: 2
    </p>
    
    <table>
        <thead>
            <tr class="bg-green"> 
                <th class="w-100">ID Orden</th>
                <th class="w-150">Fecha Compra</th>
                <th>Proveedor</th>
                <th class="w-120 text-right">Total Compra</th>
                <th class="w-100 text-center">Items</th>
                <th class="w-120 text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            <tr> 
                <td>5001</td>
                <td>15/11/2025</td>
                <td>Editorial Planeta</td>
                <td class="text-right">$1,500.00</td>
                <td class="text-center">10</td>
                <td class="text-center font-bold text-green">RECIBIDO</td>
            </tr>
            <tr> 
                <td>5002</td>
                <td>20/11/2025</td>
                <td>Penguin Random House</td>
                <td class="text-right">$3,200.00</td>
                <td class="text-center">25</td>
                <td class="text-center font-bold text-green">RECIBIDO</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right font-bold bg-light-green">
                    TOTAL GASTADO
                </td>
                <td class="text-right font-bold bg-light-green">
                    $4,700.00
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean();
require_once 'plantilla.php';
?>