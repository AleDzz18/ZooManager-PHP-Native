<?php
// 1. VERIFICAR EL ESTADO DE LA SESIÓN
// Si la sesión no ha iniciado, la iniciamos para poder leer $_SESSION.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. COMPROBAR SI EL USUARIO ESTÁ LOGUEADO
// Verificamos si la variable 'user_id' existe en la sesión.
if (!isset($_SESSION['user_id'])) {
    
    // 3. MENSAJE DE SEGURIDAD
    // Guardamos un mensaje para mostrarlo en el login.
    $_SESSION['error'] = "Acceso denegado. Debes iniciar sesión para ver esa página.";

    // 4. REDIRECCIÓN FORZADA
    // Enviamos al intruso de vuelta al Login.
    // Nota: Asumimos que este archivo se usa en carpetas tipo views/admin/
    header("Location: ../../views/auth/login.php");
    
    // 5. DETENER EJECUCIÓN (CRÍTICO)
    // Sin el exit(), el código de abajo se seguiría ejecutando en segundo plano
    // aunque el navegador cambie de página. ¡Es un hueco de seguridad grave!
    exit();
}
?>