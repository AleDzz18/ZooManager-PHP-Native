<?php 
/**
 * DASHBOARD PRINCIPAL (INDEX)
 */

require_once 'config/db.php'; 
require 'includes/header.php'; // Aquí se cargan las funciones como esAdmin() y puedeVerAnimales()

ini_set('display_errors', 0);

// LÓGICA DE ÚLTIMO ACCESO
$fecha_acceso = "Primer ingreso";
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT ultimo_acceso FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $resultado = $stmt->fetchColumn();
        if ($resultado) {
            $fecha_acceso = formatearFecha($resultado);
        }
    } catch (PDOException $e) {
        // Silencioso
    }
}
?>

<div class="container py-5">
    
    <?php echo mostrarAlertas(); ?>

    <?php if (isset($_SESSION['user_id'])): ?>

        <div class="row justify-content-center mb-0">
            <div class="col-md-8 text-center">

                <div class="p-4 card-material">
                    <h1 class="display-5 fw-bold text-on-surface">
                        👋 Hola, <?php echo limpiar($_SESSION['user_name']); ?>
                    </h1>
                    <p class="lead text-muted">Bienvenido al Panel de Control de ZooManager.</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                            <i class="bi bi-shield-lock"></i> Rol: <?php echo ucfirst($_SESSION['user_role'] ?? 'Usuario'); ?>
                        </span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                            <i class="bi bi-clock-history"></i> Último acceso: <?php echo $fecha_acceso; ?>
                        </span>
                    </div>

                </div>
            </div>

            <div class="row g-4 justify-content-center">

                <?php if (puedeVerAnimales()): ?>
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
                <?php endif; ?>

                <?php if (esAdmin()): ?>
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
                <?php endif; ?>

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