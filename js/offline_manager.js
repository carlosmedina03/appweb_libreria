// ============================================================
// RESPONSABLE: Rol 7 (POS Offline-First (PWA) con sincronización diferida)
// REQUERIMIENTO: "Gestor de Base de Datos Local (IndexedDB) y Tickets"
// DESCRIPCIÓN: Administra el almacenamiento local de productos/ventas y genera reportes offline
// ============================================================

// Configuración de la Base de Datos Local
const dbName = "LibreriaOfflineDB";
const dbVersion = 2; // Versión 2 incluye el almacén de productos
let db;

// 1. INICIALIZACIÓN DE INDEXEDDB
const request = indexedDB.open(dbName, dbVersion);

request.onupgradeneeded = (event) => {
    db = event.target.result;
    
    // Crear almacén para ventas pendientes (Cola de salida)
    if (!db.objectStoreNames.contains("ventas_pendientes")) {
        db.createObjectStore("ventas_pendientes", { keyPath: "id", autoIncrement: true });
    }

    // Crear almacén para catálogo de productos (Copia local)
    // Usamos 'codigo' como llave para búsquedas rápidas con escáner
    if (!db.objectStoreNames.contains("productos")) {
        db.createObjectStore("productos", { keyPath: "codigo" }); 
    }
};

request.onsuccess = (event) => {
    db = event.target.result;
    console.log("BD Offline lista");
    contarPendientes();
    
    // Si hay internet al iniciar, actualizamos el catálogo silenciosamente
    if (navigator.onLine) {
        descargarCatalogo();
    }
};

// --- SECCIÓN A: GESTIÓN DE PRODUCTOS ---

// Función para descargar catálogo del servidor y guardarlo localmente
function descargarCatalogo() {
    fetch('ajax/catalogo_offline.php')
        .then(res => res.json())
        .then(productos => {
            const tx = db.transaction(["productos"], "readwrite");
            const store = tx.objectStore("productos");
            
            // Limpiamos datos viejos y sobreescribimos con lo nuevo
            store.clear(); 
            productos.forEach(prod => {
                store.put(prod);
            });
            console.log("Catálogo offline actualizado: " + productos.length + " productos.");
        })
        .catch(err => console.error("No se pudo actualizar catálogo offline", err));
}

// Función promesa para buscar un producto en local por su código
function buscarProductoOffline(codigo) {
    return new Promise((resolve, reject) => {
        const tx = db.transaction(["productos"], "readonly");
        const store = tx.objectStore("productos");
        const request = store.get(codigo);

        request.onsuccess = () => {
            resolve(request.result); // Retorna el objeto producto o undefined
        };
        request.onerror = () => {
            reject("Error buscando en local");
        };
    });
}

// --- SECCIÓN B: GESTIÓN DE VENTAS Y TICKETS ---

// Guarda la venta en cola cuando no hay internet
function guardarVentaOffline(datosVenta) {
    const tx = db.transaction(["ventas_pendientes"], "readwrite");
    const store = tx.objectStore("ventas_pendientes");
    datosVenta.fecha_local = new Date().toISOString(); 
    
    const req = store.add(datosVenta);
    
    req.onsuccess = () => {
        alert("Sin conexión: Venta guardada localmente.");
        if (confirm("¿Desea imprimir el ticket provisional?")) {
            imprimirTicketOffline(datosVenta);
        }
        contarPendientes();
    };
}

// Genera e imprime el ticket HTML directamente desde el navegador
function imprimirTicketOffline(datosVenta) {
    let htmlContent = `
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Ticket Offline</title>
            <link rel="stylesheet" href="css/ticket.css" media="print">
            <link rel="stylesheet" href="css/ticket.css" media="screen">
            <style>
                /* Estilos embebidos para asegurar formato sin dependencias externas */
                body { font-family: 'Courier New', monospace; margin: 0; padding: 5px; max-width: 80mm; }
                img { max-width: 100%; }
                table { width: 100%; border-collapse: collapse; }
                td, th { text-align: left; vertical-align: top; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .border-top { border-top: 1px dashed #000; }
                .my-2 { margin-top: 10px; margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <div class="text-center">
                <img src="assets/img/logo-maria-de-letras-ticket.png" alt="Logo" style="max-width: 150px;">
                <h3>Librería María De Letras</h3>
                
                <p style="font-size: 1.2em; font-weight: bold; margin: 5px 0;">
                    Folio: ${datosVenta.folio || 'sin folio'}   
                </p>
                <p>Fecha: ${new Date().toLocaleString()}</p>
            </div>
            <br>
            <table>
                <thead>
                    <tr>
                        <th>Cant.</th>
                        <th>Descripción</th>
                        <th class="text-right">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    ${datosVenta.productos.map(prod => `
                        <tr>
                            <td style="vertical-align: top;">${prod.cantidad}</td>
                            <td style="vertical-align: top;">
                                ${prod.titulo || prod.nombre}
                                <br>
                                <small style="font-size: 0.85em; color: #555;">[${prod.codigo || 'S/C'}]</small>
                            </td>
                            <td class="text-right" style="vertical-align: top;">
                                $${(prod.cantidad * (prod.precio_venta || prod.precio)).toFixed(2)}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            <br>
            <div class="border-top my-2">
                <table style="margin-top: 5px;">
                    <tr>
                        <td class="text-right"><strong>TOTAL:</strong></td>
                        <td class="text-right"><strong>$${datosVenta.total}</strong></td>
                    </tr>
                </table>
            </div>
            <br>
            <div class="text-center">
                <p>¡Gracias por su compra!</p>
                <p>*** TICKET GENERADO OFFLINE ***</p>
                <p>Se sincronizará al conectar a internet</p>
            </div>
        </body>
        </html>
    `;

    // Abrimos ventana emergente para impresión
    let ventana = window.open('', '_blank', 'width=400,height=600');

    if (!ventana) {
        alert("⚠️ El navegador bloqueó el ticket. Por favor permite las ventanas emergentes en la barra de direcciones.");
        return;
    }

    ventana.document.open();
    ventana.document.write(htmlContent);
    ventana.document.close();

    ventana.onload = function() {
        setTimeout(() => {
            ventana.focus();
            ventana.print();
            ventana.close(); 
        }, 500);
    };
}

// Envía las ventas pendientes al servidor cuando vuelve el internet
function sincronizarVentas() {
    if (!navigator.onLine) { alert("Sin conexión."); return; }

    const tx = db.transaction(["ventas_pendientes"], "readonly");
    const store = tx.objectStore("ventas_pendientes");
    const req = store.getAll();

    req.onsuccess = () => {
        const ventas = req.result;
        if (ventas.length === 0) { alert("Nada pendiente."); return; }

        // Envío AJAX
        fetch('ajax/sync_ventas.php', {
            method: 'POST',
            body: JSON.stringify(ventas)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert("¡Sincronizado!");
                // Si el servidor confirma éxito, borramos la cola local
                const txWrite = db.transaction(["ventas_pendientes"], "readwrite");
                txWrite.objectStore("ventas_pendientes").clear();
                contarPendientes();
            } else {
                alert("Error servidor: " + data.message);
            }
        });
    };
}

// Utilidad para mostrar cuántas ventas hay en cola en la consola
function contarPendientes() {
    if (!db) return;
    const tx = db.transaction(["ventas_pendientes"], "readonly");
    const store = tx.objectStore("ventas_pendientes");
    const req = store.count();
    req.onsuccess = () => console.log("Pendientes: " + req.result);
}