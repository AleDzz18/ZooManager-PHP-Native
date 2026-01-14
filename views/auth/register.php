<?php
// 1. Incluimos el header para mantener el menú y el diseño 
// Usamos ../../ para subir dos niveles y encontrar la carpeta includes
require '../../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Registro de Personal</h2>
        <p>Crea una cuenta para acceder al sistema ZooManager.</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']); // Borramos el error para que no aparezca de nuevo al refrescar
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

        <form action="<?php echo BASE_URL; ?>actions/auth/register_action.php" method="POST" class="form-standard">
            
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" name="nombre_completo" id="nombre" required placeholder="Ej: Juan Pérez">
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" id="email" required placeholder="usuario@zoológico.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required minlength="6" placeholder="Mínimo 6 caracteres">
            </div>

            <div class="form-group">
                <label for="rol">Rol Asignado:</label>
                <select name="rol" id="rol" required>
                    <option value="cuidador">Cuidador (Acceso limitado)</option>
                    <option value="admin">Administrador (Acceso total)</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Registrar Personal</button>
        </form>

        <p class="auth-link">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</div>

<?php
// 3. Incluimos el footer para cerrar las etiquetas HTML
require '../../includes/footer.php';
?>