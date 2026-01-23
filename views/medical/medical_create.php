<?php
require '../../includes/auth_check.php';
require '../../includes/header.php';
require '../../config/db.php';

// Obtener el ID del animal para saber a quién estamos atendiendo
$animal_id = $_GET['animal_id'] ?? null;

if (!$animal_id) {
    header("Location: animals.php");
    exit();
}

// Consultamos nombre para mostrarlo en el título
$stmt = $pdo->prepare("SELECT nombre FROM animals WHERE id = ?");
$stmt->execute([$animal_id]);
$animal = $stmt->fetch();
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 700px;">
        <div class="admin-header">
            <h2>🩺 Nuevo Registro Médico</h2>
            <a href="medical_history.php?id=<?php echo $animal_id; ?>" class="btn-delete" style="background:#7f8c8d;">Cancelar</a>
        </div>

        <?php echo mostrarAlertas(); ?>
        
        <p>Paciente: <strong><?php echo limpiar($animal['nombre']); ?></strong></p>

        <form action="../../actions/medical/medical_create_action.php" method="POST" class="form-standard">
            <input type="hidden" name="animal_id" value="<?php echo $animal_id; ?>">

            <div class="row" style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label>Fecha del Suceso:</label>
                    <input type="date" name="fecha" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group" style="flex:2;">
                    <label>Motivo de Consulta / Descripción:</label>
                    <input type="text" name="descripcion" required placeholder="Ej: Revisión rutinaria, herida en la pata...">
                </div>
            </div>

            <div class="form-group">
                <label>Diagnóstico (Opcional):</label>
                <textarea name="diagnostico" rows="2" placeholder="Qué enfermedad o condición tiene..."></textarea>
            </div>

            <div class="form-group">
                <label>Tratamiento / Medicación (Opcional):</label>
                <textarea name="tratamiento" rows="3" placeholder="Medicamentos recetados, reposo, dieta especial..."></textarea>
            </div>

            <div class="form-group">
                <label>Severidad:</label>
                <select name="severidad" required>
                    <option value="" disabled selected>Seleccione la severidad</option>
                    <option value="Baja">Baja</option>
                    <option value="Media">Media</option>
                    <option value="Alta">Alta</option>
                </select>

            <button type="submit" class="btn-submit" style="background:#2980b9;">Guardar Registro</button>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>