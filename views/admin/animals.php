<?php
/*
    ---------------------------------------------------
    VISTA DE GESTIÓN DE ANIMALES (READ)
    ---------------------------------------------------
*/

// 1. SEGURIDAD
require '../../includes/auth_check.php';

// 2. INTERFAZ Y CONEXIÓN
require '../../includes/header.php';

// Verificación de permisos
if (!puedeVerAnimales()) {
    $_SESSION['error'] = "No tienes permisos para acceder a la gestión de animales.";
    header("Location: " . BASE_URL . "index.php");
    exit();
}

require '../../config/db.php';

// 3. OBTENER DATOS
try {
    $sql = "SELECT animals.*, habitats.nombre as habitat_nombre 
            FROM animals 
            LEFT JOIN habitats ON animals.habitat_id = habitats.id 
            ORDER BY animals.id DESC";
            
    $stmt = $pdo->query($sql);
    $animales = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error al cargar los animales: " . $e->getMessage();
}
?>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="card card-material shadow-lg border-0 p-3">
            <h2 class="fw-bold mb-0">
                <i class="bi bi-paw-print"></i> Gestión de Animales
            </h2>
            <p class="text-grey mb-0">Administra la fauna del zoológico</p>
        </div>
        
        <?php if (esAdmin()): ?>
            <a href="animal_create.php" class="btn btn-material-primary shadow-sm">
                <i class="bi bi-plus-lg"></i> Nuevo Animal
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
                            <th class="py-3 ps-4 text-secondary">ID</th> 
                            <th class="py-3">Nombre</th>
                            <th class="py-3">Especie</th>
                            <th class="py-3">Edad</th>
                            <th class="py-3">Hábitat</th>
                            <th class="py-3">Dieta</th>
                            <th class="py-3">Llegada</th>
                            <?php if (puedeVerAnimales()): ?>
                                <th class="py-3 text-end pe-4">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($animales as $animal): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted">#<?php echo $animal['id']; ?></td> 
                                
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px;">
                                            <span class="fw-bold"><?php echo strtoupper(substr($animal['nombre'], 0, 1)); ?></span>
                                        </div>
                                        <span class="fw-semibold text-dark"><?php echo limpiar($animal['nombre']); ?></span>
                                    </div>
                                </td>
                                
                                <td class="text-secondary"><?php echo limpiar($animal['especie']); ?></td>
                                
                                <td>
                                    <span class="text-dark"><?php echo $animal['edad']; ?> años</span>
                                </td>
                                
                                <td>
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2">
                                        <i class="bi bi-geo-alt-fill me-1"></i>
                                        <?php echo $animal['habitat_nombre'] ?? 'Sin asignar'; ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <span class="small text-muted border rounded px-2 py-1">
                                        <?php echo limpiar($animal['dieta'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                
                                <td class="text-muted small">
                                    <?php echo formatearFecha($animal['fecha_llegada']); ?>
                                </td>
                                
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <?php if (puedeVerAnimales()): ?>
                                            <a href="../medical/medical_history.php?id=<?php echo $animal['id']; ?>" 
                                                class="btn btn-sm btn-outline-info" 
                                                title="Historial Médico">
                                                📋
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (esAdmin()): ?>
                                            <a href="animal_edit.php?id=<?php echo $animal['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Editar">
                                                ✏️
                                            </a>
                                            
                                            <a href="../../actions/animals/animal_delete.php?id=<?php echo $animal['id']; ?>" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('¿Estás seguro de eliminar al animal #<?php echo $animal['id']; ?>?');"
                                                title="Eliminar">
                                                🗑️
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($animales)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No hay animales registrados aún.</p>
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