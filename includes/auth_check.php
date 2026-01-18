<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Acceso denegado. Debes iniciar sesión.";
    
    header("Location: /zoo-system/views/auth/login.php");
    exit();
}