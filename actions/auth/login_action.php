<?php
// 1. INICIAR SESIÓN
// Necesario para guardar los datos del usuario si el login es exitoso.
session_start();

// 2. CONEXIÓN A LA BASE DE DATOS
require_once '../../config/db.php';
require_once '../../includes/functions.php';

// 3. VERIFICAR MÉTODO POST
// Solo procesamos si los datos vienen del formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4. LIMPIEZA DE DATOS
    $email = limpiar($_POST['email']);
    $password_ingresada = $_POST['password'];

    // 5. VALIDACIÓN DE CAMPOS VACÍOS
    if (empty($email) || empty($password_ingresada)) {
        $_SESSION['error'] = "Por favor, completa todos los campos.";
        header("Location: ../../views/auth/login.php");
        exit();
    }

    try {
        // 6. BUSCAR AL USUARIO POR EMAIL
        // Preparamos la consulta SQL para evitar inyección de código.
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        // fetch(PDO::FETCH_ASSOC) nos devuelve los datos del usuario como un array asociativo.
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 7. VERIFICACIÓN DE CREDENCIALES
        // Revisamos dos cosas:
        // A) ¿Existe el usuario? ($usuario no es falso)
        // B) ¿La contraseña coincide? Usamos password_verify() que compara texto plano vs hash.
        if ($usuario && password_verify($password_ingresada, $usuario['password'])) {
            
            // --- ¡LOGIN EXITOSO! ---

            // 8. GUARDAR DATOS EN LA SESIÓN
            // Estas variables estarán disponibles en TODAS las páginas (header.php las usa).
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nombre_completo'];
            $_SESSION['user_role'] = $usuario['rol']; // Importante para permisos de Admin/Cuidador

            // 9. REDIRECCIONAR AL DASHBOARD
            // Por ahora vamos al inicio, pero luego iremos al panel de administración.
            header("Location: ../../index.php");
            exit();

        } else {
            // --- LOGIN FALLIDO ---
            
            // Nota de Seguridad: No decimos "El usuario no existe" o "La contraseña está mal".
            // Decimos "Credenciales incorrectas" para no dar pistas a hackers.
            $_SESSION['error'] = "Credenciales incorrectas. Inténtalo de nuevo.";
            header("Location: ../../views/auth/login.php");
            exit();
        }

    } catch (PDOException $e) {
        // 10. ERROR TÉCNICO
        die("Error en el sistema de login: " . $e->getMessage());
    }

} else {
    // Si intentan entrar directo por URL, los devolvemos al formulario.
    header("Location: ../../views/auth/login.php");
    exit();
}