<?php 
// 1. Incluir Configuración y Header
require 'includes/header.php'; 

// 2. Conexión a la Base de Datos
require_once 'config/db.php'; 
?>

<div class="container" style="margin-top: 40px;">
    
    <?php echo mostrarAlertas(); ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="dashboard-welcome">
            <h1>👋 Hola, <?php echo limpiar($_SESSION['user_name']); ?></h1>
            <p>Bienvenido al Panel de Control de ZooManager.</p>
            <p>Tu rol actual es: <strong><?php echo ucfirst($_SESSION['user_role'] ?? 'Usuario'); ?></strong></p>

            <?php
            // Consultamos la fecha fresca de la base de datos
            $stmt = $pdo->prepare("SELECT ultimo_acceso FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $fecha_acceso = $stmt->fetchColumn();
            ?>

            <?php if ($fecha_acceso): ?>
                <p style="font-size: 0.9em; color: #666; margin-top: 5px;">
                    🕒 Último acceso registrado: 
                    <strong><?php echo date('d/m/Y h:i A', strtotime($fecha_acceso)); ?></strong>
                </p>
            <?php endif; ?>
        </div>

        <div class="dashboard-grid" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-top: 30px;">
            
            <div class="card" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; width: 250px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <div style="font-size: 3em;">🐾</div>
                <h3>Gestión de Animales</h3>
                <p>Registrar, editar y ver listado de animales.</p>
                <a href="views/admin/animals.php" class="btn-submit" style="display:block; margin-top:10px; text-decoration:none;">Ir a Animales</a>
            </div>

            <div class="card" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; width: 250px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <div style="font-size: 3em;">🌿</div>
                <h3>Gestión de Hábitats</h3>
                <p>Control de zonas y capacidades.</p>
                <a href="views/admin/habitats.php" class="btn-submit" style="display:block; margin-top:10px; text-decoration:none; background-color: #95a5a6;">Ir a Hábitats</a>
            </div>

        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 40px 0;">
            <div class="mb-0"> <img src="<?php echo BASE_URL; ?>assets/img/leon_logo.png" 
            alt="Logo ZooManager" 
            style="width: 320px; height: auto; display: block; margin: 0 auto; margin-bottom: -15px;"> 
            </div>
            <h2 class="display-4 fw-bold" style="color: #1a1a1a;">ZooManager</h2>
            <p class="fs-5 fw-medium mt-3" style="color: #1a1a1a; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6;">
    Sistema integral para la gestión zoológica, control de especies <br> 
    y monitoreo de hábitats.
</p>
            
        </div>
    <?php endif; ?>

</div>

<?php require 'includes/footer.php'; ?>