<?php
// 1. SEGURIDAD
require '../../includes/auth_check.php';
require '../../includes/header.php';
require '../../config/db.php';

// 2. CAPTURAR EL ID
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../admin/animals.php");
    exit();
}

try {
    // 3. OBTENER DATOS
    $sql = "SELECT m.*, a.nombre as nombre_animal 
            FROM medical_records m 
            JOIN animals a ON m.animal_id = a.id 
            WHERE m.id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$registro) {
        die("El registro médico no existe.");
    }

} catch (PDOException $e) {
    die("Error de BD: " . $e->getMessage());
}
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 700px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">Editar Registro Médico</h2>
                <div class="text-muted small">
                    Paciente: <strong><?php echo limpiar($registro['nombre_animal']); ?></strong>
                </div>
            </div>
            <a href="medical_history.php?id=<?php echo $registro['animal_id']; ?>" 
                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <?php echo mostrarAlertas(); ?>

        <form action="../../actions/medical/medical_update_action.php" method="POST">
            
            <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">
            <input type="hidden" name="animal_id" value="<?php echo $registro['animal_id']; ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fecha</label>
                    <input type="date" name="fecha" class="form-control" required 
                           value="<?php echo $registro['fecha']; ?>">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Severidad</label>
                    <select name="severidad" class="form-select" required>
                        <option value="Baja" <?php echo ($registro['severidad'] == 'Baja') ? 'selected' : ''; ?>>🟢 Baja</option>
                        <option value="Media" <?php echo ($registro['severidad'] == 'Media') ? 'selected' : ''; ?>>🟡 Media</option>
                        <option value="Alta" <?php echo ($registro['severidad'] == 'Alta') ? 'selected' : ''; ?>>🔴 Alta</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Motivo / Descripción</label>
                <input type="text" name="descripcion" class="form-control" required 
                    value="<?php echo limpiar($registro['descripcion']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Diagnóstico</label>
                <textarea name="diagnostico" class="form-control" rows="2"><?php echo limpiar($registro['diagnostico']); ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Tratamiento</label>
                <textarea name="tratamiento" class="form-control" rows="3"><?php echo limpiar($registro['tratamiento']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-material-primary w-100 py-2">
                <i class="bi bi-check-circle-fill me-2"></i> Actualizar Historial
            </button>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>