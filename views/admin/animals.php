<?php
/*
    ---------------------------------------------------
    VISTA DE GESTIÓN DE ANIMALES (READ)
    ---------------------------------------------------
*/

// 1. SEGURIDAD: EL GUARDIA
// Incluimos auth_check antes que nada. Si no está logueado, lo patea al login.
require '../../includes/auth_check.php';

// 2. INTERFAZ Y CONEXIÓN
require '../../includes/header.php';

// Si NO puede ver animales, lo sacamos de aquí
if (!puedeVerAnimales()) {
    $_SESSION['error'] = "No tienes permisos para acceder a la gestión de animales.";
    header("Location: " . BASE_URL . "index.php");
    exit();
}

require '../../config/db.php';

// 3. OBTENER DATOS (Lógica de Lectura)
try {
    // Usamos LEFT JOIN para traer el nombre del hábitat en lugar de solo el número ID.
    // Esto cumple con el requisito de "Tablas Relacionadas".
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

<div class="container">
    <div class="admin-header">
        <h1>🐾 Gestión de Animales</h1>
        <?php if (esAdmin()): ?>
            <a href="animal_create.php" class="btn-register">
                + Nuevo Animal
            </a>
        <?php endif; ?>
    </div>

    <?php echo mostrarAlertas(); ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr class="table-dark">
                    <th style="width: 50px;">ID</th> <th>Nombre</th>
                    <th>Especie</th>
                    <th>Edad</th>
                    <th>Hábitat</th>
                    <th>Dieta</th>
                    <th>Llegada</th>
                    <?php if (puedeVerAnimales()): ?>
                        <th style="text-align: center;">Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animales as $animal): ?>
                    <tr>
                        <td class="text-muted">#<?php echo $animal['id']; ?></td> 
                        <td><strong><?php echo limpiar($animal['nombre']); ?></strong></td>
                        <td><?php echo limpiar($animal['especie']); ?></td>
                        <td><?php echo $animal['edad']; ?> años</td>
                        <td>
                            <span class="badge-habitat">
                                <?php echo $animal['habitat_nombre'] ?? 'Sin asignar'; ?>
                            </span>
                        </td>
                        <td><?php echo limpiar($animal['dieta'] ?? 'N/A'); ?></td>
                        <td><?php echo formatearFecha($animal['fecha_llegada']); ?></td>
                            <td class="actions-cell">
                                <?php if (puedeVerAnimales()): ?>
                                    <a href="../medical/medical_history.php?id=<?php echo $animal['id']; ?>" 
                                        class="btn-edit" 
                                        style="background-color: #3498db;" 
                                        title="Ver Historial Médico">
                                        📋
                                    </a>
                                <?php endif; ?>
                                <?php if (esAdmin()): ?>
                                    <a href="animal_edit.php?id=<?php echo $animal['id']; ?>" class="btn-edit" title="Editar">✏️</a>
                                    <a href="../../actions/animals/animal_delete.php?id=<?php echo $animal['id']; ?>" 
                                        class="btn-delete" 
                                        onclick="return confirm('¿Estás seguro de eliminar al animal #<?php echo $animal['id']; ?>?');">
                                        🗑️
                                    </a>
                                <?php endif; ?>
                            </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>