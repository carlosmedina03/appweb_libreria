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
                       value="2025-12-01" 
                       class="filter-input">
            </div>
            
            <div class="filter-group">
                <label for="fin">Fecha Fin</label>
                <input type="date" id="fin" name="fin" required 
                       value="2025-12-31" 
                       class="filter-input">
            </div>
            
            <button type="button" class="btn w-150">
                Generar Reporte
            </button>
            <button type="button" class="btn-secondary w-150" onclick="window.print()">
                Imprimir / PDF
            </button>
        </div>
    </form>
</div>

<div class="card">
    <p class="font-bold text-sm">
        Total de Devoluciones: 1
    </p>
    
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
            <tr> 
                <td>1001</td>
                <td>01/12/2025 14:00:00</td>
                <td>Cien Años de Soledad</td>
                <td class="text-center">1</td>
                <td class="text-right">$250.00</td>
                <td>Defecto de fábrica</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right font-bold bg-light-green">
                    TOTAL DEVUELTO
                </td>
                <td class="text-right font-bold bg-light-green">
                    $250.00
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