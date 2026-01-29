<?php
// 1. EL GUARDIA DE SEGURIDAD
require '../../includes/auth_check.php';
require '../../config/db.php';
require '../../includes/header.php';
// Verificación de permisos
if (!puedeVerAnimales()) {
    $_SESSION['error'] = "No tienes permisos para acceder a la gestión de animales.";
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// 2. OBTENER LOS HÁBITATS PARA EL SELECT
try {
    // Consultamos solo ID y Nombre para llenar el select
    $stmt = $pdo->query("SELECT id, nombre FROM habitats");
    $habitats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al cargar hábitats.";
}
?>

<div class="auth-container">
    
    <div class="auth-card" style="max-width: 700px;"> 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">Registrar Nuevo Animal</h2>
                <p class="text-muted mb-0 small">Ingresa los datos del nuevo habitante</p>
            </div>
            <a href="animals.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-x-lg"></i> Cancelar
            </a>
        </div>

        <?php echo mostrarAlertas(); ?>

        <form action="../../actions/animals/animal_create_action.php" method="POST">
            
            <div class="mb-3">
                <label for="nombre" class="form-label fw-semibold">Nombre del Animal</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Ej: Simba">
            </div>

            <div class="row g-3 mb-3"> 
                <div class="col-md-6">
                    <label for="especie" class="form-label fw-semibold">Especie</label>
                    <input type="text" name="especie" id="especie" class="form-control" required placeholder="Ej: León Africano">
                </div>
                
                <div class="col-md-6">
                    <label for="clima" class="form-label fw-semibold">Clima Requerido</label>
                    <select name="clima" id="clima" class="form-select" required>
                        <option value="" disabled selected>-- Selecciona --</option>
                        <option value="Selva">🌴 Selva Tropical</option>
                        <option value="Desierto">🌵 Desierto</option>
                        <option value="Acuático">💧 Acuático</option>
                        <option value="Polar">❄️ Polar</option>
                        <option value="Aviario">🦅 Aviario</option>
                        <option value="Sabana">🦁 Sabana</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="dieta" class="form-label fw-semibold">Dieta</label>
                    <input type="text" name="dieta" id="dieta" class="form-control" required placeholder="Ej: Carnívoro">
                </div>

                <div class="col-md-4">
                    <label for="edad" class="form-label fw-semibold">Edad (Años)</label>
                    <input type="number" name="edad" id="edad" class="form-control" required min="0" placeholder="0">
                </div>
                
                <div class="col-md-4">
                    <label for="fecha_llegada" class="form-label fw-semibold">Fecha de Llegada</label>
                    <input type="date" name="fecha_llegada" id="fecha_llegada" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>

            <div class="mb-4">
                <label for="habitat_id" class="form-label fw-semibold">Asignar Hábitat</label>
                <select name="habitat_id" id="habitat_id" class="form-select" required>
                    <option value="">-- Selecciona un hábitat --</option>
                    
                    <?php foreach ($habitats as $h): ?>
                        <option value="<?php echo $h['id']; ?>">
                            <?php echo limpiar($h['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text text-muted small">
                    <i class="bi bi-info-circle"></i> Solo aparecerán los hábitats disponibles.
                </div>
            </div>

            <button type="submit" class="btn btn-material-primary w-100 py-2">
                <i class="bi bi-save me-2"></i> Guardar Animal
            </button>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>