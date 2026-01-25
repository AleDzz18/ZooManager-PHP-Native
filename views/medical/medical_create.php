<?php
require '../../includes/auth_check.php';
require '../../includes/header.php';
require '../../config/db.php';

// Obtener el ID del animal
$animal_id = $_GET['animal_id'] ?? null;

if (!$animal_id) {
    header("Location: animals.php");
    exit();
}

// Consultamos nombre
$stmt = $pdo->prepare("SELECT nombre FROM animals WHERE id = ?");
$stmt->execute([$animal_id]);
$animal = $stmt->fetch();
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 700px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">Nuevo Registro Médico</h2>
                <div class="text-muted small">
                    Paciente: <strong><?php echo limpiar($animal['nombre']); ?></strong>
                </div>
            </div>
            <a href="medical_history.php?id=<?php echo $animal_id; ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-x-lg"></i> Cancelar
            </a>
        </div>

        <?php echo mostrarAlertas(); ?>
        
        <form action="../../actions/medical/medical_create_action.php" method="POST">
            <input type="hidden" name="animal_id" value="<?php echo $animal_id; ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fecha del Suceso</label>
                    <input type="date" name="fecha" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Motivo de Consulta</label>
                    <input type="text" name="descripcion" class="form-control" required placeholder="Ej: Revisión rutinaria, herida...">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Diagnóstico <span class="text-muted fw-light">(Opcional)</span></label>
                <textarea name="diagnostico" class="form-control" rows="2" placeholder="Describa la condición encontrada..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tratamiento / Medicación <span class="text-muted fw-light">(Opcional)</span></label>
                <textarea name="tratamiento" class="form-control" rows="3" placeholder="Medicamentos, dosis, reposo..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Severidad del Caso</label>
                <select name="severidad" class="form-select" required>
                    <option value="" disabled selected>Seleccione la severidad</option>
                    <option value="Baja">🟢 Baja (Leve / Rutina)</option>
                    <option value="Media">🟡 Media (Requiere atención)</option>
                    <option value="Alta">🔴 Alta (Urgencia / Grave)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-material-primary w-100 py-2">
                <i class="bi bi-save me-2"></i> Guardar Registro
            </button>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>