// ============================================================
// RESPONSABLE: Rol 2 (Front-End) - Lógica de Interacción
// INTEGRACIÓN: El Rol 4 provee los endpoints en /ajax
// ============================================================

// 1. Detectar evento 'submit' o 'change' del input código.
// 2. Fetch a 'ajax/buscar_producto.php?q=' + codigo.
// 3. Si encuentra: Fetch a 'ajax/carrito_add.php'.
// 4. Actualizar DOM de la tabla del carrito.
// 5. Manejar el evento del botón "Confirmar Venta" -> AJAX -> Imprimir.

// CODIGO BASE, frontend favor de modificar a como es la interfaz real
document.addEventListener("DOMContentLoaded", () => {
    const inputCodigo = document.getElementById("codigo"); // El input donde escanea el lector
    const tablaCarrito = document.getElementById("tabla-carrito"); // Tbody de la tabla
    const totalDisplay = document.getElementById("total-display"); // Donde muestra el total
    const btnCobrar = document.getElementById("btn-cobrar");
    const btnBuscar = document.getElementById("btn-buscar"); // <-- AÑADIR ESTA LÍNEA
    const btnCancelar = document.getElementById("btn-cancelar");

    // 1. Escuchar el Lector de Barras (Detectar ENTER)
    inputCodigo.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            // Si el botón de buscar existe, simula un clic para unificar la lógica
            const codigo = inputCodigo.value.trim();
            if (codigo) {
                buscarProducto(codigo);
            }
        }
    });

    // CORRECCIÓN 2: Escuchar el clic en el botón "Buscar"
    if (btnBuscar) {
        btnBuscar.addEventListener('click', () => {
            const codigo = inputCodigo.value.trim();
            if (codigo) {
                buscarProducto(codigo);
            } else {
                inputCodigo.focus();
            }
        });
    }

    // Escuchar clics en la tabla para delegar el evento de borrado
    tablaCarrito.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-remover-item')) {
            e.preventDefault();
            const id = e.target.dataset.id;
            if (id) {
                if (confirm('¿Desea quitar este producto del carrito?')) {
                    removerDelCarrito(id);
                }
            }
        }
    });

    // Escuchar clic en el botón de cancelar venta
    if (btnCancelar) {
        btnCancelar.addEventListener('click', () => {
            cancelarVenta();
        });
    }

    let carritoActual = {}; // Variable para mantener el estado del carrito en el frontend

    // NUEVA FUNCIÓN: Carga el estado del carrito desde la sesión
    async function cargarCarritoInicial() {
        try {
            // Este endpoint no existe, pero si lo creas, esta es la lógica
            const res = await fetch("ajax/carrito_get.php"); // Devuelve el contenido de $_SESSION['carrito']
            if (!res.ok) return;
            const data = await res.json();
            carritoActual = data.carrito || {}; // Actualizar estado local
            renderizarCarrito();
        } catch (error) {
            console.error("Error al cargar el carrito inicial:", error);
        }
    }
    
    // 2. Función para buscar producto en el Backend
    async function buscarProducto(codigo) {
        try {
            // Llamada al endpoint del Rol 4
            const respuesta = await fetch(`ajax/buscar_producto.php?q=${codigo}`);
            const productos = await respuesta.json();

            if (productos.length > 0) {
                // Si encuentra, tomamos el primero (asumiendo código único)
                agregarAlCarrito(productos[0]);
            } else {
                alert("Producto no encontrado");
                inputCodigo.value = ""; // Limpiar para siguiente escaneo
                inputCodigo.focus();
            }
        } catch (error) {
            console.error("Error buscando producto:", error);
        }
    }

    // 3. Función para agregar al carrito (Sesión PHP)
    async function agregarAlCarrito(producto) {
        const formData = new FormData();
        formData.append("id", producto.id);
        formData.append("titulo", producto.titulo);
        formData.append("precio", producto.precio_venta);

        try {
            const res = await fetch("ajax/carrito_add.php", {
                method: "POST",
                body: formData
            });
            const data = await res.json();

            if (data.status === "ok") {
                carritoActual = data.carrito; // Actualizar estado local
                renderizarCarrito();
                inputCodigo.value = "";
                inputCodigo.focus(); // Regresar foco al scanner
            }
        } catch (error) {
            console.error("Error agregando al carrito:", error);
        }
    }

    // 3.B. Función para REMOVER del carrito (Sesión PHP)
    async function removerDelCarrito(id) {
        const formData = new FormData();
        formData.append("id", id);

        try {
            const res = await fetch("ajax/carrito_remove.php", {
                method: "POST",
                body: formData
            });
            const data = await res.json();

            if (data.status === "ok") {
                carritoActual = data.carrito; // Actualizar estado local
                renderizarCarrito();
                inputCodigo.focus(); // Devolver foco al scanner
            } else {
                alert("Error: " + (data.msg || "No se pudo quitar el producto."));
            }
        } catch (error) {
            console.error("Error removiendo del carrito:", error);
        }
    }

    // 3.C. Función para CANCELAR toda la venta
    async function cancelarVenta() {
        if (confirm('¿Está seguro de que desea cancelar toda la venta? Se vaciará el carrito.')) {
            try {
                const res = await fetch("ajax/carrito_clear.php", { method: "POST" });
                const data = await res.json();

                if (data.status === 'ok') {
                    carritoActual = {}; // Limpiar estado local
                    renderizarCarrito(); // Renderiza el carrito vacío
                    inputCodigo.focus();
                } else {
                    alert('Error al cancelar la venta.');
                }
            } catch (error) {
                console.error("Error cancelando venta:", error);
                alert('Error de conexión al cancelar la venta.');
            }
        }
    }

    // 4. Renderizar la tabla visualmente (Rol 2)
    function renderizarCarrito() {
        tablaCarrito.innerHTML = "";
        let total = 0;
        let hayItems = false;

        // Convertir objeto carrito a array para recorrerlo
        Object.values(carritoActual).forEach(item => {
            const subtotal = item.cantidad * parseFloat(item.precio);
            total += subtotal;

            const row = `
                <tr>
                    <td>${item.titulo}</td>
                    <!-- CORRECCIÓN 1: Añadir clases de ancho para alinear con cabecera -->
                    <td class="text-center col-10">${item.cantidad}</td>
                    <td class="text-right col-15">$${parseFloat(item.precio).toFixed(2)}</td>
                    <td class="text-right col-15">$${subtotal.toFixed(2)}</td>
                    <td class="text-center col-5">
                        <button class="btn-remover-item" data-id="${item.id}" title="Quitar del carrito" style="background:none; border:none; color:red; cursor:pointer; font-size: 1.2em;">&times;</button>
                    </td>
                </tr>
            `;
            tablaCarrito.innerHTML += row;
            hayItems = true;
        });

        if (!hayItems) {
            tablaCarrito.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #777;">Escanea un producto para comenzar...</td></tr>';
        }

        // Actualizar total visual
        if (totalDisplay) totalDisplay.innerText = `$${total.toFixed(2)}`;
    }

    // 5. Confirmar Venta
    if (btnCobrar) {
        btnCobrar.addEventListener("click", async () => {
            if (!confirm("¿Confirmar venta y generar ticket?")) return;

            try {
                const res = await fetch("ajax/confirmar_venta.php", {
                    method: "POST"
                });
                const data = await res.json();

                if (data.status === "ok") {
                    // Abrir ticket para imprimir
                    window.open(`ticket.php?folio=${data.folio}`, '_blank', 'width=400,height=600');
                    // Recargar pagina para limpiar
                    window.location.reload();
                } else {
                    alert("Error: " + data.msg);
                }
            } catch (error) {
                console.error("Error en venta:", error);
                alert("Error de conexión");
            }
        });
    }

    // INICIALIZACIÓN: Cargar el carrito existente al entrar a la página
    // Se llama al final para asegurar que todas las funciones estén declaradas.
    cargarCarritoInicial(); // Carga el carrito desde la sesión al iniciar.
    // renderizarCarrito(); // Ya no es necesario aquí, cargarCarritoInicial se encarga.
});