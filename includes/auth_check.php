<?php
/*
    ---------------------------------------------------
    MIDDLEWARE DE AUTENTICACIÓN (AUTH CHECK)
    ---------------------------------------------------
    Propósito Educativo: Este archivo se incluye al principio de las páginas
    que requieren que el usuario esté conectado. Actúa como una barrera.
*/

// PASO 1: VERIFICACIÓN DE SESIÓN ACTIVA
// session_status() === PHP_SESSION_NONE comprueba si ya hay una sesión iniciada.
// Si no la hay, la inicia. Esto evita el molesto error de "Session already started"
// si otro archivo ya había ejecutado session_start() previamente.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PASO 2: BARRERA DE AUTENTICACIÓN
// Verificamos si existe la variable 'user_id' en la sesión. 
// Esta variable solo se crea si el usuario pasó exitosamente por login_action.php.
if (!isset($_SESSION['user_id'])) {
    
    // Si no está logueado, preparamos un mensaje de error.
    $_SESSION['error'] = "Acceso denegado. Debes iniciar sesión.";
    
    // PASO 3: REDIRECCIÓN SEGURA DE RESPALDO
    // Por si este archivo se llegara a incluir en un contexto donde db.php 
    // (que define BASE_URL) no haya cargado, verificamos si existe.
    // Si no existe, usamos una ruta relativa de emergencia ('/zoo-system/').
    $base = defined('BASE_URL') ? BASE_URL : '/zoo-system/';
    
    // Expulsamos al visitante de vuelta a la pantalla de login y detenemos el código (exit).
    header("Location: " . $base . "views/auth/login.php");
    exit();
}
?>