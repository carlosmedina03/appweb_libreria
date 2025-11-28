<?php
// ============================================================
// RESPONSABLE: Rol 4 (Ayudantes)
// REQUERIMIENTO: Formatos generales del sistema
// ============================================================

// Función: formato_moneda($cantidad)
// REQ: "Formato monetario: 2 decimales, separador decimal '.'" (Ej: 1234.50)
function formato_moneda($cantidad) {
    return number_format($cantidad, 2, '.', ',');
}

// Función: sanear_input($data)
// Seguridad básica para XSS.
?>