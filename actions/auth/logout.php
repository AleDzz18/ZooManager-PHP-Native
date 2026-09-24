<?php
// PASO 1: ACCEDER A LA SESIÓN ACTUAL
// Necesitamos iniciar la sesión temporalmente para saber qué vamos a destruir.
session_start();
require_once '../../config/db.php'; // Importado exclusivamente para acceder a la constante BASE_URL

// PASO 2: LIMPIEZA EN MEMORIA INMEDIATA
// Vaciamos por completo el arreglo $_SESSION. Esto borra el ID de usuario, rol, etc., del script actual.
$_SESSION = [];

// PASO 3: DESTRUCCIÓN FÍSICA DE LA COOKIE
// Buscamos la galleta (cookie) que relaciona al navegador del usuario con el servidor.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    // Reemplazamos la cookie actual por una vacía ('') y establecemos su fecha de caducidad
    // en el pasado (time() - 42000), obligando al navegador a borrarla inmediatamente.
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// PASO 4: DESTRUCCIÓN EN EL SERVIDOR
// Eliminamos el archivo temporal que PHP creó en el servidor para almacenar los datos de esta sesión.
session_destroy();

// PASO 5: REDIRECCIÓN
// Enviamos al usuario limpio de vuelta al formulario de inicio de sesión.
header("Location: " . BASE_URL . "views/auth/login.php");
exit();
?>