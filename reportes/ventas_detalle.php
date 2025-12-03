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
            <button type="button" class="btn-secondary w-150" onclick="window.print()">
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