// RESPONSABLE: Rol 2 (Front)rd
// Validaciones generales, manejo de modales, toggles de menú.
console.log("Sistema cargado");

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // MENÚ HAMBURGUESA
    // ==========================================
    const menuBtn = document.getElementById('mobile-menu-btn');
    const navbarMenu = document.getElementById('navbar-menu');

    if (menuBtn && navbarMenu) {
        menuBtn.addEventListener('click', function (e) {
            e.stopPropagation(); // Evita que el clic se propague al documento
            navbarMenu.classList.toggle('active');
            console.log("Abriendo/Cerrando menú...")
        });
    }

    // ==========================================
    // SUBMENÚS
    // ==========================================
    const dropdownBtns = document.querySelectorAll('.dropbtn');

    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {

            if (window.innerWidth <= 768) {
                e.preventDefault();
                e.stopPropagation();

                const dropdownContent = this.nextElementSibling;

                const yaEstabaAbierto = dropdownContent.classList.contains('show');

                document.querySelectorAll('.dropdown-content').forEach(content => {
                    content.classList.remove('show');
                });

                if (!yaEstabaAbierto) {
                    dropdownContent.classList.add('show');
                }
            }
        });
    });

    // ==========================================
    // CERRAR AL HACER CLIC AFUERA
    // ==========================================
    document.addEventListener('click', function (e) {
        if (window.innerWidth <= 768) {

            if (navbarMenu && !navbarMenu.contains(e.target) && e.target !== menuBtn) {

                navbarMenu.classList.remove('active');

                document.querySelectorAll('.dropdown-content').forEach(c => c.classList.remove('show'));
            }
        }
    });

    // ==========================================
    // CONFIRM ACTIONS
    // ==========================================
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-confirm-action');
        if (btn) {
            const msg = btn.dataset.confirmMessage || '¿Estás seguro?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        }
    });

});

// ==========================================
// LÓGICA DE COMPRAS (compras.php)
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    const inputProducto = document.getElementById('input-producto-compra');
    if (inputProducto) {
        const btnAgregar = document.getElementById('btn-agregar-item');
        const tablaDetalle = document.getElementById('tabla-detalle-compra');
        const totalDisplay = document.getElementById('total-compra-display');
        const btnGuardar = document.getElementById('btn-guardar-compra');
        const selectProveedor = document.getElementById('proveedor');

        let itemsCompra = {};

        btnAgregar.addEventListener('click', buscarYAgregarProducto);
        inputProducto.addEventListener('keypress', function (e) {
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
                    const producto = productos[0];
                    if (!itemsCompra[producto.id]) {
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
                tablaDetalle.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Agrega productos para comenzar la orden</td></tr>';
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
                        <td><input type="number" class="input-cantidad input-qty-small" value="${item.cantidad}" min="1"></td>
                        <td><input type="number" class="input-costo input-cost-small" value="${item.costo.toFixed(2)}" min="0" step="0.01"></td>
                        <td class="text-right subtotal-celda">$${subtotal}</td>
                        <td class="text-center"><button type="button" class="btn-remover btn-icon-remove">X</button></td>
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

        btnGuardar.addEventListener('click', async function () {
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
                        window.location.reload();
                    } else {
                        alert('Error: ' + resultado.msg);
                    }
                } catch (error) {
                    console.error('Error al guardar la compra:', error);
                    alert('Ocurrió un error de conexión.');
                }
            }
        });
    }
});

// ==========================================
// LÓGICA DE DEVOLUCIONES (devoluciones.php)
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    // Checkbox logic
    const checkDevolucion = document.querySelectorAll('.check-devolucion');
    if (checkDevolucion.length > 0) {
        checkDevolucion.forEach(check => {
            check.addEventListener('change', function () {
                const id = this.getAttribute('data-id');
                const input = document.getElementById('cant_' + id);
                if (input) {
                    input.disabled = !this.checked;
                    if (!this.checked) input.value = 1;
                }
            });
        });
    }

    const btnProcesar = document.getElementById('btn-procesar-devolucion');
    if (btnProcesar) {
        btnProcesar.addEventListener('click', async function () {
            const itemsADevolver = [];
            document.querySelectorAll('.check-devolucion:checked').forEach(check => {
                const id = check.getAttribute('data-id');
                const cantidadInput = document.getElementById('cant_' + id);
                if (cantidadInput) {
                    itemsADevolver.push({
                        id_libro: parseInt(id),
                        cantidad: parseInt(cantidadInput.value)
                    });
                }
            });

            if (itemsADevolver.length === 0) {
                alert('Debe seleccionar al menos un producto para devolver.');
                return;
            }

            const idVenta = document.getElementById('venta_id_origen').value;
            const motivo = document.getElementById('motivo_devolucion').value;

            if (confirm('¿Está seguro de procesar esta devolución? El stock será restaurado.')) {
                try {
                    const response = await fetch('ajax/confirmar_devolucion.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            id_venta: parseInt(idVenta),
                            items: itemsADevolver,
                            motivo: motivo
                        })
                    });
                    const resultado = await response.json();
                    if (resultado.status === 'ok') {
                        alert(`Devolución registrada con éxito. Folio de devolución: ${resultado.folio}\nSe abrirá el comprobante para imprimir.`);
                        window.open(`ticket.php?folio=${resultado.folio}&tipo=devolucion`, '_blank');
                        window.location.href = 'devoluciones.php';
                    } else {
                        alert('Error: ' + resultado.msg);
                    }
                } catch (error) {
                    console.error('Error al procesar devolución:', error);
                    alert('Ocurrió un error de conexión.');
                }
            }
        });
    }
});


// ==========================================
// LÓGICA DE TICKET (ticket.php)
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    // Generar código de barras si existe el elemento y la librería
    if (document.getElementById("codigoBarrasTicket") && window.JsBarcode) {
        const ticketContainer = document.querySelector('.ticket');
        const folio = ticketContainer ? ticketContainer.dataset.folio : '00000000';

        if (folio) {
            JsBarcode("#codigoBarrasTicket", folio.padStart(8, '0'), {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 40,
                displayValue: true,
                fontSize: 14,
                margin: 5
            });
        }

        // Auto imprimir
        setTimeout(function () {
            window.print();
        }, 500);
    }

    // Botón cerrar
    const btnClose = document.querySelector('.btn-close-window');
    if (btnClose) {
        btnClose.addEventListener('click', function () {
            window.close();
        });
    }

    // Botón imprimir genérico
    const btnPrint = document.querySelectorAll('.btn-print');
    btnPrint.forEach(btn => {
        btn.addEventListener('click', function () {
            window.print();
        });
    });
});
