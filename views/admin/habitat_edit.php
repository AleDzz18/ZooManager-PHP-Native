<?php
// 1. SEGURIDAD Y CONEXIÓN
require '../../includes/auth_check.php';
require '../../includes/header.php';
require '../../config/db.php';

// 2. OBTENER EL ID Y VALIDAR
$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID de hábitat no especificado.";
    header("Location: habitats.php");
    exit();
}

try {
    // 3. BUSCAR DATOS ACTUALES
    $stmt = $pdo->prepare("SELECT * FROM habitats WHERE id = ?");
    $stmt->execute([$id]);
    $habitat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$habitat) {
        $_SESSION['error'] = "El hábitat no existe.";
        header("Location: habitats.php");
        exit();
    }

} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 650px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">Editar Hábitat</h2>
                <div class="text-muted small">
                    Editando: <strong><?php echo limpiar($habitat['nombre']); ?></strong> (ID: #<?php echo $habitat['id']; ?>)
                </div>
            </div>
            <a href="habitats.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <?php echo mostrarAlertas(); ?>

        <form action="../../actions/habitats/habitat_update_action.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $habitat['id']; ?>">

            <div class="mb-3">
                <label for="nombre" class="form-label fw-semibold">Nombre del Hábitat</label>
                <input type="text" name="nombre" id="nombre" class="form-control" 
                    value="<?php echo limpiar($habitat['nombre']); ?>" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="clima" class="form-label fw-semibold">Tipo de Clima</label>
                    <select name="clima" id="clima" class="form-select" required>
                        <?php 
                        $climas = ["Selva", "Desierto", "Acuático", "Polar", "Aviario", "Sabana"];
                        foreach ($climas as $c): 
                        ?>
                            <option value="<?php echo $c; ?>" <?php echo ($habitat['clima'] == $c) ? 'selected' : ''; ?>>
                                <?php echo $c; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="capacidad" class="form-label fw-semibold">Capacidad Máxima</label>
                    <input type="number" name="capacidad" id="capacidad" class="form-control" 
                        value="<?php echo $habitat['capacidad']; ?>" required min="1">
                    <div class="form-text text-muted small">
                        <i class="bi bi-exclamation-triangle"></i> Reducir la capacidad podría causar conflicto si hay muchos animales.
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="3"><?php echo limpiar($habitat['descripcion']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-material-primary w-100 py-2">
                <i class="bi bi-check-circle-fill me-2"></i> Guardar Cambios
            </button>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>