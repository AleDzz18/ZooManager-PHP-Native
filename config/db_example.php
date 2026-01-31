<?php
/*
    ---------------------------------------------------
    PLANTILLA DE CONEXIÓN A BASE DE DATOS
    ---------------------------------------------------
    Instrucciones:
    1. Duplica este archivo y renómbralo a 'db.php'.
    2. Configura las variables de abajo con tus credenciales reales.
    3. NO subas el archivo 'db.php' con contraseñas reales al repositorio.
*/

// --- SEGURIDAD: BLINDAJE CONTRA ACCESO DIRECTO ---
// Si este archivo es el único que se está ejecutando, alguien entró directo.
if (count(get_included_files()) == 1) {
    header('HTTP/1.0 403 Forbidden');
    exit("Acceso prohibido.");
}

// 1. CONTROL DE ERRORES Y BUFFER
// Desactivamos mostrar errores en pantalla para evitar que rompan la redirección
ini_set('display_errors', 0); 
// Iniciamos el buffer solo si no está activo ya
if (ob_get_level() == 0) ob_start();

// 2. CREDENCIALES (MODIFICAR SEGÚN TU ENTORNO)
$host = 'localhost';
$port = '3306';
$dbname = '';
$username = 'root';
$password = '';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    // Intentamos conectar
    $pdo = new PDO($dsn, $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Si llegamos aquí, todo salió bien. Limpiamos el buffer silenciosamente.
    if (ob_get_length()) ob_end_clean();

} catch (\PDOException $e) { // Usamos \PDOException para asegurar el namespace global
    
    // Si hay basura en el buffer, la borramos para que no salga en pantalla
    if (ob_get_length()) ob_end_clean();

    // 1. Logueamos el error real en el servidor (logs de Apache/PHP)
    error_log("Error Crítico BD: " . $e->getMessage());

    // 2. REDIRECCIÓN A VISTA DE ERROR 500
    // Ajusta la carpeta '/zoo-system/' si tu proyecto tiene otro nombre
    $error_url = "/zoo-system/views/errors/500.php";

    // Método A: HTTP Header
    if (!headers_sent()) {
        header("Location: " . $error_url);
        exit();
    }

    // Método B: Fallback HTML/JS si las cabeceras fallaron
    echo '<!DOCTYPE html><html><head>';
    echo '<meta http-equiv="refresh" content="0;url='.$error_url.'">';
    echo '<script>window.location.href="'.$error_url.'";</script>';
    echo '</head><body>';
    echo '<p>Error crítico del sistema. Redirigiendo...</p>';
    echo '</body></html>';
    exit();
}
?>