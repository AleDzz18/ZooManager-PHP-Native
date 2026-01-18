<?php
require '../../includes/auth_check.php';
require '../../includes/header.php';
require '../../config/db.php';

// 1. OBTENER EL ANIMAL A EDITAR
$id = $_GET['id'] ?? null;
if (!$id) { header("Location: animals.php"); exit(); }

try {
    // Traemos los datos del animal
    $stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt->execute([$id]);
    $animal = $stmt->fetch();

    // Traemos los hábitats para el select
    $h_stmt = $pdo->query("SELECT id, nombre FROM habitats");
    $habitats = $h_stmt->fetchAll();

    if (!$animal) { die("Animal no encontrado."); }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 600px;">
        <h2>Editar Animal: <?php echo htmlspecialchars($animal['nombre']); ?></h2>
        
        <form action="../../actions/animals/animal_update_action.php" method="POST" class="form-standard">
            <input type="hidden" name="id" value="<?php echo $animal['id']; ?>">

            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($animal['nombre']); ?>" required>
            </div>

            <div class="form-group">
                <label>Especie:</label>
                <input type="text" name="especie" value="<?php echo htmlspecialchars($animal['especie']); ?>" required>
            </div>

            <div class="row" style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label>Edad:</label>
                    <input type="number" name="edad" value="<?php echo $animal['edad']; ?>" required>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Fecha Llegada:</label>
                    <input type="date" name="fecha_llegada" value="<?php echo $animal['fecha_llegada']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Hábitat:</label>
                <select name="habitat_id" required>
                    <?php foreach ($habitats as $h): ?>
                        <option value="<?php echo $h['id']; ?>" <?php echo ($h['id'] == $animal['habitat_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($h['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-submit" style="background:#f39c12;">Actualizar Datos</button>
            <a href="animals.php" style="display:block; text-align:center; margin-top:10px; color:#7f8c8d;">Volver atrás</a>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>