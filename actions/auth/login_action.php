<?php
// PASO 1: INICIALIZACIÓN Y CONFIGURACIÓN
// Iniciamos la sesión para poder acceder a la variable global $_SESSION y guardar los datos del usuario.
session_start();
require_once '../../config/db.php';
require_once '../../includes/functions.php';

// PASO 2: SEGURIDAD DE ACCESO
// Bloqueamos el acceso si el usuario intenta llegar a este archivo escribiendo la URL (Método GET).
soloMetodoPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // PASO 3: RECOLECCIÓN Y LIMPIEZA DE DATOS
    // Usamos nuestra función limpiar() para sanitizar el email y evitar inyecciones de scripts (XSS).
    $email = limpiar($_POST['email']);
    
    // NOTA DE SEGURIDAD: La contraseña NO se limpia con htmlspecialchars ni trim, 
    // ya que si un usuario usa espacios o caracteres especiales válidos en su clave, los alteraríamos y no podría entrar.
    $password_ingresada = $_POST['password'];

    // PASO 4: VALIDACIÓN BÁSICA
    if (empty($email) || empty($password_ingresada)) {
        $_SESSION['error'] = "Por favor, completa todos los campos.";
        header("Location: " . BASE_URL . "views/auth/login.php");
        exit();
    }

    try {
        // PASO 5: BÚSQUEDA DEL USUARIO (PREVINIENDO INYECCIÓN SQL)
        // Preparamos la consulta (?) en lugar de concatenar la variable directamente.
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        // Obtenemos los datos del usuario en forma de array asociativo. Si no existe, devuelve false.
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // PASO 6: VERIFICACIÓN DE CREDENCIALES
        // Comprobamos 2 cosas al mismo tiempo: 
        // 1. Que el usuario exista ($usuario).
        // 2. Que la clave ingresada coincida con el "hash" guardado usando password_verify().
        if ($usuario && password_verify($password_ingresada, $usuario['password'])) {
            
            // --- ¡LOGIN EXITOSO! ---

            // PASO 7: ACTUALIZAR ÚLTIMO ACCESO (OPCIONAL/SILENCIOSO)
            // Intentamos guardar la fecha y hora de este login. 
            // Si la base de datos falla en esto, ignoramos el error para no arruinarle el login al usuario.
            try {
                $sql_update = "UPDATE users SET ultimo_acceso = NOW() WHERE id = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([$usuario['id']]);
            } catch (PDOException $e) {
                // Falla silenciosa para el log de acceso
            }

            // PASO 8: PREVENCIÓN DE SECUESTRO DE SESIÓN (SESSION HIJACKING)
            // Regeneramos el ID de la sesión. Esto cambia la "cookie" interna del navegador.
            // Si un atacante robó el ID de sesión viejo, este paso lo vuelve inútil.
            session_regenerate_id(true);
            
            // PASO 9: CREACIÓN DE VARIABLES GLOBALES DE SESIÓN
            // Guardamos los datos que necesitaremos en todo el sistema.
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nombre_completo'];
            $_SESSION['user_role'] = $usuario['rol'];

            // Redireccionamos al panel principal.
            header("Location: " . BASE_URL . "index.php");
            exit();

        } else {
            // --- LOGIN FALLIDO ---
            // NOTA DE SEGURIDAD: Nunca especificamos qué falló (si el correo o la clave).
            // Usamos un mensaje genérico ("Credenciales incorrectas") para no dar pistas a posibles atacantes 
            // sobre qué correos están registrados en nuestro sistema.
            $_SESSION['error'] = "Credenciales incorrectas. Inténtalo de nuevo.";
            header("Location: " . BASE_URL . "views/auth/login.php");
            exit();
        }

    } catch (PDOException $e) {
        // Manejador de errores para evitar la "Pantalla Blanca de la Muerte" y proteger detalles técnicos.
        registrarErrorCritico($e, "views/auth/login.php", "Error en el sistema al intentar iniciar sesión.");
    }

} else {
    // Redirección por si esquivan soloMetodoPost()
    header("Location: " . BASE_URL . "views/auth/login.php");
    exit();
}