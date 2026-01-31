<?php

// --- SEGURIDAD ANTI-ACCESO DIRECTO ---
if (count(get_included_files()) == 1) {
    header('HTTP/1.0 403 Forbidden');
    exit("Acceso prohibido.");
}

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

// Comprueba si es Admin (Para gestión completa)
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

function obtenerClimasValidos() {
    return ['Desierto', 'Acuático', 'Polar', 'Aviario', 'Sabana', 'Selva'];
}

function esFechaValida($fecha) {
    // 1. Validar formato estricto YYYY-MM-DD
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!($d && $d->format('Y-m-d') === $fecha)) {
        return false; // Formato incorrecto (ej: texto, o 2026-02-30)
    }

    // 2. Validar rango lógico (Ni antes de 1900 ni en el futuro)
    $fecha_minima = '1900-01-01';
    $fecha_actual = date('Y-m-d');

    if ($fecha < $fecha_minima || $fecha > $fecha_actual) {
        return false; // Fecha ilógica
    }

    return true;
}

// 5. BLOQUEO DE ACCESO DIRECTO POR URL
function soloMetodoPost() {
    // Si el método NO es POST (es decir, alguien escribió la URL en el navegador)
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // Opcional: Guardar mensaje de error
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['error'] = "Acceso denegado: No puedes acceder directamente a los archivos de acción.";
        
        // Lo mandamos al inicio
        header("Location: ../../index.php");
        exit();
    }
}