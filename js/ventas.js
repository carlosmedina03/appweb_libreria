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
        // CORRECCIÓN: Si no hay internet, iniciamos vacío y evitamos el error rojo
        if (!navigator.onLine) {
            console.log("Offline: Iniciando con carrito vacío visualmente.");
            carritoActual = {};
            renderizarCarrito();
            return;
        }

        try {
            const res = await fetch("ajax/carrito_get.php"); 
            if (!res.ok) return;
            const data = await res.json();
            carritoActual = data.carrito || {}; 
            renderizarCarrito();
        } catch (error) {
            console.error("Error al cargar el carrito inicial:", error);
        }
    }
    // 2. Función HÍBRIDA para buscar producto
    async function buscarProducto(codigo) {
        console.log("Buscando: " + codigo);

        // INTENTO 1: BUSCAR EN SERVIDOR (ONLINE)
        try {
            const respuesta = await fetch(`ajax/buscar_producto.php?q=${codigo}`);
            if (!respuesta.ok) throw new Error("Error servidor");
            const productos = await respuesta.json();

            if (productos.length > 0) {
                agregarAlCarrito(productos[0]);
            } else {
                alert("Producto no encontrado en servidor.");
                inputCodigo.value = "";
                inputCodigo.focus();
            }

        } catch (error) {
            // INTENTO 2: MODO OFFLINE (Aquí es donde entra tu sistema cuando falla el internet)
            console.warn("Sin conexión (" + error.message + "). Buscando en local...");

            // Verificamos si existe la función de búsqueda local
            if (typeof buscarProductoOffline === 'function') {
                try {
                    const productoLocal = await buscarProductoOffline(codigo);
                    
                    if (productoLocal) {
                        // Adaptamos los datos para el carrito
                        const prodAdaptado = {
                            id: productoLocal.id,
                            titulo: productoLocal.titulo, 
                            precio_venta: productoLocal.precio_venta,
                            codigo: productoLocal.codigo
                        };
                        agregarAlCarrito(prodAdaptado);
                    } else {
                        alert("Producto no encontrado en catálogo offline.");
                        inputCodigo.value = "";
                        inputCodigo.focus();
                    }
                } catch (err) {
                    console.error("Error buscando en local:", err);
                }
            } else {
                console.error("Falta la función buscarProductoOffline en offline_manager.js");
            }
        }
    }

// 3. Función para agregar al carrito (Blindada)
    async function agregarAlCarrito(producto) {
        
        // Función interna para guardar en local (Plan B)
        const guardarEnLocal = () => {
            console.log("Usando carrito local...");
            const id = producto.id;
            if (carritoActual[id]) {
                carritoActual[id].cantidad++;
            } else {
                carritoActual[id] = {
                    id: producto.id,
                    titulo: producto.titulo || producto.nombre,
                    precio: producto.precio_venta || producto.precio,
                    cantidad: 1,
                    codigo: producto.codigo 
                };
            }
            renderizarCarrito();
            inputCodigo.value = "";
            inputCodigo.focus();
        };

        // 1. Si físicamente no hay red, Plan B directo
        if (!navigator.onLine) {
            guardarEnLocal();
            return;
        }

        // 2. Intentamos conectar (Plan A)
        const formData = new FormData();
        formData.append("id", producto.id);
        formData.append("titulo", producto.titulo);
        formData.append("precio", producto.precio_venta);

        try {
            const res = await fetch("ajax/carrito_add.php", {
                method: "POST",
                body: formData
            });
            // Si el servidor responde error (o está apagado), lanzamos error
            if (!res.ok) throw new Error("Fallo servidor");
            
            const data = await res.json();
            if (data.status === "ok") {
                carritoActual = data.carrito; 
                renderizarCarrito();
                inputCodigo.value = "";
                inputCodigo.focus(); 
            }
        } catch (error) {
            // 3. ¡AQUÍ ESTÁ LA SOLUCIÓN!
            // Si Apache está apagado pero tienes WiFi, el código cae aquí.
            console.warn("Fallo al conectar con PHP. Guardando en local.");
            guardarEnLocal();
        }
    }

   // 3.B. Función para REMOVER (Blindada)
    async function removerDelCarrito(id) {
        
        const borrarLocal = () => {
            if (carritoActual[id]) {
                delete carritoActual[id];
                renderizarCarrito();
            }
            inputCodigo.focus();
        };

        if (!navigator.onLine) { borrarLocal(); return; }

        const formData = new FormData();
        formData.append("id", id);

        try {
            const res = await fetch("ajax/carrito_remove.php", {
                method: "POST",
                body: formData
            });
            if (!res.ok) throw new Error("Fallo servidor");
            const data = await res.json();
            if (data.status === "ok") {
                carritoActual = data.carrito; 
                renderizarCarrito();
                inputCodigo.focus(); 
            }
        } catch (error) {
            borrarLocal(); // Si falla, borramos local
        }
    }

    // 3.C. Función para CANCELAR (Blindada)
    async function cancelarVenta() {
        if (!confirm('¿Cancelar venta? Se vaciará el carrito.')) return;

        const borrarLocal = () => {
            carritoActual = {};
            renderizarCarrito();
            inputCodigo.focus();
        };

        if (!navigator.onLine) { borrarLocal(); return; }

        try {
            const res = await fetch("ajax/carrito_clear.php", { method: "POST" });
            if (!res.ok) throw new Error("Fallo servidor");
            const data = await res.json();
            if (data.status === 'ok') {
                carritoActual = {}; 
                renderizarCarrito(); 
                inputCodigo.focus();
            }
        } catch (error) {
            borrarLocal(); // Si falla, borramos local
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

    // 5. Confirmar Venta (Blindado a prueba de fallos)
    if (btnCobrar) {
        btnCobrar.addEventListener("click", async () => {
            
            if (Object.keys(carritoActual).length === 0) {
                alert("El carrito está vacío");
                return;
            }

            if (!confirm("¿Confirmar venta y generar ticket?")) return;

            // --- FUNCIÓN INTERNA: PLAN B (OFFLINE) ---
            const procesarVentaOffline = () => {
                console.log("Procesando venta offline...");
                
                // 1. Convertimos el carrito a array
                const productosArray = Object.values(carritoActual);
                
                // 2. Calculamos total
                const totalVenta = productosArray.reduce((acc, item) => acc + (item.cantidad * parseFloat(item.precio)), 0);

                // GENERAR FOLIO TEMPORAL ÚNICO
                // Tomamos los últimos 9 dígitos del tiempo actual para que sea único
                const folioUnico = "OFF-" + Date.now().toString().slice(-9);

                const datosVenta = {
                    total: totalVenta.toFixed(2),
                    productos: productosArray,
                    folio: folioUnico
                };

                // 3. Guardamos en IndexedDB
                if (typeof guardarVentaOffline === 'function') {
                    guardarVentaOffline(datosVenta);
                    
                    // Limpiamos todo
                    carritoActual = {};
                    renderizarCarrito();
                } else {
                    alert("Error crítico: No se encontró la función offline.");
                }
            };

            // --- INTENTO 1: SI NO HAY RED FÍSICA ---
            if (!navigator.onLine) {
                procesarVentaOffline();
                return;
            }

            // --- INTENTO 2: TRATAR DE CONECTAR ONLINE ---
            try {
                const res = await fetch("ajax/confirmar_venta.php", {
                    method: "POST"
                });
                
                // Si el servidor está apagado o da error, lanzamos excepción
                if (!res.ok) throw new Error("Fallo servidor");

                const data = await res.json();

                if (data.status === "ok") {
                    // ÉXITO ONLINE
                    window.open(`ticket.php?folio=${data.folio}`, '_blank', 'width=400,height=600');
                    window.location.reload();
                } else {
                    alert("Error del sistema: " + data.msg);
                }

            } catch (error) {
                // --- INTENTO 3: SI FALLÓ LA CONEXIÓN (PLAN B) ---
                console.warn("Fallo conexión con servidor (" + error.message + "). Guardando offline.");
                procesarVentaOffline();
            }
        });
    }

    // INICIALIZACIÓN: Cargar el carrito existente al entrar a la página
    // Se llama al final para asegurar que todas las funciones estén declaradas.
    cargarCarritoInicial(); // Carga el carrito desde la sesión al iniciar.
    // renderizarCarrito(); // Ya no es necesario aquí, cargarCarritoInicial se encarga.
});