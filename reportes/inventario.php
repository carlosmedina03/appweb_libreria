<?php
$titulo_reporte = "REPORTE DE INVENTARIO ACTUAL";
ob_start();
?>

<div class="card filtros-print mb-20">
    <h3 class="mb-15">Filtros de Inventario</h3>
    <form action="" method="GET">
        <div class="filters-container">
            
            <div class="filter-group-large">
                <label for="busqueda">Buscar Producto</label>
                <input type="text" id="busqueda" name="busqueda" placeholder="Código o Título..." class="filter-input">
            </div>

            <div class="filter-group">
                <label for="stock">Estado de Stock</label>
                <select id="stock" name="stock" class="filter-input">
                    <option value="todos">Todos</option>
                    <option value="bajo">Stock Bajo</option>
                    <option value="agotado">Agotado</option>
                </select>
            </div>
            
            <button type="button" class="btn w-150">
                Filtrar
            </button>
            <button type="button" class="btn-secondary w-150" onclick="window.print()">
                Imprimir / PDF
            </button>
        </div>
    </form>
</div>

<div class="card">
    <p class="font-bold text-sm">
        Total de Productos: 3
    </p>
    
    <table>
        <thead>
            <tr class="bg-green"> 
                <th class="w-150">Código</th>
                <th>Título del Libro</th>
                <th class="w-120 text-right">Precio Venta</th>
                <th class="w-100 text-center">Stock Actual</th>
                <th class="w-150 text-center">Valor Inventario</th>
            </tr>
        </thead>
        <tbody>
            <tr> 
                <td>LIB001</td>
                <td>Cien Años de Soledad</td>
                <td class="text-right">$250.00</td>
                <td class="text-center">15</td>
                <td class="text-right">$3,750.00</td>
            </tr>
            <tr> 
                <td>LIB002</td>
                <td>El Principito</td>
                <td class="text-right">$150.00</td>
                <td class="text-center font-bold text-danger">3 (Bajo)</td>
                <td class="text-right">$450.00</td>
            </tr>
            <tr> 
                <td>LIB003</td>
                <td>Rayuela</td>
                <td class="text-right">$200.00</td>
                <td class="text-center">8</td>
                <td class="text-right">$1,600.00</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right font-bold bg-light-green">
                    VALOR TOTAL DEL INVENTARIO
                </td>
                <td class="text-right font-bold bg-light-green">
                    $5,800.00
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean();
require_once 'plantilla.php';
?>