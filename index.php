<?php 
// 1. Incluir Configuración y Header

// 2. Conexión a la Base de Datos
require_once 'config/db.php'; 

require 'includes/header.php';

?>

<div class="container py-5">
    
    <?php echo mostrarAlertas(); ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="row justify-content-center mb-0">
            <div class="col-md-8 text-center">
                <div class="p-4 card-material">
                    <h1 class="display-5 fw-bold text-on-surface">👋 Hola, <?php echo limpiar($_SESSION['user_name']); ?></h1>
                    <p class="lead text-muted">Bienvenido al Panel de Control de ZooManager.</p>
                    
                    <div class="d-inline-block px-3 py-1 rounded-pill mb-3" 
                        style="background-color: var(--md-sys-color-primary-container); color: var(--md-sys-color-on-primary-container);">
                        <strong>Rol:</strong> <?php echo ucfirst($_SESSION['user_role'] ?? 'Usuario'); ?>
                    </div>

                    <?php
                    // Consultamos la fecha fresca de la base de datos
                    $stmt = $pdo->prepare("SELECT ultimo_acceso FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $fecha_acceso = $stmt->fetchColumn();
                    ?>

                    <?php if ($fecha_acceso): ?>
                        <p class="small text-muted mb-0">
                            🕒 Último acceso: <strong><?php echo date('d/m/Y h:i A', strtotime($fecha_acceso)); ?></strong>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

        <div class="row g-4 justify-content-center">
            
            <div class="col-md-5 col-lg-4">
                <div class="card h-100 card-material text-center p-4">
                    <div class="card-body">
                        <div class="material-icon-large">🐾</div>
                        <h3 class="card-title fw-bold">Animales</h3>
                        <p class="card-text">Registrar nuevos ingresos, editar fichas y ver el listado completo.</p>
                        <a href="views/admin/animals.php" class="btn btn-material-primary w-100 mt-3 stretched-link">
                            Gestionar Animales
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-5 col-lg-4">
                <div class="card h-100 card-material text-center p-4">
                    <div class="card-body">
                        <div class="material-icon-large">🌿</div>
                        <h3 class="card-title fw-bold">Hábitats</h3>
                        <p class="card-text">Control de zonas, climas y capacidad máxima de los recintos.</p>
                        <a href="views/admin/habitats.php" class="btn btn-material-secondary w-100 mt-3 stretched-link">
                            Gestionar Hábitats
                        </a>
                    </div>
                </div>
            </div>

        </div>

    <?php else: ?>

        <div class="row min-vh-50 align-items-center justify-content-center text-center mt-0">
            <div class="col-lg-8">
                <div class="mb-0"> 
                    <img src="<?php echo BASE_URL; ?>assets/img/leon_logo.png" 
                        alt="Logo ZooManager" 
                        class="img-fluid rounded-circle" 
                        style="max-width: 250px; max-height: 250px;"> 
                </div>
                
                <h1 class="display-3 fw-bold" style="color: #1a1a1a;">
                    ZooManager
                </h1>
                
                <p class="lead fs-4 mb-0 mx-auto" style="max-width: 700px; color: #1a1a1a;">
                    Sistema integral para la gestión zoológica, control de especies y monitoreo de hábitats bajo estándares de bienestar animal.
                </p>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php require 'includes/footer.php'; ?>