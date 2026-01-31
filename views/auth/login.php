<?php 
/**
 * VISTA: INICIAR SESIÓN
 */

// 1. LÓGICA DE SESIÓN
if (session_status() === PHP_SESSION_NONE) session_start();

if (!defined('BASE_URL')) define('BASE_URL', 'http://localhost/zoo-system/');

// Si ya está logueado, fuera de aquí
if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// 2. CABECERAS ANTI-CACHÉ
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 3. VISTA
$pageTitle = "Iniciar Sesión";
require '../../includes/header.php'; 
?>

<style>
    /* Efecto: El placeholder desaparece al hacer clic (focus) */
    input:focus::placeholder {
        color: transparent !important;
        transition: color 0.2s ease; /* Pequeña animación suave */
    }
</style>

<div class="auth-container">
    <div class="auth-card text-center">
        
        <img src="<?php echo BASE_URL; ?>assets/img/leon_logo.png" 
            alt="Logo ZooManager" width="100" height="100" 
            class="rounded-circle logo-auth mb-4">

        <h2 class="fw-bold text-primary">Bienvenido</h2>
        <p class="text-muted mb-4">Ingresa tus credenciales para continuar</p>

        <?php echo mostrarAlertas(); ?>

        <form action="<?php echo BASE_URL; ?>actions/auth/login_action.php" method="POST" class="text-start">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" 
                    required autofocus autocomplete="username" 
                    placeholder="nombre@ejemplo.com">
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" 
                    required autocomplete="current-password" 
                    placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-material-primary w-100 py-2 mb-3 shadow-sm">
                Entrar al Sistema
            </button>
        </form>

        <p class="auth-link mb-0 small">
            ¿No tienes cuenta? <a href="register.php" class="text-primary fw-bold text-decoration-none">Regístrate aquí</a>
        </p>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>