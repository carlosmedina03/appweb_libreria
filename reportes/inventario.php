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
require_once '../includes/auth.php'; // Protegido

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
<div class="card filtros-print" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 10px;">Filtros de Inventario</h3>
    <form action="" method="GET"> 
        <div style="display: flex; gap: 20px; align-items: flex-end;">
            
            <div style="flex: 2;">
                <label for="q">Buscar (Código / Nombre)</label>
                <input type="text" id="q" name="q" placeholder="Ej: LIB001 o Cien Años"
                       value="<?php echo htmlspecialchars($filtro_q); ?>" 
                       style="width: 100%; padding: 8px;">
            </div>
            
            <div style="flex: 1; display: flex; align-items: center; padding-bottom: 5px;">
                <input type="checkbox" id="activos" name="activos" 
                       <?php echo $solo_activos ? 'checked' : ''; ?>
                       style="margin-right: 5px;">
                <label for="activos">Solo Activos</label>
            </div>
            
            <button type="submit" style="width: 150px; padding: 10px;">
                Aplicar Filtro
            </button>
            
            <button type="button" class="btn-secondary" onclick="window.print()" style="width: 150px; padding: 10px;">
                Imprimir / PDF
            </button>
        </div>
    </form>
</div>

<div class="card">
    <p style="font-size: 0.9em; font-weight: bold;">
        Total de Productos Encontrados: <?php echo $total_items; ?>
    </p>
    
    <table>
        <thead>
            <tr style="background: #3498db; color: white;">
                <th style="width: 120px;">Código</th>
                <th>Nombre del Producto</th>
                <th style="width: 100px; text-align: right;">Precio Venta</th>
                <th style="width: 100px; text-align: right;">Existencia</th>
                <th style="width: 80px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_items > 0): ?>
                <?php foreach ($productos as $p): ?>
                    <tr> 
                        <td><?php echo htmlspecialchars($p['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td style="text-align: right;">$<?php echo number_format($p['precio'], 2); ?></td>
                        <td style="text-align: right; font-weight: bold;"><?php echo number_format($p['existencia'], 0); ?></td>
                        <td><?php echo htmlspecialchars($p['estado_str']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center;">No se encontraron productos con los filtros seleccionados.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold; background: #f0f0f0;">
                    TOTAL UNIDADES EN EXISTENCIA
                </td>
                <td style="font-weight: bold; background: #f0f0f0; text-align: right;">
                    <?php echo number_format($total_existencias, 0); ?>
                </td>
                <td style="background: #f0f0f0;"></td> </tr>
        </tfoot>
    </table>
</div>

<?php
$contenido_reporte = ob_get_clean(); // Guarda el buffer en la variable
require_once 'plantilla.php'; // Asume que plantilla.php generará el HTML final
?>