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
    <div class="auth-card" style="max-width: 600px;">
        <div class="admin-header">
            <h2>Editar Hábitat</h2>
            <a href="habitats.php" class="btn-delete" style="background:#7f8c8d;">Cancelar</a>
        </div>

        <?php echo mostrarAlertas(); ?>

        <form action="../../actions/habitats/habitat_update_action.php" method="POST" class="form-standard">
            <input type="hidden" name="id" value="<?php echo $habitat['id']; ?>">

            <div class="form-group">
                <label>Nombre del Hábitat:</label>
                <input type="text" name="nombre" value="<?php echo limpiar($habitat['nombre']); ?>" required>
            </div>

            <div class="row" style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label>Tipo de Clima:</label>
                    <select name="clima" required>
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

                <div class="form-group" style="flex:1;">
                    <label>Capacidad Máxima:</label>
                    <input type="number" name="capacidad" value="<?php echo $habitat['capacidad']; ?>" required min="1">
                </div>
            </div>

            <div class="form-group">
                <label>Descripción:</label>
                <textarea name="descripcion" rows="3"><?php echo limpiar($habitat['descripcion']); ?></textarea>
            </div>

            <button type="submit" class="btn-submit" style="background:#f39c12;">Guardar Cambios</button>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>