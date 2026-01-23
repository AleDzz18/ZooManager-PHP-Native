<?php require '../../includes/header.php'; ?>

<?php if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();}?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Iniciar Sesión</h2>
        <p>Ingresa tus credenciales para acceder.</p>

        <?php echo mostrarAlertas(); ?>

        <form action="<?php echo BASE_URL; ?>actions/auth/login_action.php" method="POST" class="form-standard">
            
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" id="email" required autofocus placeholder="tu@email.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required placeholder="Tu contraseña">
            </div>

            <button type="submit" class="btn-submit">Ingresar al Sistema</button>
        </form>

        <p class="auth-link">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>
</div>
<?php require '../../includes/footer.php'; ?>