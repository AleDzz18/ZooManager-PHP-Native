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
    // 3. CONSULTAR DATOS DEL ANIMAL
    $stmt_animal = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt_animal->execute([$animal_id]);
    $animal = $stmt_animal->fetch(PDO::FETCH_ASSOC);

    if (!$animal) {
        die("Animal no encontrado.");
    }

    // 4. CONSULTAR HISTORIAL MÉDICO
    $sql_history = "SELECT * FROM medical_records WHERE animal_id = ? ORDER BY fecha DESC";
    $stmt_history = $pdo->prepare($sql_history);
    $stmt_history->execute([$animal_id]);
    $registros = $stmt_history->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>

<div class="container mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">
                <i class="bi bi-clipboard-pulse"></i> Historial Médico
            </h2>
            <div class="text-muted mt-1">
                Paciente: <strong><?php echo limpiar($animal['nombre']); ?></strong> 
                <span class="badge bg-secondary bg-opacity-10 text-secondary ms-2">
                    <?php echo limpiar($animal['especie']); ?>
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="../admin/animals.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <a href="medical_create.php?animal_id=<?php echo $animal['id']; ?>" class="btn btn-material-primary btn-sm d-flex align-items-center">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Chequeo
            </a>
        </div>
    </div>

    <?php echo mostrarAlertas(); ?>

    <div class="card card-material shadow-lg border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="py-3 ps-4 text-secondary">Fecha</th>
                            <th class="py-3">Descripción</th>
                            <th class="py-3">Diagnóstico</th>
                            <th class="py-3">Tratamiento</th>
                            <th class="py-3 text-center">Severidad</th>
                            <?php if (puedeVerAnimales()): ?>
                                <th class="py-3 text-end pe-4">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($registros) > 0): ?>
                            <?php foreach ($registros as $record): ?>
                                <?php 
                                    // Lógica de colores para la severidad
                                    $sevClass = match($record['severidad']) {
                                        'Alta' => 'bg-danger bg-opacity-10 text-danger border-danger',
                                        'Media' => 'bg-warning bg-opacity-10 text-warning border-warning', // Bootstrap warning es amarillo/naranja
                                        default => 'bg-success bg-opacity-10 text-success border-success' // Baja
                                    };
                                ?>
                                <tr>
                                    <td class="ps-4 text-nowrap">
                                        <i class="bi bi-calendar-event text-muted me-1"></i>
                                        <?php echo formatearFecha($record['fecha']); ?>
                                    </td>
                                    
                                    <td class="fw-semibold text-dark">
                                        <?php echo limpiar($record['descripcion']); ?>
                                    </td>
                                    
                                    <td class="small text-muted">
                                        <?php echo !empty($record['diagnostico']) ? limpiar($record['diagnostico']) : '<span class="text-secondary opacity-50">-</span>'; ?>
                                    </td>
                                    
                                    <td class="small text-muted">
                                        <?php echo !empty($record['tratamiento']) ? limpiar($record['tratamiento']) : '<span class="text-secondary opacity-50">-</span>'; ?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <span class="badge rounded-pill border <?php echo $sevClass; ?> px-3">
                                            <?php echo limpiar($record['severidad']); ?>
                                        </span>
                                    </td>
                                    
                                    <?php if (puedeVerAnimales()): ?>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <a href="medical_edit.php?id=<?php echo $record['id']; ?>" 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                <a href="../../actions/medical/medical_delete.php?id=<?php echo $record['id']; ?>" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('¿Seguro que deseas eliminar este registro médico?');"
                                                    title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted opacity-75">
                                        <i class="bi bi-heart-pulse" style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0">Este animal goza de buena salud.</p>
                                        <small>No hay registros médicos aún.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>