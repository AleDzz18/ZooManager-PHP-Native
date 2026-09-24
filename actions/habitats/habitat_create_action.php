<?php
// PASO 1: INICIALIZACIÓN
// Necesitamos session_start() para poder enviar mensajes de error o éxito de vuelta a la vista.
session_start();
require_once '../../config/db.php';
require_once '../../includes/functions.php';

// PASO 2: PROTECCIÓN DE RUTA
// Evitamos que un usuario ingrese a este script de procesamiento tecleando la URL en su navegador.
soloMetodoPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // PASO 3: LIMPIEZA DE DATOS (SANITIZACIÓN)
    // Pasamos todas las entradas de texto por limpiar() para neutralizar etiquetas HTML y evitar ataques XSS.
    $nombre = limpiar($_POST['nombre']);
    $clima = limpiar($_POST['clima']);
    $climas_permitidos = obtenerClimasValidos(); // Obtenemos la lista blanca (whitelist) de climas permitidos
    $capacidad = (int) $_POST['capacidad']; // Forzamos el tipo de dato a entero por seguridad
    $descripcion = limpiar($_POST['descripcion']);

    // PASO 4: VALIDACIÓN DE LISTA BLANCA (WHITELISTING)
    // Protegemos el sistema contra alteraciones en el inspector del navegador (F12).
    // Si alguien intenta enviar un clima inventado, el sistema lo rechaza.
    if (!in_array($clima, $climas_permitidos)) {
        $_SESSION['error'] = "Error de seguridad: El clima '$clima' no es válido.";
        header("Location: " . BASE_URL . "views/admin/habitat_create.php");
        exit();
    }

    // PASO 5: RESTRICCIÓN DE LONGITUD
    // Prevenimos desbordamientos en la base de datos asegurando que el nombre no exceda el límite de la columna.
    if (strlen($nombre) > 50) {
        $_SESSION['error'] = "El nombre del hábitat es muy largo (Máx 50 caracteres).";
        header("Location: " . BASE_URL . "views/admin/habitat_create.php");
        exit();
    }

    // PASO 6: VALIDACIÓN DE REQUISITOS MÍNIMOS
    // Un hábitat no tiene sentido si su capacidad es 0 o menor.
    if (empty($nombre) || empty($clima) || $capacidad < 1) {
        $_SESSION['error'] = "Por favor, revisa los campos. La capacidad debe ser al menos 1.";
        header("Location: " . BASE_URL . "views/admin/habitat_create.php");
        exit();
    }

    try {
        // PASO 7: PREVENCIÓN DE DUPLICADOS (REGLA DE NEGOCIO)
        // Consultamos la base de datos para asegurarnos de que no exista otro hábitat con el mismo nombre.
        $check = $pdo->prepare("SELECT id FROM habitats WHERE nombre = ?");
        $check->execute([$nombre]);
        
        // Si rowCount() es mayor a 0, significa que la consulta encontró una coincidencia.
        if ($check->rowCount() > 0) {
            $_SESSION['error'] = "Error: Ya existe un hábitat llamado '$nombre'.";
            header("Location: " . BASE_URL . "views/admin/habitat_create.php");
            exit();
        }

        // PASO 8: INSERCIÓN SEGURA
        // Usamos sentencias preparadas con (?) para evitar inyecciones SQL.
        $sql = "INSERT INTO habitats (nombre, clima, capacidad, descripcion) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nombre, $clima, $capacidad, $descripcion])) {
            $_SESSION['success'] = "Hábitat '$nombre' creado exitosamente.";
            header("Location: " . BASE_URL . "views/admin/habitats.php");
            exit();
        }

    } catch (PDOException $e) {
        // PASO 9: MANEJO DE ERRORES CRÍTICOS SILENCIOSO
        registrarErrorCritico($e, "views/admin/habitat_create.php", "Error al crear el hábitat en la base de datos.");
    }
}