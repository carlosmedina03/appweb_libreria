<?php
// ============================================================
// RESPONSABLE: Rol 2 (UX-UI) y Rol 5 (Accesos)
// REQUERIMIENTO: "Hay dos perfiles mínimos: admin... y operador..."
// ============================================================
require_once 'includes/auth.php';

// TODO:
// 1. Mostrar menú diferente según $_SESSION['user']['rol'].
//    - ADMIN: Ve todo (Catálogo, Usuarios, Compras, Ventas, Reportes).
//    - OPERADOR: Solo ve Ventas, Devoluciones y Consultas.
// 2. Mostrar nombre del usuario logueado.
// 3. Botón de Logout visible.
?>