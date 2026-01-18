<?php
// 1. SEGURIDAD Y CONEXIONES
require '../../includes/auth_check.php';
require '../../includes/header.php';
require '../../config/db.php';

// 2. CONSULTA AVANZADA (Relación 1 a N)
// Seleccionamos todos los datos del hábitat (h.*)
// Y contamos (COUNT) cuántos animales (a.id) tienen ese habitat_id
// Usamos LEFT JOIN para que aparezcan los hábitats aunque estén vacíos (0 animales)
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

<div class="container">
    <div class="admin-header">
        <h1>🌿 Gestión de Hábitats</h1>
        <a href="habitat_create.php" class="btn-register">
            + Nuevo Hábitat
        </a>
    </div>

    <?php echo mostrarAlertas(); ?>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Clima</th>
                    <th>Descripción</th>
                    <th>Capacidad</th>
                    <th>Ocupación</th> <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($habitats)): ?>
                    <?php foreach ($habitats as $h): ?>
                        <tr>
                            <td><strong><?php echo limpiar($h['nombre']); ?></strong></td>
                            <td><?php echo limpiar($h['clima']); ?></td>

                            <?php if (!empty($h['descripcion'])): ?>
                                <td title="<?php echo limpiar($h['descripcion']); ?>">
                                    <?php 
                                        $desc_corta = (strlen($h['descripcion']) > 30) ? substr($h['descripcion'], 0, 27) . '...' : $h['descripcion'];
                                        echo limpiar($desc_corta); 
                                    ?>
                                </td>
                            <?php else: ?>
                                <td><em style="color:#888;">(Sin descripción)</em></td>
                            <?php endif; ?>
                            
                            <td><?php echo $h['capacidad']; ?> animales</td>

                            <td>
                                <?php 
                                    $ocupacion = $h['ocupacion_actual'];
                                    $capacidad = $h['capacidad'];
                                    // Evitamos división por cero
                                    $porcentaje = ($capacidad > 0) ? round(($ocupacion / $capacidad) * 100) : 0;
                                    
                                    // Color según ocupación: Verde (bajo), Amarillo (medio), Rojo (lleno)
                                    $color = ($porcentaje >= 100) ? '#e74c3c' : (($porcentaje > 70) ? '#f1c40f' : '#2ecc71');
                                ?>
                                <div style="background:#ecf0f1; border-radius:5px; width:100px; height:20px; position:relative;">
                                    <div style="background:<?php echo $color; ?>; width:<?php echo $porcentaje; ?>%; height:100%; border-radius:5px; transition: width 0.3s;"></div>
                                    <span style="position:absolute; left:50%; top:50%; transform:translate(-50%, -50%); font-size:10px; color:#333;">
                                        <?php echo $ocupacion . " / " . $capacidad; ?>
                                    </span>
                                </div>
                            </td>

                            <td>
                                <?php if ($ocupacion >= $capacidad): ?>
                                    <span class="badge-danger">⛔ LLENO</span>
                                <?php else: ?>
                                    <span class="badge-success">✅ Disponible</span>
                                <?php endif; ?>
                            </td>

                            <td class="actions-cell">
                                <a href="habitat_edit.php?id=<?php echo $h['id']; ?>" class="btn-edit">✏️</a>
                                
                                <?php if (esAdmin()): ?>
                                    <a href="../../actions/habitats/habitat_delete.php?id=<?php echo $h['id']; ?>" 
                                        class="btn-delete" 
                                        onclick="return confirm('¿Seguro? Si borras el hábitat, los animales quedarán sin casa.');">🗑️</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center">No hay hábitats registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>