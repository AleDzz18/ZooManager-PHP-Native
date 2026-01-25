<?php 
$pageTitle = "Iniciar Sesión";
require '../../includes/header.php'; 

if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
?>

<div class="auth-container">
    <div class="auth-card text-center">
        <img src="<?php echo BASE_URL; ?>assets/img/leon_logo.png" 
            alt="Logo" width="100" height="100" 
            class="rounded-circle logo-auth mb-4">

        <h2>Bienvenido</h2>
        <p class="text-muted mb-4">Ingresa tus credenciales para continuar</p>

        <?php echo mostrarAlertas(); ?>

        <form action="<?php echo BASE_URL; ?>actions/auth/login_action.php" method="POST" class="text-start">
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" 
                    required autofocus placeholder="nombre@ejemplo.com">
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" 
                    required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-material-primary w-100 py-2 mb-3">
                Entrar al Sistema
            </button>
        </form>

        <p class="auth-link mb-0">
            ¿No tienes cuenta? <a href="register.php" class="text-primary fw-bold text-decoration-none">Regístrate aquí</a>
        </p>
    </div>
</div>

<?php require '../../includes/footer.php'; ?>