<?php
// ============================================================
// RESPONSABLE: Rol 2 (Maquetación A4) y Rol 4 (Datos)
// REQUERIMIENTO: "Reporte 3.1 Inventario actual... Filtros: q, solo activos"
// ============================================================
// 1. Usar Query 3 de consultas_base.sql.
// 2. Mostrar tabla con encabezados que resalten y líneas zebra.
// 3. Numeración de página en pie.

// BACKEND ABAJO (NO BORRAR)
// REQUERIMIENTO: "Filtros obligatorios: q (código/nombre), solo activos"
// ---------------------------------------------------------
require_once '../config/db.php';
require_once '../includes/security_guardr.php';

// 1. Recibir Filtros
$filtro_q = isset($_GET['q']) ? $mysqli->real_escape_string($_GET['q']) : '';
$solo_activos = isset($_GET['activos']) ? true : false;

// 2. Construir Query (Basado en Consultas Base 3.1)
$sql = "SELECT l.codigo, l.titulo as nombre, l.precio_venta as precio, e.cantidad as existencia, l.estatus 
        FROM libros l 
        JOIN existencias e ON l.id = e.id_libro 
        WHERE 1=1";

if ($filtro_q != '') {
    $sql .= " AND (l.codigo LIKE '%$filtro_q%' OR l.titulo LIKE '%$filtro_q%')";
}
if ($solo_activos) {
    $sql .= " AND l.estatus = 1";
}

$sql .= " ORDER BY l.titulo";

// 3. Ejecutar y preparar Dataset
$resultado = $mysqli->query($sql);
$productos = [];
$total_existencias = 0;

while ($row = $resultado->fetch_assoc()) {
    $row['estado_str'] = ($row['estatus'] == 1) ? 'ACTIVO' : 'INACTIVO';
    $total_existencias += $row['existencia'];
    $productos[] = $row;
}

$total_items = count($productos);

// AHORA EL ROL 2 (UX) USARÁ $productos, $total_items y $total_existencias EN EL HTML   
?>
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
            <button type="button" class="btn w-150" onclick="window.print()">
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