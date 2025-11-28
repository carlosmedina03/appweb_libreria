<?php
// ============================================================
// RESPONSABLE: Rol 4 (Back-End)
// REQUERIMIENTO: "Exportación CSV (obligatoria)... Codificación UTF-8 con BOM"
// ============================================================
// 1. Recibir qué reporte se quiere exportar (inventario, ventas, etc).
// 2. header('Content-Type: text/csv; charset=utf-8');
// 3. header('Content-Disposition: attachment; filename=reporte_....csv');
// 4. echo "\xEF\xBB\xBF"; // BOM para Excel.
// 5. Imprimir datos separados por comas.
?>