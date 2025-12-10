<?php
// ... [CÓDIGO PHP DE BACKEND QUE RECUPERA LOS DATOS Y FILTROS] ...

// BACKEND ABAJO (NO BORRAR)
require_once '../config/db.php';
require_once '../includes/security_guardr.php';

// 1. Manejo de Filtros de Fechas y Proveedor
$fecha_ini_input = $_GET['inicio'] ?? date('Y-m-01');
$fecha_fin_input = $_GET['fin'] ?? date('Y-m-d');
$id_proveedor_filtro = $_GET['proveedor'] ?? 0;

// Se añaden las horas al filtro para la consulta SQL
$fecha_ini_db = $fecha_ini_input . ' 00:00:00';
$fecha_fin_db = $fecha_fin_input . ' 23:59:59';

// 2. Query de la Base de Datos (Con filtros aplicados para la tabla)
$sql = "SELECT c.id AS folio, c.fecha_hora, p.nombre AS proveedor, c.total_compra 
        FROM compras c 
        JOIN proveedores p ON c.id_proveedor = p.id
        WHERE c.fecha_hora BETWEEN '$fecha_ini_db' AND '$fecha_fin_db'"; 
        
if ($id_proveedor_filtro > 0) {
    $sql .= " AND c.id_proveedor = $id_proveedor_filtro";
}

$sql .= " ORDER BY c.fecha_hora DESC";


$res = $mysqli->query($sql);
$compras = [];
$total_comprado = 0;

while ($row = $res->fetch_assoc()) {
    $total_comprado += $row['total_compra'];
    $compras[] = $row;
}

// 3. Obtener la lista de proveedores para el Dropdown
$providers_res = $mysqli->query("SELECT id, nombre FROM proveedores WHERE estatus = 1 ORDER BY nombre");
$all_providers = [];
while ($p_row = $providers_res->fetch_assoc()) {
    $all_providers[] = $p_row;
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
                       value="<?php echo htmlspecialchars($fecha_ini_input); ?>" 
                       class="filter-input">
            </div>
            
            <div class="filter-group">
                <label for="fin">Fecha Fin</label>
                <input type="date" id="fin" name="fin" required 
                       value="<?php echo htmlspecialchars($fecha_fin_input); ?>" 
                       class="filter-input">
            </div>

            <div class="filter-group-large">
                <label for="proveedor">Proveedor (Opcional)</label>
                <select id="proveedor" name="proveedor" class="filter-input">
                    <option value="0">--- Todos los Proveedores ---</option>
                    <?php foreach ($all_providers as $proveedor): ?>
                        <option value="<?php echo $proveedor['id']; ?>"
                            <?php if ($proveedor['id'] == $id_proveedor_filtro) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($proveedor['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn-general w-150">
                Generar Reporte
            </button>
            
            <button type="button" class="btn-general w-150 btn-print">
                Imprimir / PDF
            </button>
            
            <?php 
            // Se pasan los filtros, aunque exportar.php no los use actualmente por tu restricción
            $csv_url = '../reportes/exportar.php?tipo=compras' . 
                       '&inicio=' . urlencode($fecha_ini_input) . 
                       '&fin=' . urlencode($fecha_fin_input) .
                       '&proveedor=' . urlencode($id_proveedor_filtro);
            ?>
            <a href="<?php echo $csv_url; ?>" class="btn-general w-150">
                Exportar CSV
            </a>
            
        </div>
    </form>
</div>

<div class="card">
    <p class="font-bold text-sm">
        Total de Órdenes Encontradas: **<?php echo count($compras); ?>**
    </p>
    
    <table>
        <thead>
            <tr class="bg-green"> 
                <th class="w-100">Folio</th>
                <th class="w-150">Fecha/Hora</th>
                <th>Proveedor</th>
                <th class="w-150 text-right">Total Compra</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($compras) > 0): ?>
                <?php foreach ($compras as $compra): ?>
                    <tr> 
                        <td><?php echo htmlspecialchars($compra['folio']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($compra['fecha_hora'])); ?></td>
                        <td><?php echo htmlspecialchars($compra['proveedor']); ?></td>
                        <td class="text-right">$<?php echo number_format($compra['total_compra'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No se encontraron compras en el período seleccionado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right font-bold bg-light-green">
                    TOTAL GASTADO
                </td>
                <td class="text-right font-bold bg-light-green">
                    $<?php echo number_format($total_comprado, 2); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean();
require_once 'plantilla.php';
?>