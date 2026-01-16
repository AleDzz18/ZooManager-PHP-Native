<?php
// 1. INICIAR LA SESIÓN
// Aunque suene contradictorio, necesitamos "entrar" a la sesión actual para poder destruirla.
session_start();

// 2. LIMPIAR TODAS LAS VARIABLES DE SESIÓN
// Vaciamos el array $_SESSION para borrar datos como el ID, nombre y rol de la memoria inmediata.
$_SESSION = [];

// 3. DESTRUIR LA COOKIE DE SESIÓN (Limpieza profunda)
// Esto borra la "llave" física que estaba guardada en el navegador del usuario.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. DESTRUIR LA SESIÓN EN EL SERVIDOR
// Esto elimina el archivo físico en el servidor donde se guardaban los datos.
session_destroy();

// 5. REDIRECCIONAR AL LOGIN
// Enviamos al usuario fuera del sistema.
header("Location: ../../views/auth/login.php");
exit();
?>