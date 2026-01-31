<?php
// includes/header.php

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php'; // Asegura que las funciones estén disponibles

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/zoo-system/');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'ZooManager'; ?> - Sistema de Gestión</title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg glass-navbar fixed-top">
    <div class="container"> 
        <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL; ?>index.php">
            <img src="<?php echo BASE_URL; ?>assets/img/leon_logo.png" 
                alt="Logo" width="50" height="50" class="rounded-circle me-2 shadow-sm">
            <span class="fw-bold text-on-surface">ZooManager</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Menú dinámico según permisos -->
                <!-- Si el usuario está logueado -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?php echo BASE_URL; ?>index.php">Inicio</a>
                    </li>
                    <?php if (puedeVerAnimales()): ?>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="<?php echo BASE_URL; ?>views/admin/animals.php">Animales</a>
                        </li>  
                    <?php endif; ?>
                    <?php if (esAdmin()): ?>    
                        <li class="nav-item">
                            <a class="nav-link px-3" href="<?php echo BASE_URL; ?>views/admin/habitats.php">Hábitats</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="small text-black d-none d-sm-inline">
                        <i class="bi bi-person"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </span>
                    <a href="<?php echo BASE_URL; ?>actions/auth/logout.php" 
                        class="btn btn-outline-danger btn-sm rounded-pill px-4">Salir</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>views/auth/login.php" 
                        class="btn btn-material-secondary btn-sm">Entrar</a>
                    <a href="<?php echo BASE_URL; ?>views/auth/register.php" 
                        class="btn btn-material-primary btn-sm">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="pt-5 mt-5">
