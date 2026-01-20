<?php
// 1. SEGURIDAD Y CONEXIÓN
require '../../includes/auth_check.php';
require '../../includes/header.php';
require '../../config/db.php';

// 2. OBTENER ID DEL ANIMAL
$animal_id = $_GET['id'] ?? null;

if (!$animal_id) {
    header("Location: animals.php");
    exit();
}

try {
    // 3. CONSULTAR DATOS DEL ANIMAL (Para el título)
    $stmt_animal = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt_animal->execute([$animal_id]);
    $animal = $stmt_animal->fetch(PDO::FETCH_ASSOC);

    if (!$animal) {
        die("Animal no encontrado.");
    }

    // 4. CONSULTAR HISTORIAL MÉDICO
    // Ordenamos por fecha descendente (lo más reciente primero)
    $sql_history = "SELECT * FROM medical_records WHERE animal_id = ? ORDER BY fecha DESC";
    $stmt_history = $pdo->prepare($sql_history);
    $stmt_history->execute([$animal_id]);
    $registros = $stmt_history->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>

<div class="container">
    <div class="admin-header">
        <div>
            <h2>📋 Historial Médico: <?php echo limpiar($animal['nombre']); ?></h2>
            <p class="text-muted">Especie: <?php echo limpiar($animal['especie']); ?></p>
        </div>
        <div>
            <a href="medical_create.php?animal_id=<?php echo $animal['id']; ?>" class="btn-register" style="background-color: #27ae60;">
                + Nuevo Chequeo
            </a>
            <a href="../admin/animals.php" class="btn-delete" style="background-color: #7f8c8d;">Volver</a>
        </div>
    </div>

    <?php echo mostrarAlertas(); ?>

    <?php if (count($registros) > 0): ?>
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo/Descripción</th>
                        <th>Diagnóstico</th>
                        <th>Tratamiento Recetado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $record): ?>
                        <tr>
                            <td><?php echo formatearFecha($record['fecha']); ?></td>
                            <td><?php echo limpiar($record['descripcion']); ?></td>
                            <td><?php echo !empty($record['diagnostico']) ? limpiar($record['diagnostico']) : '<span style="color:#aaa;">-</span>'; ?></td>
                            <td><?php echo !empty($record['tratamiento']) ? limpiar($record['tratamiento']) : '<span style="color:#aaa;">-</span>'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="card" style="text-align:center; padding:40px;">
            <p>Este animal goza de buena salud (o aún no tiene registros médicos).</p>
        </div>
    <?php endif; ?>
</div>

<?php require '../../includes/footer.php'; ?>