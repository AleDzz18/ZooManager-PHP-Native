<?php
// 1. SEGURIDAD Y CONEXIONES
require '../../includes/auth_check.php';
require '../../includes/header.php';
require '../../config/db.php';

// 2. CONSULTA AVANZADA
$sql = "SELECT h.*, COUNT(a.id) as ocupacion_actual 
        FROM habitats h 
        LEFT JOIN animals a ON h.id = a.habitat_id 
        GROUP BY h.id 
        ORDER BY h.id DESC";

try {
    $stmt = $pdo->query($sql);
    $habitats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al cargar datos: " . $e->getMessage();
}
?>

<div class="container mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="card card-material shadow-lg border-0 p-3">
            <h2 class="fw-bold mb-0">
                <i class="bi bi-tree-fill"></i> Gestión de Hábitats
            </h2>
            <p class="text-grey mb-0">Administra los entornos del zoológico</p>
        </div>
        
        <?php if (esAdmin()): ?>
            <a href="habitat_create.php" class="btn btn-material-primary shadow-sm">
                <i class="bi bi-plus-lg"></i> Nuevo Hábitat
            </a>
        <?php endif; ?>
    </div>

    <?php echo mostrarAlertas(); ?>

    <div class="card card-material shadow-lg border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="py-3 ps-4 text-secondary">Nombre</th>
                            <th class="py-3">Clima</th>
                            <th class="py-3" style="width: 25%;">Descripción</th>
                            <th class="py-3">Ocupación / Capacidad</th>
                            <th class="py-3 text-center">Estado</th>
                            <?php if (esAdmin()): ?>
                                <th class="py-3 text-end pe-4">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($habitats)): ?>
                            <?php foreach ($habitats as $h): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?php echo limpiar($h['nombre']); ?></div>
                                    </td>
                                    
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3">
                                            <?php echo limpiar($h['clima']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php if (!empty($h['descripcion'])): ?>
                                            <span class="text-muted small d-inline-block text-truncate" style="max-width: 200px;" title="<?php echo limpiar($h['descripcion']); ?>">
                                                <?php echo limpiar($h['descripcion']); ?>
                                            </span>
                                        <?php else: ?>
                                            <em class="text-muted opacity-50 small">(Sin descripción)</em>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <?php 
                                            $ocupacion = $h['ocupacion_actual'];
                                            $capacidad = $h['capacidad'];
                                            $porcentaje = ($capacidad > 0) ? round(($ocupacion / $capacidad) * 100) : 0;
                                            
                                            // Lógica de color Bootstrap
                                            $bgClass = 'bg-success'; // Verde por defecto
                                            if ($porcentaje > 70) $bgClass = 'bg-warning'; // Amarillo
                                            if ($porcentaje >= 100) $bgClass = 'bg-danger'; // Rojo
                                        ?>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 10px; background-color: #e9ecef;">
                                                <div class="progress-bar <?php echo $bgClass; ?>" role="progressbar" 
                                                    style="width: <?php echo $porcentaje; ?>%;" 
                                                    aria-valuenow="<?php echo $porcentaje; ?>" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="small fw-bold text-muted" style="min-width: 45px;">
                                                <?php echo $ocupacion . "/" . $capacidad; ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($ocupacion >= $capacidad): ?>
                                            <span class="badge rounded-pill bg-danger text-white shadow-sm">
                                                <i class="bi bi-slash-circle me-1"></i> Lleno
                                            </span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-success text-white shadow-sm">
                                                <i class="bi bi-check-circle me-1"></i> Libre
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <?php if (esAdmin()): ?>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <a href="habitat_edit.php?id=<?php echo $h['id']; ?>" 
                                                    class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="../../actions/habitats/habitat_delete.php?id=<?php echo $h['id']; ?>" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('¿Seguro? Si borras el hábitat, los animales quedarán sin casa.');"
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
                                        <i class="bi bi-geo" style="font-size: 2.5rem;"></i>
                                        <p class="mt-2 mb-0">No hay hábitats registrados.</p>
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