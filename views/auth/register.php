<?php 
/**
 * VISTA: REGISTRO DE USUARIO
 */

// 1. LÓGICA DE SESIÓN (Al principio)
if (session_status() === PHP_SESSION_NONE) session_start();

if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/zoo-system/');

if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// 2. CABECERAS ANTI-CACHÉ
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 3. VISTA
$pageTitle = "Registro de Personal";
require '../../includes/header.php'; 
?>

<style>
    input:focus::placeholder {
        color: transparent !important;
        transition: color 0.2s ease;
    }
</style>

<div class="auth-container">
    <div class="auth-card" style="max-width: 550px;">
        <div class="text-center">
            <img src="<?php echo BASE_URL; ?>assets/img/leon_logo.png" 
                alt="Logo ZooManager" width="80" height="80" 
                class="rounded-circle logo-auth mb-3">
            
            <h2 class="fw-bold text-primary">Crear Cuenta</h2>
            <p class="text-muted">Completa los datos del nuevo personal</p>
        </div>

        <?php echo mostrarAlertas(); ?>

        <form action="<?php echo BASE_URL; ?>actions/auth/register_action.php" method="POST" class="mt-4">
            
            <div class="mb-3">
                <label for="nombre" class="form-label fw-semibold">Nombre Completo</label>
                <input type="text" name="nombre_completo" id="nombre" class="form-control" 
                    required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+" title="Solo letras y espacios" 
                    autocomplete="name"
                    placeholder="Ej: Juan Pérez">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" 
                    required autocomplete="email" 
                    placeholder="usuario@zoo.com">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label fw-semibold">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" 
                        required minlength="8" autocomplete="new-password" 
                        placeholder="Mínimo 8 caracteres">
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

            <button type="submit" class="btn btn-material-primary w-100 py-2 mt-3 mb-3 shadow-sm">
                Registrar Personal
            </button>
        </form>

        <p class="auth-link text-center mb-0 small">
            ¿Ya tienes una cuenta? <a href="login.php" class="text-primary fw-bold text-decoration-none">Inicia sesión aquí</a>
        </p>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>