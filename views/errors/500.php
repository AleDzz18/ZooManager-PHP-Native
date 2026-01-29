<?php
// views/errors/500.php
// NO incluimos db.php ni header.php para evitar bucles infinitos
// Definimos la ruta base manualmente para cargar los estilos
$base_url = "http://localhost/zoo-system/"; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Crítico - ZooManager</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/bootstrap-icons/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .error-card {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .icon-danger {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <div class="error-card">
        <div class="icon-danger">
            <i class="bi bi-exclamation-octagon-fill"></i>
        </div>
        
        <h1 class="display-4 fw-bold text-dark mb-2">Error 500</h1>
        <h3 class="text-secondary mb-4">Fallo de Conexión</h3>

        <div class="d-grid gap-2 d-sm-flex justify-content-center">
            <a href="<?php echo $base_url; ?>index.php" class="btn btn-primary px-4 py-2">
                <i class="bi bi-arrow-clockwise me-2"></i> Reintentar Conexión
            </a>
        </div>
    </div>

</body>
</html>