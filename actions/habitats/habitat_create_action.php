<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/functions.php'; // Necesario para la función limpiar

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Limpieza de datos
    $nombre = limpiar($_POST['nombre']);
    $clima = limpiar($_POST['clima']);
    $climas_permitidos = obtenerClimasValidos();
    $capacidad = (int) $_POST['capacidad'];
    $descripcion = limpiar($_POST['descripcion']);

    if (!in_array($clima, $climas_permitidos)) {
        $_SESSION['error'] = "Error de seguridad: El clima '$clima' no es válido.";
        // Opcional: Podrías loguear esto como un intento de hackeo
        header("Location: ../../views/admin/habitat_create.php");
        exit();
    }

    if (strlen($nombre) > 50) {
        $_SESSION['error'] = "El nombre del hábitat es muy largo (Máx 50 caracteres).";
        header("Location: ../../views/admin/habitat_create.php");
        exit();
    }

    // 2. Validación básica
    if (empty($nombre) || empty($clima) || $capacidad < 1) {
        $_SESSION['error'] = "Por favor, revisa los campos. La capacidad debe ser al menos 1.";
        header("Location: ../../views/admin/habitat_create.php");
        exit();
    }

    try {
        // 3. Verificar duplicados
        // Regla de negocio: No pueden haber dos hábitats con el mismo nombre exacto
        $check = $pdo->prepare("SELECT id FROM habitats WHERE nombre = ?");
        $check->execute([$nombre]);
        
        if ($check->rowCount() > 0) {
            $_SESSION['error'] = "Error: Ya existe un hábitat llamado '$nombre'.";
            header("Location: ../../views/admin/habitat_create.php");
            exit();
        }

        // 4. Insertar
        $sql = "INSERT INTO habitats (nombre, clima, capacidad, descripcion) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nombre, $clima, $capacidad, $descripcion])) {
            $_SESSION['success'] = "Hábitat '$nombre' creado exitosamente.";
            header("Location: ../../views/admin/habitats.php");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error en base de datos: " . $e->getMessage();
        header("Location: ../../views/admin/habitat_create.php");
        exit();
    }
}