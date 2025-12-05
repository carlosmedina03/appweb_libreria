<?php
// compras.php

// 1. SEGURIDAD (Rol 5)
// Este include hace dos cosas:
// a) Verifica que haya sesión.
// b) Verifica que el rol sea 'admin'. Si es operador, lo expulsa.
require_once 'includes/security_guard.php'; 

require_once 'config/db.php';

// 2. DEFINICIÓN DE VARIABLES PARA VISTA
// Necesario para que el Navbar sepa qué mostrar
$rol = $_SESSION['user']['rol']; 

// BACKEND (Lógica de Proveedores)
// Obtener lista de proveedores para el <select> del HTML
$proveedores = [];
// Verificamos que la conexión exista antes de consultar
if (isset($mysqli)) {
    $sql_prov = "SELECT id, nombre FROM proveedores WHERE estatus = 1 ORDER BY nombre";
    if ($res_prov = $mysqli->query($sql_prov)) {
        while ($row = $res_prov->fetch_assoc()) {
            $proveedores[] = $row;
        }
    }
}
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>María de Letras | Órdenes de Compra</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/png" href="assets/img/logo-maria-de-letras_icon.svg">
  </head>

  <body>
<div class="navbar">
        
        <div class="navbar-logo">
            <img src="assets/img/logo-maria-de-letras_v2.svg" alt="Logo">
        </div>

        <div class="navbar-menu">
            <a href="dashboard.php">Inicio</a>
            <a href="ventas.php">Punto de Venta</a>
            
            <?php if (isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'admin'): ?>
                <a href="productos.php">Productos</a>
                <a href="compras.php">Compras</a>
                <a href="devoluciones.php">Devoluciones</a>
                <a href="usuarios.php">Usuarios</a>

                <div class="dropdown">
                    <button class="dropbtn">Reportes ▾</button>
                    <div class="dropdown-content">
                        <a href="reportes/compras.php">Reportes Compra</a>
                        <a href="reportes/devoluciones.php">Reportes Devoluciones</a>
                        <a href="reportes/inventario.php">Reportes Inventario</a>
                        <a href="reportes/ventas_detalle.php">Reportes Detalle</a>
                        <a href="reportes/ventas_encabezado.php">Reportes Encabezado</a>
                    </div>
                </div>

            <?php else: ?>
                <a href="devoluciones.php">Devoluciones</a>
            <?php endif; ?>
            
            <a href="includes/logout.php" class="cerrar-sesion">Cerrar Sesión</a>
        </div>

    </div>

    <div class="container main-content">
        <h2>Registro de Orden de Compra</h2>

        <div class="card">
            <h3>Datos de la Compra</h3>
            <form id="form-compra-encabezado">
                <div class="grid-2">
                    <div>
                        <label for="fecha">Fecha de Pedido</label>
                        <input type="date" id="fecha" name="fecha" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <label for="proveedor">Proveedor</label>
                        <select id="proveedor" name="proveedor" required>
                            <option value="">-- Seleccione un proveedor --</option>
                            <?php foreach ($proveedores as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="card mt-20">
            <h3>Detalle de Productos a Comprar</h3>
            
            <div class="flex-row mb-15">
                <input type="text" 
                    id="input-producto-compra" 
                    placeholder="Buscar producto por título o código..." 
                    class="flex-grow w-auto">
                <button type="button" id="btn-agregar-item" class="btn w-150">Agregar Item</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="col-35">Producto</th>
                        <th class="col-15">Código</th>
                        <th class="col-15">Cantidad Pedida</th>
                        <th class="col-15">Costo Unitario</th>
                        <th class="col-10">Subtotal</th>
                        <th class="col-10"></th>
                    </tr>
                </thead>
                <tbody id="tabla-detalle-compra">
                    <tr>
                        <td colspan="6" style="text-align:center; color:#888;">Agrega productos para comenzar la orden</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="text-right text-xl font-bold mt-15">
                Total Compra: <span id="total-compra-display">$0.00</span>
            </div>

            <button id="btn-guardar-compra" class="btn mt-20">
                Guardar Orden de Compra
            </button>
        </div>
    </div>
    
    <script src="js/main.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputProducto = document.getElementById('input-producto-compra');
    const btnAgregar = document.getElementById('btn-agregar-item');
    const tablaDetalle = document.getElementById('tabla-detalle-compra');
    const totalDisplay = document.getElementById('total-compra-display');
    const btnGuardar = document.getElementById('btn-guardar-compra');
    const selectProveedor = document.getElementById('proveedor');

    let itemsCompra = {}; // Objeto para guardar los items de la compra

    // 1. Agregar item al presionar Enter o clic
    btnAgregar.addEventListener('click', buscarYAgregarProducto);
    inputProducto.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarYAgregarProducto();
        }
    });

    async function buscarYAgregarProducto() {
        const query = inputProducto.value.trim();
        if (!query) return;

        try {
            const response = await fetch(`ajax/buscar_producto.php?q=${query}`);
            const productos = await response.json();

            if (productos.length > 0) {
                const producto = productos[0]; // Tomamos el primer resultado
                if (!itemsCompra[producto.id]) { // Evitar duplicados
                    itemsCompra[producto.id] = {
                        id_libro: producto.id,
                        titulo: producto.titulo,
                        codigo: producto.codigo,
                        cantidad: 1,
                        costo: 0.00
                    };
                    renderizarTabla();
                }
                inputProducto.value = '';
            } else {
                alert('Producto no encontrado.');
            }
        } catch (error) {
            console.error('Error al buscar producto:', error);
        }
    }

    function renderizarTabla() {
        tablaDetalle.innerHTML = '';
        if (Object.keys(itemsCompra).length === 0) {
            tablaDetalle.innerHTML = '<tr><td colspan="6" style="text-align:center; color:#888;">Agrega productos para comenzar la orden</td></tr>';
            calcularTotal();
            return;
        }

        for (const id in itemsCompra) {
            const item = itemsCompra[id];
            const subtotal = (item.cantidad * item.costo).toFixed(2);
            const fila = `
                <tr data-id="${item.id_libro}">
                    <td>${item.titulo}</td>
                    <td>${item.codigo}</td>
                    <td><input type="number" class="input-cantidad" value="${item.cantidad}" min="1" style="width: 80px; text-align: center;"></td>
                    <td><input type="number" class="input-costo" value="${item.costo.toFixed(2)}" min="0" step="0.01" style="width: 100px; text-align: right;"></td>
                    <td class="text-right subtotal-celda">$${subtotal}</td>
                    <td class="text-center"><button type="button" class="btn-remover" style="background: #c0392b; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;">X</button></td>
                </tr>
            `;
            tablaDetalle.innerHTML += fila;
        }
        calcularTotal();
        agregarListenersInputs();
    }

    function agregarListenersInputs() {
        tablaDetalle.querySelectorAll('tr').forEach(fila => {
            const id = fila.dataset.id;
            fila.querySelector('.input-cantidad').addEventListener('change', (e) => {
                itemsCompra[id].cantidad = parseInt(e.target.value) || 1;
                renderizarTabla();
            });
            fila.querySelector('.input-costo').addEventListener('change', (e) => {
                // CORRECCIÓN: Reemplazar coma por punto para asegurar el parseo correcto de decimales.
                itemsCompra[id].costo = parseFloat(e.target.value.replace(',', '.')) || 0.00;
                renderizarTabla();
            });
            fila.querySelector('.btn-remover').addEventListener('click', () => {
                delete itemsCompra[id];
                renderizarTabla();
            });
        });
    }

    function calcularTotal() {
        let total = 0;
        for (const id in itemsCompra) {
            total += itemsCompra[id].cantidad * itemsCompra[id].costo;
        }
        totalDisplay.textContent = `$${total.toFixed(2)}`;
    }

    // 2. Guardar la orden de compra
    btnGuardar.addEventListener('click', async function() {
        if (!selectProveedor.value) {
            alert('Por favor, seleccione un proveedor.');
            return;
        }
        if (Object.keys(itemsCompra).length === 0) {
            alert('Debe agregar al menos un producto a la orden.');
            return;
        }

        if (confirm('¿Confirma la creación de esta Orden de Compra? El stock se incrementará.')) {
            const datosCompra = {
                proveedor: selectProveedor.value,
                items: Object.values(itemsCompra)
            };

            try {
                const response = await fetch('ajax/confirmar_compra.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datosCompra)
                });
                const resultado = await response.json();

                if (resultado.status === 'ok') {
                    alert(`Compra registrada con éxito. Folio: ${resultado.folio}`);
                    window.location.reload(); // Recargar para limpiar
                } else {
                    alert('Error: ' + resultado.msg);
                }
            } catch (error) {
                console.error('Error al guardar la compra:', error);
                alert('Ocurrió un error de conexión.');
            }
        }
    });
});
</script>
  </body>
</html>