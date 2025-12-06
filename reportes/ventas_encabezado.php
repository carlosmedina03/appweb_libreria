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
require_once '../includes/security_guardr.php';

// 1. Fechas por defecto (Mes actual si no envían nada)
$fecha_ini = $_GET['inicio'] ?? date('Y-m-01 00:00:00');
$fecha_fin = $_GET['fin'] ?? date('Y-m-t 23:59:59');
$filtro_cajero = isset($_GET['cajero']) ? intval($_GET['cajero']) : 0;

// BACKEND: Obtener lista de cajeros para el filtro
$res_cajeros = $mysqli->query("SELECT id, nombre_completo FROM usuarios WHERE activo = 1 ORDER BY nombre_completo");
$cajeros = [];
while($row = $res_cajeros->fetch_assoc()) {
    $cajeros[] = $row;
}


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
            <button type="button" class="btn w-150" onclick="window.print()">
                Imprimir / PDF
            </button>
            <button type="button" class="btn w-150">
                Exportar CSV
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