<?php 
// 1. Incluir Configuración y Header
require 'includes/header.php'; 

// 2. VALIDACIÓN DE MENSAJES FLASH (Feedback al usuario)
?>

<div class="container" style="margin-top: 40px;">
    
    <?php echo mostrarAlertas(); ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="dashboard-welcome">
            <h1>👋 Hola, <?php echo limpiar($_SESSION['user_name']); ?></h1>
            <p>Bienvenido al Panel de Control de ZooManager.</p>
            <p>Tu rol actual es: <strong><?php echo ucfirst($_SESSION['user_role'] ?? 'Usuario'); ?></strong></p>
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
            <div class="mb-4">
                <img src="<?php echo BASE_URL; ?>assets/img/leon logo.png" alt="Logo ZooManager" style="width: 180px; height: auto;">
            </div>
            
            <h2 class="display-4 fw-bold" style="color: #2c3e50;">ZooManager</h2>
            <p class="lead">Sistema integral para la gestión zoológica, control de especies y monitoreo de hábitats.</p>
            
        </div>
    <?php endif; ?>

</div>

<?php require 'includes/footer.php'; ?>