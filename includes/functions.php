<?php
/*
    ---------------------------------------------------
    ARCHIVO DE FUNCIONES GLOBALES Y UTILIDADES
    ---------------------------------------------------
    Propósito Educativo: Centralizar la lógica repetitiva del sistema 
    (seguridad, formato, roles) para mantener el código limpio y mantenible.
*/

// --- SEGURIDAD ANTI-ACCESO DIRECTO ---
// Evita que alguien ejecute este archivo de utilidades directamente desde el navegador.
if (count(get_included_files()) == 1) {
    header('HTTP/1.0 403 Forbidden');
    exit("Acceso prohibido.");
}

/* ==========================================================================
    1. SEGURIDAD Y SANITIZACIÓN DE DATOS
   ========================================================================== */

/**
 * Función para prevenir ataques XSS (Cross-Site Scripting).
 * @param string $dato El texto ingresado por el usuario.
 * @return string El texto limpio y seguro.
 */
function limpiar($dato) {
    // 1. trim(): Elimina espacios en blanco accidentales al inicio y final.
    // 2. htmlspecialchars(): Convierte caracteres especiales (como < y >) en entidades HTML (&lt; y &gt;).
    //    Esto evita que un atacante inyecte etiquetas <script> maliciosas.
    // 3. ENT_QUOTES: Asegura que tanto comillas simples como dobles sean neutralizadas.
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

/**
 * Función para evitar la manipulación de URL.
 * Bloquea cualquier petición que no sea enviada a través de un formulario (POST).
 */
function soloMetodoPost() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['error'] = "Acceso denegado: No puedes acceder directamente a los archivos de acción.";
        
        $base = defined('BASE_URL') ? BASE_URL : '/zoo-system/';
        header("Location: " . $base . "index.php");
        exit();
    }
}

/* ==========================================================================
    2. INTERFAZ DE USUARIO Y EXPERIENCIA (UI/UX)
   ========================================================================== */

/**
 * Formatea fechas técnicas (YYYY-MM-DD) a un formato amigable para el usuario.
 */
function formatearFecha($fecha) {
    if (empty($fecha)) return "N/A";
    return date('d/m/Y', strtotime($fecha)); // Ejemplo: Convierte "2026-09-23" a "23/09/2026"
}

/**
 * Sistema de Mensajes Flash.
 * Lee los mensajes de éxito o error de la sesión, genera el HTML de la alerta y
 * luego borra la variable de sesión para que el mensaje no vuelva a aparecer al recargar.
 */
function mostrarAlertas() {
    $salida = "";
    if (isset($_SESSION['error'])) {
        $salida .= "<div class='alert alert-error'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']); // Borrado ("consumo") del mensaje
    }
    if (isset($_SESSION['success'])) {
        $salida .= "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    return $salida;
}

/* ==========================================================================
    3. CONTROL DE ACCESO BASADO EN ROLES (RBAC)
    Concepto: Separar los privilegios en funciones específicas permite cambiar 
    las reglas del negocio en un solo lugar sin buscar archivo por archivo.
   ========================================================================== */

// Verificaciones básicas de rol
function esAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function esCuidador() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'cuidador';
}

// Permiso general de lectura
function puedeVerAnimales() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'cuidador');
}

// Permiso destructivo explícito (Para usuarios y animales)
function puedeEliminarRegistros() {
    return esAdmin(); // Solo los administradores tienen el nivel máximo de borrado
}

// Permiso específico (Regla de negocio: Cuidadores pueden corregir sus propios historiales médicos)
function puedeEliminarHistorialMedico() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'cuidador');
}

// Permiso para modificar la infraestructura del zoológico
function puedeGestionarHabitats() {
    return esAdmin();
}

/* ==========================================================================
    4. REGLAS DE NEGOCIO Y LÓGICA BIOLÓGICA
   ========================================================================== */

/**
 * Whitelisting (Lista Blanca) de climas permitidos.
 * Es mucho más seguro verificar contra una lista fija de opciones válidas que
 * intentar adivinar qué palabras incorrectas podría enviar el usuario.
 */
function obtenerClimasValidos() {
    return ['Desierto', 'Acuático', 'Polar', 'Aviario', 'Sabana', 'Selva'];
}

/**
 * Validación estricta de formato y lógica de fechas.
 */
function esFechaValida($fecha) {
    // 1. Verifica que el formato sea exactamente YYYY-MM-DD (Rechaza fechas inexistentes como 30 de febrero)
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!($d && $d->format('Y-m-d') === $fecha)) {
        return false;
    }
    
    // 2. Límites lógicos (Boundary Checking)
    $fecha_minima = '1900-01-01'; // Un animal no puede haber llegado en el siglo XIX
    $fecha_actual = date('Y-m-d'); // No podemos registrar llegadas desde el futuro
    
    if ($fecha < $fecha_minima || $fecha > $fecha_actual) {
        return false;
    }
    return true;
}

/* ==========================================================================
    5. MANEJO DE ERRORES SILENCIOSO
   ========================================================================== */

/**
 * Protege contra Information Disclosure (Fuga de Información).
 * Atrapa los errores técnicos (caídas de base de datos, consultas mal formadas)
 * y los esconde del usuario final, mostrando un mensaje genérico y seguro.
 */
function registrarErrorCritico($excepcion, $ruta_redireccion, $mensaje_usuario = "Ocurrió un error inesperado. Contacte al administrador.") {
    // 1. Logueamos el error real en los archivos del servidor de forma invisible.
    error_log("Error Técnico Capturado: " . $excepcion->getMessage());
    
    // 2. Preparamos el mensaje amistoso para el usuario.
    $_SESSION['error'] = $mensaje_usuario;
    
    // 3. Redirigimos usando rutas dinámicas absolutas.
    $base = defined('BASE_URL') ? BASE_URL : '/zoo-system/';
    // ltrim() quita cualquier barra (/) accidental al principio de la ruta de redirección
    header("Location: " . $base . ltrim($ruta_redireccion, '/'));
    exit();
}
?>