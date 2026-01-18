<?php
require '../../includes/auth_check.php';
require '../../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 600px;">
        <div class="admin-header">
            <h2>Construir Nuevo Hábitat</h2>
            <a href="habitats.php" class="btn-delete" style="background:#7f8c8d;">Volver</a>
        </div>

        <?php echo mostrarAlertas(); ?>

        <form action="../../actions/habitats/habitat_create_action.php" method="POST" class="form-standard">
            
            <div class="form-group">
                <label>Nombre del Hábitat:</label>
                <input type="text" name="nombre" required placeholder="Ej: Sabana Africana Norte">
            </div>

            <div class="row" style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label>Tipo de Clima:</label>
                    <select name="clima" required>
                        <option value="Selva">🌴 Selva Tropical</option>
                        <option value="Desierto">🌵 Desierto</option>
                        <option value="Acuático">💧 Acuático</option>
                        <option value="Polar">❄️ Polar</option>
                        <option value="Aviario">🦅 Aviario</option>
                        <option value="Sabana">🦁 Sabana</option>
                    </select>
                </div>

                <div class="form-group" style="flex:1;">
                    <label>Capacidad Máxima:</label>
                    <input type="number" name="capacidad" required min="1" placeholder="Ej: 10">
                    <small style="color:#666;">Máximo de animales permitidos.</small>
                </div>
            </div>

            <div class="form-group">
                <label>Descripción:</label>
                <textarea name="descripcion" rows="3" placeholder="Detalles sobre temperatura, vegetación..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Registrar Hábitat</button>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>