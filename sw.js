// ============================================================
// RESPONSABLE: Rol 7 (POS Offline-First (PWA) con sincronización diferida)
// REQUERIMIENTO: "Service Worker - Intercepción de Red y Caché"
// DESCRIPCIÓN: Cachea archivos estáticos para cargar sin internet e intercepta peticiones
// ============================================================

const CACHE_NAME = 'pos-libreria-v1';

// Lista de archivos vitales para que la App funcione sin servidor
const ASSETS_TO_CACHE = [
  './',                     // Raíz
  'index.php',              // Login
  'dashboard.php',          // Panel principal
  'ventas.php',             // Módulo de ventas
  'css/styles.css',         // Estilos globales
  'css/ticket.css',         // Estilos de impresión
  'js/main.js',
  'js/ventas.js',
  'js/offline_manager.js',  // Lógica de BD local
  'assets/img/logo-maria-de-letras_v2.svg',       // Logo principal
  'assets/img/logo-maria-de-letras-ticket.png'    // Logo para ticket
];

// 1. EVENTO INSTALL
// Se ejecuta al registrar el SW. Descarga y guarda todos los archivos estáticos.
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log("Pre-cacheando activos...");
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
});

// 2. EVENTO FETCH
// Intercepta cada petición HTTP que hace el navegador.
self.addEventListener('fetch', (event) => {
  
  // Ignoramos peticiones que no sean GET (como los POST de guardar venta)
  if (event.request.method !== 'GET') return;

  // Ignoramos peticiones AJAX (las maneja ventas.js con su lógica try/catch)
  if (event.request.url.includes('ajax/')) return;

  // Estrategia: "Network first, falling back to cache" (o Cache First según convenga)
  // Aquí usamos: Intenta red, si falla, busca en caché.
  event.respondWith(
    fetch(event.request)
      .catch(() => caches.match(event.request))
  );
});