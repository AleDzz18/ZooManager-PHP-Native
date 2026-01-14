<?php 
require 'includes/header.php'; // Carga el menú y el CSS
?>

<div style="text-align: center; padding: 50px 0;">
    <h1>🦁 Bienvenido al Sistema ZooManager</h1>
    <p>Gestión integral de hábitats y animales.</p>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        <p>Por favor, inicia sesión para gestionar el zoológico.</p>
    <?php else: ?>
        <p>Sistema listo para operar.</p>
    <?php endif; ?>
</div>

<?php 
require 'includes/footer.php'; // Carga el pie de página
?>