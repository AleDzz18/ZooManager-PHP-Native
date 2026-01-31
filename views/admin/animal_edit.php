<?php
require '../../includes/auth_check.php';
require '../../config/db.php';
require '../../includes/header.php';

// Verificación de permisos
if (!esAdmin()) {
    $_SESSION['error'] = "No tienes permisos para acceder a la gestión de animales.";
    header("Location: " . BASE_URL . "index.php");
    exit();
}


// 1. OBTENER EL ANIMAL A EDITAR
$id = $_GET['id'] ?? null;
if (!$id) { header("Location: animals.php"); exit(); }

try {
    // Traemos los datos del animal
    $stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt->execute([$id]);
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);

    // Traemos los hábitats para el select
    $h_stmt = $pdo->query("SELECT id, nombre FROM habitats");
    $habitats = $h_stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$animal) { die("Animal no encontrado."); }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 700px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">Editar Animal</h2>
                <div class="text-muted small">
                    Editando a: <strong><?php echo limpiar($animal['nombre']); ?></strong> (ID: #<?php echo $animal['id']; ?>)
                </div>
            </div>
            <a href="animals.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
        
        <?php echo mostrarAlertas(); ?>
        
        <form action="../../actions/animals/animal_update_action.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">

            <div class="mb-3">
                <label for="nombre" class="form-label fw-semibold">Nombre del Animal</label>
                <input type="text" name="nombre" id="nombre" class="form-control" 
                    value="<?php echo limpiar($animal['nombre']); ?>" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="especie" class="form-label fw-semibold">Especie</label>
                    <input type="text" name="especie" id="especie" class="form-control" 
                        value="<?php echo limpiar($animal['especie']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="clima" class="form-label fw-semibold">Clima Requerido</label>
                    <select name="clima" id="clima" class="form-select" required>
                        <?php 
                        $climas = ["Selva", "Desierto", "Acuático", "Polar", "Aviario", "Sabana"];
                        $climaActual = $animal['clima'] ?? ''; 
                        foreach ($climas as $c): 
                        ?>
                            <option value="<?php echo $c; ?>" <?php echo ($climaActual == $c) ? 'selected' : ''; ?>>
                                <?php echo $c; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="dieta" class="form-label fw-semibold">Dieta</label>
                    <input type="text" name="dieta" id="dieta" class="form-control" 
                        value="<?php echo limpiar($animal['dieta']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="edad" class="form-label fw-semibold">Edad (Años)</label>
                    <input type="number" name="edad" id="edad" class="form-control" 
                        value="<?php echo $animal['edad']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="fecha_llegada" class="form-label fw-semibold">Fecha de Llegada</label>
                    <input type="date" name="fecha_llegada" id="fecha_llegada" class="form-control" 
                        value="<?php echo $animal['fecha_llegada']; ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="habitat_id" class="form-label fw-semibold">Hábitat Asignado</label>
                <select name="habitat_id" id="habitat_id" class="form-select" required>
                    <?php foreach ($habitats as $h): ?>
                        <option value="<?php echo $h['id']; ?>" 
                            <?php echo ($h['id'] == $animal['habitat_id']) ? 'selected' : ''; ?>>
                            <?php echo limpiar($h['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text text-muted small">
                    <i class="bi bi-exclamation-triangle"></i> Si cambias el hábitat, se verificará la capacidad disponible nuevamente.
                </div>
            </div>

            <button type="submit" class="btn btn-material-primary w-100 py-2">
                <i class="bi bi-check-circle-fill me-2"></i> Actualizar Datos
            </button>

        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>