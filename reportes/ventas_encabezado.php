<?php
$titulo_reporte = "REPORTE DE VENTAS POR RANGO";
ob_start();
?>

<div class="card filtros-print mb-20">
    <h3 class="mb-15">Filtros de Ventas por Período</h3>
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

            <div class="filter-group-large">
                <label for="cajero">Cajero (Opcional)</label>
                <select id="cajero" name="cajero" class="filter-input">
                    <option value="0">--- Todos los Cajeros ---</option>
                    <option value="1">Juan Pérez</option>
                    <option value="2">María López</option>
                    <option value="3">Carlos Ruiz</option>
                </select>
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
        Total de Tickets Encontrados: 3
    </p>
    
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
            <tr> 
                <td>1001</td>
                <td>01/12/2025 10:30:00</td>
                <td>Juan Pérez</td>
                <td class="text-right">$215.52</td>
                <td class="text-right">$34.48</td>
                <td class="text-right font-bold">$250.00</td>
            </tr>
            <tr> 
                <td>1002</td>
                <td>01/12/2025 11:15:00</td>
                <td>María López</td>
                <td class="text-right">$258.62</td>
                <td class="text-right">$41.38</td>
                <td class="text-right font-bold">$300.00</td>
            </tr>
            <tr> 
                <td>1003</td>
                <td>02/12/2025 09:45:00</td>
                <td>Juan Pérez</td>
                <td class="text-right">$172.41</td>
                <td class="text-right">$27.59</td>
                <td class="text-right font-bold">$200.00</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right font-bold bg-light-green">
                    TOTAL FACTURADO
                </td>
                <td class="text-right font-bold bg-light-green">
                    $750.00
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-right font-bold bg-light-gray">
                    NÚMERO DE TICKETS
                </td>
                <td class="text-right font-bold bg-light-gray">
                    3
                </td>
            </tr>
            <tr>
                <td colspan="5" class="text-right font-bold bg-gray">
                    TICKET PROMEDIO
                </td>
                <td class="text-right font-bold bg-gray">
                    $250.00
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean();
require_once 'plantilla.php';
?>