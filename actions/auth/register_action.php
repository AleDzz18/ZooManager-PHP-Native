<?php
// PASO 1: INICIALIZACIÓN
session_start();
require_once '../../config/db.php';
require_once '../../includes/functions.php';

// PASO 2: SEGURIDAD
soloMetodoPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // PASO 3: CAPTURA Y SANITIZACIÓN DE DATOS
    $nombre = limpiar($_POST['nombre_completo']);
    $email = limpiar($_POST['email']);
    $password_plana = $_POST['password']; // Se captura tal cual la escribió el usuario (Texto Plano)
    $rol = $_POST['rol'];

    // PASO 4: VALIDACIÓN BÁSICA
    if (empty($nombre) || empty($email) || empty($password_plana)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: " . BASE_URL . "views/auth/register.php");
        exit();
    }

    try {
        // PASO 5: VERIFICACIÓN DE DUPLICIDAD DE CORREOS
        // Es vital revisar si el correo ya existe antes de intentar guardarlo para evitar errores de base de datos (Unique Key constraints).
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        // Si el fetch() encuentra algo, significa que el correo ya está en uso.
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Este correo ya está registrado en el sistema.";
            header("Location: " . BASE_URL . "views/auth/register.php");
            exit();
        }

        // PASO 6: ENCRIPTACIÓN DE CONTRASEÑA (HASHING)
        // REGLA DE ORO: Nunca, jamás guardamos contraseñas legibles en la base de datos.
        // password_hash() utiliza el algoritmo BCrypt por defecto. Genera una cadena alfanumérica
        // segura e irreversible. Además, genera una "sal" (salt) automática para cada clave.
        $password_encriptada = password_hash($password_plana, PASSWORD_DEFAULT);

        // PASO 7: INSERCIÓN EN LA BASE DE DATOS
        $sql = "INSERT INTO users (nombre_completo, email, password, rol) VALUES (?, ?, ?, ?)";
        $insert = $pdo->prepare($sql);
        // Enviamos la clave ENCRIPTADA al execute, no la plana.
        $insert->execute([$nombre, $email, $password_encriptada, $rol]);

        // PASO 8: ÉXITO Y REDIRECCIÓN
        $_SESSION['success'] = "Usuario registrado correctamente. Ya puedes iniciar sesión.";
        header("Location: " . BASE_URL . "views/auth/login.php");
        exit();

    } catch (PDOException $e) {
        // Manejador seguro para evitar mostrar errores de tablas al usuario si algo falla.
        registrarErrorCritico($e, "views/auth/register.php", "Error al intentar registrar el usuario. Inténtelo más tarde.");
    }

} else {
    // Redirección en caso de intentar acceder por URL directa
    header("Location: " . BASE_URL . "views/auth/register.php");
    exit();
}