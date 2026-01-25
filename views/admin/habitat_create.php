<?php
require '../../includes/auth_check.php';
require '../../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 650px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-0">Construir Nuevo Hábitat</h2>
                <div class="text-muted small">Define las características del entorno</div>
            </div>
            <a href="habitats.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-x-lg"></i> Cancelar
            </a>
        </div>

        <?php echo mostrarAlertas(); ?>

        <form action="../../actions/habitats/habitat_create_action.php" method="POST">
            
            <div class="mb-3">
                <label for="nombre" class="form-label fw-semibold">Nombre del Hábitat</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Ej: Sabana Africana Norte">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="clima" class="form-label fw-semibold">Tipo de Clima</label>
                    <select name="clima" id="clima" class="form-select" required>
                        <option value="" disabled selected>-- Selecciona --</option>
                        <option value="Selva">🌴 Selva Tropical</option>
                        <option value="Desierto">🌵 Desierto</option>
                        <option value="Acuático">💧 Acuático</option>
                        <option value="Polar">❄️ Polar</option>
                        <option value="Aviario">🦅 Aviario</option>
                        <option value="Sabana">🦁 Sabana</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="capacidad" class="form-label fw-semibold">Capacidad Máxima</label>
                    <input type="number" name="capacidad" id="capacidad" class="form-control" required min="1" placeholder="Ej: 10">
                    <div class="form-text text-muted small">
                        <i class="bi bi-people-fill"></i> Máximo de animales permitidos.
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="form-label fw-semibold">Descripción <span class="fw-light text-muted">(Opcional)</span></label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="3" placeholder="Detalles sobre temperatura, vegetación, etc..."></textarea>
            </div>

            <button type="submit" class="btn btn-material-primary w-100 py-2">
                <i class="bi bi-hammer me-2"></i> Registrar Hábitat
            </button>
        </form>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>