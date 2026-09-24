<?php
/*
    ---------------------------------------------------
    PLANTILLA DE CONEXIÓN A BASE DE DATOS (DB_EXAMPLE)
    ---------------------------------------------------
    Propósito Educativo: Este archivo sirve como "molde" para el repositorio.
    Por buenas prácticas de ciberseguridad, el archivo 'db.php' real que contiene
    las contraseñas nunca debe subirse a GitHub.
    
    Instrucciones para nuevos desarrolladores:
    1. Duplica este archivo.
    2. Renómbralo a 'db.php'.
    3. Coloca tus credenciales locales en el PASO 4.
*/

// PASO 1: BLINDAJE CONTRA ACCESO DIRECTO
if (count(get_included_files()) == 1) {
    header('HTTP/1.0 403 Forbidden');
    exit("Acceso prohibido.");
}

// PASO 2: ENRUTAMIENTO DINÁMICO
define('BASE_URL', '/zoo-system/');

// PASO 3: CONTROL DE BÚFER Y ERRORES
ini_set('display_errors', 0); 
if (ob_get_level() == 0) ob_start();

// PASO 4: CREDENCIALES (MODIFICAR SEGÚN TU ENTORNO LOCAL)
$host = 'localhost';
$port = '3306'; 
$dbname = 'NOMBRE_DE_TU_BASE_DE_DATOS';
$username = 'TU_USUARIO';
$password = 'TU_CONTRASEÑA';

try {
    // PASO 5: INSTANCIAR PDO
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    if (ob_get_length()) ob_end_clean();

} catch (\PDOException $e) { 
    // PASO 6: MANEJO DE CAÍDA CRÍTICA (FALLBACK SILENCIOSO)
    if (ob_get_length()) ob_end_clean(); 

    error_log("Error Crítico BD: " . $e->getMessage());

    $error_url = BASE_URL . "views/errors/500.php";

    if (!headers_sent()) {
        header("Location: " . $error_url);
        exit();
    }

    echo '<!DOCTYPE html><html><head>';
    echo '<meta http-equiv="refresh" content="0;url='.$error_url.'">';
    echo '<script>window.location.href="'.$error_url.'";</script>';
    echo '</head><body>';
    echo '<p>Error crítico del sistema. Redirigiendo...</p>';
    echo '<a href="'.$error_url.'">Clic aquí si no se redirige automáticamente</a>';
    echo '</body></html>';
    exit();
}
?>