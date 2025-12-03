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

    // 1. Escuchar el Lector de Barras (Detectar ENTER)
    inputCodigo.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            const codigo = inputCodigo.value.trim();
            if (codigo) {
                buscarProducto(codigo);
            }
        }
    });

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
                renderizarCarrito(data.carrito);
                inputCodigo.value = "";
                inputCodigo.focus(); // Regresar foco al scanner
            }
        } catch (error) {
            console.error("Error agregando al carrito:", error);
        }
    }

    // 4. Renderizar la tabla visualmente (Rol 2)
    function renderizarCarrito(carrito) {
        tablaCarrito.innerHTML = "";
        let total = 0;

        // Convertir objeto carrito a array para recorrerlo
        Object.values(carrito).forEach(item => {
            const subtotal = item.cantidad * item.precio;
            total += subtotal;

            const row = `
                <tr>
                    <td>${item.titulo}</td>
                    <td class="text-center">${item.cantidad}</td>
                    <td class="text-right">$${parseFloat(item.precio).toFixed(2)}</td>
                    <td class="text-right">$${subtotal.toFixed(2)}</td>
                </tr>
            `;
            tablaCarrito.innerHTML += row;
        });

        // Actualizar total visual (con IVA o sin IVA según lógica visual)
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
});