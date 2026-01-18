<?php require '../../includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Registro de Personal</h2>
        <p>Crea una cuenta para acceder al sistema ZooManager.</p>

        <?php echo mostrarAlertas(); ?>

        <form action="<?php echo BASE_URL; ?>actions/auth/register_action.php" method="POST" class="form-standard">
            
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" name="nombre_completo" id="nombre" 
                    required pattern="[A-Za-zÁéíóúÁÉÍÓÚñÑ\s]+" 
                    title="Solo se permiten letras y espacios" 
                    placeholder="Ej: Juan Pérez">
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" name="email" id="email" required placeholder="usuario@zoo.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" 
                    required minlength="8" 
                    placeholder="Mínimo 8 caracteres">
            </div>

            <div class="form-group">
                <label for="rol">Rol Asignado:</label>
                <select name="rol" id="rol" required>
                    <option value="" disabled selected>-- Selecciona un Rol --</option>
                    <option value="cuidador">Cuidador (Acceso limitado)</option>
                    <option value="admin">Administrador (Acceso total)</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Registrar Personal</button>
        </form>

        <p class="auth-link">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</div>
<?php require '../../includes/footer.php'; ?>