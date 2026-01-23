<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

// Definimos la constante si no existe para evitar errores de re-definición
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">

</head>
<body>

<nav class="navbar navbar-expand-sm glass-navbar fixed-top">
    <div class="container-fluid px-4"> 
        <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL; ?>index.php">
            <img src="<?php echo BASE_URL; ?>assets/img/leon_logo.png" 
                alt="Logo ZooManager" 
                width="65" 
                height="65" 
                class="d-inline-block align-items-center me-2 rounded-circle">
                <span class="fw-bold fs-3 text-dark">ZooSystem</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><span class="nav-link fw-semibold"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
                    <?php if (puedeVerAnimales()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>views/admin/animals.php">Animales</a>
                        </li>  
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>views/admin/habitats.php">Hábitats</a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex gap-2"> <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>actions/auth/logout.php" class="btn btn-danger rounded-pill px-4">Salir</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>views/auth/login.php" class="btn btn-custom-light rounded-pill px-4 fw-bold">Entrar</a>
                    <a href="<?php echo BASE_URL; ?>views/auth/register.php" class="btn btn-custom-primary rounded-pill px-4 fw-bold">Registrarse</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>