<?php
// 1. INCLUIR EL ENCABEZADO
// Esto carga el menú, los estilos CSS y la lógica de sesión inicial.
require '../../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Iniciar Sesión</h2>
        <p>Ingresa tus credenciales para acceder.</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']); // Limpiamos el error después de mostrarlo
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']); 
                ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>actions/auth/login_action.php" method="POST" class="form-standard">
            
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" id="email" required placeholder="tu@email.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required placeholder="Tu contraseña secreta">
            </div>

            <button type="submit" class="btn-submit">Ingresar al Sistema</button>
        </form>

        <p class="auth-link">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>
</div>

<?php
// 6. INCLUIR EL PIE DE PÁGINA
require '../../includes/footer.php';
?>