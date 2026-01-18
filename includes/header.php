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
</head>
<body>
    <nav class="main-nav">
        <div class="container nav-container">
            <a href="<?php echo BASE_URL; ?>index.php" class="logo">🦁 ZooSystem</a>

            <ul class="menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><span class="user-badge"><?php echo htmlspecialchars($_SESSION['user_name']); ?> (<?php echo $_SESSION['user_role']; ?>)</span></li>
                    <?php if (puedeVerAnimales()): ?>
                        <li><a href="<?php echo BASE_URL; ?>views/admin/animals.php">Animales</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo BASE_URL; ?>views/admin/habitats.php">Hábitats</a></li>
                    <li><a href="<?php echo BASE_URL; ?>actions/auth/logout.php" class="btn-logout">Salir</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>index.php">Inicio</a></li>
                    <li><a href="<?php echo BASE_URL; ?>views/auth/login.php">Entrar</a></li>
                    <li><a href="<?php echo BASE_URL; ?>views/auth/register.php">Registro</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <main class="container">