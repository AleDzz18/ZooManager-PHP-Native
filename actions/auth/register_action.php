<?php
// 1. INICIAR SESIÓN
// La necesitamos para guardar mensajes de error o éxito que mostraremos en la vista.
session_start();

// 2. IMPORTAR LA CONEXIÓN (La "llave" del zoológico)
// Usamos ../../ para salir de actions/auth/ y llegar a la carpeta config/
require_once '../../config/db.php';

// 3. VERIFICAR QUE LOS DATOS LLEGUEN POR POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. CAPTURAR Y LIMPIAR LOS DATOS
    // trim() elimina espacios vacíos accidentales al inicio o final.
    $nombre = trim($_POST['nombre_completo']);
    $email = trim($_POST['email']);
    $password_plana = $_POST['password']; // La clave tal cual la escribió el usuario
    $rol = $_POST['rol'];

    // 5. VALIDACIÓN BÁSICA (Manejo de errores exigido)
    if (empty($nombre) || empty($email) || empty($password_plana)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../../views/auth/register.php");
        exit();
    }

    try {
        // 6. VERIFICAR SI EL EMAIL YA EXISTE
        // Usamos una "Consulta Preparada" (PDO) para evitar Inyección SQL.
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Este correo ya está registrado en el sistema.";
            header("Location: ../../views/auth/register.php");
            exit();
        }

        // 7. ENCRIPTAR LA CONTRASEÑA (Requisito Obligatorio #1)
        // Nunca guardamos claves en texto plano. password_hash crea una versión ilegible.
        $password_encriptada = password_hash($password_plana, PASSWORD_DEFAULT);

        // 8. INSERTAR EL NUEVO USUARIO
        $sql = "INSERT INTO users (nombre_completo, email, password, rol) VALUES (?, ?, ?, ?)";
        $insert = $pdo->prepare($sql);
        $insert->execute([$nombre, $email, $password_encriptada, $rol]);

        // 9. ÉXITO: REDIRECCIONAR AL LOGIN
        $_SESSION['success'] = "Usuario registrado correctamente. Ya puedes iniciar sesión.";
        header("Location: ../../views/auth/login.php");
        exit();

    } catch (PDOException $e) {
        // 10. MANEJO DE ERRORES CRÍTICOS
        die("Error técnico en la base de datos: " . $e->getMessage());
    }

} else {
    // Si alguien intenta entrar a este archivo directamente por la URL, lo mandamos al registro.
    header("Location: ../../views/auth/register.php");
    exit();
}