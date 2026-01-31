<?php
// includes/auth_check.php

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
// Si el usuario no está logueado, redirigir al login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Acceso denegado. Debes iniciar sesión.";
    
    header("Location: /zoo-system/views/auth/login.php");
    exit();
}