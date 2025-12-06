<?php
// REQUERIMIENTO: "Formato monetario: 2 decimales, separador decimal '.'"
// ==========================================

function formato_moneda($cantidad) {
    // Ejemplo: 1234.5 -> "1,234.50"
    return number_format($cantidad, 2, '.', ',');
}

function sanear($mysqli, $string) {
    return $mysqli->real_escape_string(trim($string));
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>