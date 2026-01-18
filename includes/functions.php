<?php
/**
 * ARCHIVO DE FUNCIONES GLOBALES
 * Aquí centralizamos la lógica repetitiva para que el sistema sea fácil de mantener.
 */

// 1. LIMPIEZA DE ENTRADAS (Seguridad XSS)
function limpiar($dato) {
    // trim: elimina espacios al inicio y final
    // htmlspecialchars: convierte caracteres especiales en entidades HTML
    // (Ej: "<" se convierte en "&lt;"), evitando que ejecuten scripts maliciosos.
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

// 2. FORMATEO DE FECHAS
function formatearFecha($fecha) {
    // Convierte "2026-01-17" en "17 de Enero, 2026" (o el formato que prefieras)
    if (empty($fecha)) return "N/A";
    return date('d/m/Y', strtotime($fecha));
}

// 3. GENERADOR DE ALERTAS (Consistencia de Mensajes)
function mostrarAlertas() {
    $salida = "";
    
    // Verificamos si hay mensajes de error en la sesión
    if (isset($_SESSION['error'])) {
        $salida .= "<div class='alert alert-error'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']); // Lo borramos para que no se repita
    }
    
    // Verificamos si hay mensajes de éxito
    if (isset($_SESSION['success'])) {
        $salida .= "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    
    return $salida;
}

// 4. VERIFICADOR DE ROLES (Seguridad extra)

// Comprueba si es Admin (Para borrar o gestionar usuarios)
function esAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Comprueba si es Cuidador (Para historiales médicos)
function esCuidador() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'cuidador';
}

// Comprueba si tiene CUALQUIERA de los dos (Para ver la lista de animales)
function puedeVerAnimales() {
    return isset($_SESSION['user_role']) && 
        ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'cuidador');
}