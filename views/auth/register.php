<?php 
// --- EVITAR CACHÉ DEL NAVEGADOR ---
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$pageTitle = "Registro de Personal";
require '../../includes/header.php'; 

if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
?>

<div class="auth-container">
    <div class="auth-card" style="max-width: 500px;">
        <div class="text-center">
            <img src="<?php echo BASE_URL; ?>assets/img/leon_logo.png" 
                alt="Logo" width="80" height="80" 
                class="rounded-circle logo-auth mb-3">
            <h2>Crear Cuenta</h2>
            <p class="text-muted">Completa los datos del nuevo personal</p>
        </div>

        <?php echo mostrarAlertas(); ?>

        <form action="<?php echo BASE_URL; ?>actions/auth/register_action.php" method="POST" class="mt-4">
            
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" name="nombre_completo" id="nombre" class="form-control" 
                    required pattern="[A-Za-zÁéíóúÁÉÍÓÚñÑ\s]+" placeholder="Ej: Juan Pérez">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" 
                    required placeholder="usuario@zoo.com">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" 
                        required minlength="8" placeholder="Mínimo 8 caracteres">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="rol" class="form-label">Rol Asignado</label>
                    <select name="rol" id="rol" class="form-select form-control" required>
                        <option value="" disabled selected>Seleccionar...</option>
                        <option value="cuidador">Cuidador</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-material-primary w-100 py-2 mt-3 mb-3">
                Registrar Personal
            </button>
        </form>

        <p class="auth-link text-center mb-0">
            ¿Ya tienes una cuenta? <a href="login.php" class="text-primary fw-bold text-decoration-none">Inicia sesión aquí</a>
        </p>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>