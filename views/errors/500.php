<?php
// views/errors/500.php
// Definimos la ruta base manualmente para cargar los estilos
$base_url = "http://localhost/zoo-system/";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del Servidor - ZooManager</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/light.css">
    <style>
        body {
            background-color: #f0f2f5; /* Un gris claro para el fondo */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            text-align: center;
            padding: 3rem;
            border-radius: 1.5rem;
        }
        .error-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="error-container glassmorphism">
                    <img src="<?php echo $base_url; ?>assets/img/500-workers.jpg" alt="Cuidadores arreglando" class="error-image">

                    <h1 class="display-4 fw-bold text-dark mb-2">¡Ay! Error del Servidor</h1>
                    <p class="lead text-muted mb-4">
                        Algo ha salido mal en nuestro sistema. Nuestro equipo de expertos (y algunos monos) ya está trabajando para solucionarlo.
                    </p>

                    <div class="d-grid gap-2 d-sm-flex justify-content-center">
                        <a href="<?php echo $base_url; ?>index.php" class="btn btn-primary btn-lg px-4 py-2 rounded-pill">
                            <i class="bi bi-house-door-fill me-2"></i> Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>