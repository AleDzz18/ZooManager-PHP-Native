<?php
// 1. EL GUARDIA DE SEGURIDAD
require '../../includes/auth_check.php';
require '../../includes/header.php';
require '../../config/db.php';

// 2. OBTENER LOS HÁBITATS PARA EL SELECT
// Necesitamos llenar el menú desplegable con opciones reales de la BD.
try {
    // IMPORTANTE: Tu tabla usa 'nombre', no necesitamos cambiar esta consulta
    $stmt = $pdo->query("SELECT id, nombre FROM habitats");
    $habitats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al cargar hábitats.";
}
?>

<div class="auth-container"> <div class="auth-card" style="max-width: 600px;"> <div class="admin-header">
            <h2>Registrar Nuevo Animal</h2>
            <a href="animals.php" class="btn-delete" style="background:#7f8c8d;">Cancelar</a>
        </div>

        <p>Completa los datos del nuevo integrante del zoológico.</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="../../actions/animals/animal_create_action.php" method="POST" class="form-standard">
            
            <div class="form-group">
                <label>Nombre del Animal:</label>
                <input type="text" name="nombre" required placeholder="Ej: Simba">
            </div>

            <div class="form-group">
                <label>Especie:</label>
                <input type="text" name="especie" required placeholder="Ej: León Africano">
            </div>

            <div class="row" style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label>Edad (Años):</label>
                    <input type="number" name="edad" required min="0">
                </div>
                
                <div class="form-group" style="flex:1;">
                    <label>Fecha de Llegada:</label>
                    <input type="date" name="fecha_llegada" required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Asignar Hábitat:</label>
                <select name="habitat_id" required>
                    <option value="">-- Selecciona un hábitat --</option>
                    
                    <?php foreach ($habitats as $h): ?>
                        <option value="<?php echo $h['id']; ?>">
                            <?php echo limpiar($h['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                    
                </select>
            </div>

            <button type="submit" class="btn-submit">Guardar Animal</button>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>