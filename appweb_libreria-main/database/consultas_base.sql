-- ==========================================================
-- ARCHIVO: consultas_base.sql
-- DEMUESTRA EL ACCESO A DATOS PARA FUNCIONALIDADES CLAVE
-- ==========================================================

USE libreria_db;

-- 1. Búsqueda y Paginación (Requerido para el Buscador)
-- 1.A Contar total de resultados (El Backend usa esto para saber cuántas páginas hay)
SELECT COUNT(*) as total 
FROM libros 
WHERE titulo LIKE '%bases%' OR codigo LIKE 'A101';

-- 1.B Obtener los registros de la página actual (LIMIT / OFFSET)
SELECT 
    id, 
    codigo, 
    titulo, 
    precio_venta
FROM 
    libros
WHERE 
    titulo LIKE '%bases%' OR codigo LIKE 'A101'
ORDER BY 
    titulo
LIMIT 
    10 OFFSET 0;

-- 2. Miniatura por ítem (para mostrar la imagen en el catálogo)
SELECT 
    id_libro, 
    contenido, 
    tipo_mime 
FROM 
    imagenes_libro 
WHERE 
    id_libro = 1 AND es_principal = 1
LIMIT 1;

-- 3. Resumen de existencias (Reporte de Inventario Actual)
-- Cumple con especificación PDF 3.1
SELECT 
    l.codigo,
    l.titulo AS nombre,
    l.precio_venta AS precio,
    e.cantidad AS existencia,
    CASE l.estatus 
        WHEN 1 THEN 'ACTIVO' 
        ELSE 'INACTIVO' 
    END AS estado
FROM 
    libros l
JOIN 
    existencias e ON l.id = e.id_libro
WHERE 
    l.estatus = 1
ORDER BY 
    l.titulo;

-- 4. Reporte de Ventas por fecha (Encabezados)
-- Cumple con especificación PDF 3.2
SELECT 
    v.id AS folio,
    v.fecha_hora,
    u.nombre_completo AS cajero,
    v.subtotal,
    v.iva,
    v.total
FROM 
    ventas v
JOIN 
    usuarios u ON v.id_usuario = u.id
WHERE 
    v.fecha_hora BETWEEN '2025-11-01 00:00:00' AND '2025-11-30 23:59:59'
ORDER BY 
    v.fecha_hora DESC;

-- 5. Detalle de Ventas por rango (Reporte PDF 3.3)
-- Columnas: Fecha, Folio, Código, Nombre, Cantidad, Precio Unit, Importe Línea
SELECT 
    v.fecha_hora AS fecha,
    v.id AS folio,
    l.codigo,
    l.titulo AS nombre,
    dv.cantidad,
    dv.precio_unitario,
    dv.importe AS importe_linea
FROM 
    detalle_ventas dv
JOIN 
    ventas v ON dv.id_venta = v.id
JOIN 
    libros l ON dv.id_libro = l.id
WHERE 
    v.fecha_hora BETWEEN '2025-11-01 00:00:00' AND '2025-11-30 23:59:59'
ORDER BY 
    v.fecha_hora DESC;

-- 6. Compras por rango (Reporte PDF 3.4)
-- Columnas: Folio, Fecha, Proveedor, Total
SELECT 
    c.id AS folio,
    c.fecha_hora AS fecha,
    p.nombre AS proveedor,
    c.total_compra AS total
FROM 
    compras c
JOIN 
    proveedores p ON c.id_proveedor = p.id
WHERE 
    c.fecha_hora BETWEEN '2025-11-01 00:00:00' AND '2025-11-30 23:59:59'
ORDER BY 
    c.fecha_hora DESC;

-- 7. Devoluciones por rango (Reporte PDF 3.5)
-- Columnas: Fecha, Folio Venta, Código, Nombre, Cantidad Devuelta, Importe, Motivo
SELECT 
    d.fecha_hora AS fecha,
    d.id_venta AS folio_venta,
    l.codigo,
    l.titulo AS nombre,
    dd.cantidad AS cantidad_devuelta,
    dd.monto_reembolsado AS importe_ajustado,
    d.motivo
FROM 
    devoluciones d
JOIN 
    detalle_devoluciones dd ON d.id = dd.id_devolucion
JOIN 
    libros l ON dd.id_libro = l.id
WHERE 
    d.fecha_hora BETWEEN '2025-11-01 00:00:00' AND '2025-11-30 23:59:59'
ORDER BY 
    d.fecha_hora DESC;