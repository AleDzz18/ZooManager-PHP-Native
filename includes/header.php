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
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>

    <nav class="navbar navbar-expand-sm bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">🦁 ZooSystem</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><span class="nav-link text-dark"><?php echo htmlspecialchars($_SESSION['user_name']); ?> (<?php echo $_SESSION['user_role']; ?>)</span></li>
                        <?php if (puedeVerAnimales()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>views/admin/animals.php">Animales</a>
                            </li>  
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>views/admin/habitats.php">Hábitats</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>index.php">Inicio</a>
                        </li>
                    <?php endif; ?>
                    
                </ul>

                <div class="d-flex">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>actions/auth/logout.php" class="btn btn-outline-danger">Salir</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>views/auth/login.php" class="btn btn-outline-primary me-2">Entrar</a>
                        <a href="<?php echo BASE_URL; ?>views/auth/register.php" class="btn btn-primary">Registrarse</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </nav>
    <main class="container">