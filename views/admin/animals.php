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
        <a href="animal_create.php" class="btn-register">
            + Nuevo Animal
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table-standard">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Especie</th>
                    <th>Edad</th>
                    <th>Hábitat</th>
                    <th>Ingreso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($animales) > 0): ?>
                    <?php foreach ($animales as $animal): ?>
                        <tr>
                            <td><?php echo $animal['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($animal['nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($animal['especie']); ?></td>
                            <td><?php echo $animal['edad']; ?> años</td>
                            
                            <td>
                                <?php echo $animal['habitat_nombre'] ? htmlspecialchars($animal['habitat_nombre']) : '<span class="text-muted">Sin Asignar</span>'; ?>
                            </td>
                            
                            <td><?php echo date('d/m/Y', strtotime($animal['fecha_llegada'])); ?></td>
                            
                            <td class="actions-cell">
                                <a href="animal_edit.php?id=<?php echo $animal['id']; ?>" class="btn-edit">✏️ Editar</a>
                                <a href="../../actions/animals/animal_delete.php?id=<?php echo $animal['id']; ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar a este animal?');">🗑️ Borrar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No hay animales registrados aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>