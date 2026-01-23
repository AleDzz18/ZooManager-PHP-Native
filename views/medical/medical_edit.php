<?php
// 1. SEGURIDAD: Solo usuarios logueados entran.
require '../../includes/auth_check.php';
require '../../includes/header.php';
require '../../config/db.php';

// 2. CAPTURAR EL ID DEL REGISTRO MÉDICO
$id = $_GET['id'] ?? null;

if (!$id) {
    // Si no hay ID, no sabemos qué editar, volvemos al inicio.
    header("Location: ../admin/animals.php");
    exit();
}

try {
    // 3. OBTENER DATOS DEL REGISTRO MÉDICO + DATOS DEL ANIMAL
    // Hacemos un JOIN para saber el nombre del animal (para mostrarlo en el título)
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
        <div class="admin-header">
            <h2>✏️ Editar Registro de: <?php echo limpiar($registro['nombre_animal']); ?></h2>
            <a href="medical_history.php?id=<?php echo $registro['animal_id']; ?>" 
                class="btn-delete" style="background:#7f8c8d;">Cancelar</a>
        </div>

        <?php echo mostrarAlertas(); ?>

        <form action="../../actions/medical/medical_update_action.php" method="POST" class="form-standard">
            
            <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">
            
            <input type="hidden" name="animal_id" value="<?php echo $registro['animal_id']; ?>">

            <div class="row" style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label>Fecha:</label>
                    <input type="date" name="fecha" required 
                           value="<?php echo $registro['fecha']; ?>">
                </div>
                
                <div class="form-group" style="flex:1;">
                    <label>Severidad:</label>
                    <select name="severidad">
                        <option value="Baja" <?php echo ($registro['severidad'] == 'Baja') ? 'selected' : ''; ?>>Baja</option>
                        <option value="Media" <?php echo ($registro['severidad'] == 'Media') ? 'selected' : ''; ?>>Media</option>
                        <option value="Alta" <?php echo ($registro['severidad'] == 'Alta') ? 'selected' : ''; ?>>Alta</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Descripción / Motivo:</label>
                <input type="text" name="descripcion" required 
                    value="<?php echo limpiar($registro['descripcion']); ?>">
            </div>

            <div class="form-group">
                <label>Diagnóstico:</label>
                <textarea name="diagnostico" rows="2"><?php echo limpiar($registro['diagnostico']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Tratamiento:</label>
                <textarea name="tratamiento" rows="3"><?php echo limpiar($registro['tratamiento']); ?></textarea>
            </div>

            <button type="submit" class="btn-submit" style="background:#f39c12;">Actualizar Historial</button>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>