<?php
// PASO 1: INICIALIZACIÓN
// Iniciar sesión para gestionar las alertas y redirecciones del usuario.
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// PASO 2: PREVENCIÓN DE ACCESO DIRECTO
soloMetodoPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // PASO 3: RECOLECCIÓN Y LIMPIEZA DE DATOS
    // El id del animal viaja oculto en el formulario para saber a quién pertenece este registro.
    $animal_id = $_POST['animal_id'];
    $fecha = $_POST['fecha'];
    
    // Sanitizamos los campos de texto libre para evitar que inyecten código HTML o JavaScript (XSS).
    $descripcion = limpiar($_POST['descripcion']);
    $diagnostico = limpiar($_POST['diagnostico']);
    $tratamiento = limpiar($_POST['tratamiento']);
    $severidad = $_POST['severidad'];

    // PASO 4: VALIDACIÓN DE CAMPOS OBLIGATORIOS
    // Todo registro médico debe tener obligatoriamente a qué animal pertenece, qué día fue y qué pasó.
    if (empty($animal_id) || empty($descripcion) || empty($fecha)) {
        $_SESSION['error'] = "La fecha y la descripción son obligatorias.";
        header("Location: " . BASE_URL . "views/medical/medical_create.php?animal_id=" . $animal_id);
        exit();
    }

    try {
        // PASO 5: INSERCIÓN SEGURA EN LA BASE DE DATOS
        // Usamos marcadores (?) para separar la estructura SQL de los datos proporcionados por el usuario.
        $sql = "INSERT INTO medical_records (animal_id, fecha, descripcion, diagnostico, tratamiento, severidad) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        // Los parámetros deben enviarse exactamente en el mismo orden que los signos de interrogación.
        $stmt->execute([$animal_id, $fecha, $descripcion, $diagnostico, $tratamiento, $severidad]);
        
        // PASO 6: ÉXITO Y REDIRECCIÓN CONTEXTUAL
        $_SESSION['success'] = "Registro médico agregado correctamente.";
        
        // Redirigimos de vuelta al historial específico de ESE animal.
        header("Location: " . BASE_URL . "views/medical/medical_history.php?id=" . $animal_id);
        exit();

    } catch (PDOException $e) {
        // PASO 7: MANEJO SEGURO DE ERRORES TÉCNICOS
        // Si la base de datos falla, usamos nuestro gestor centralizado para no revelar detalles técnicos.
        registrarErrorCritico($e, "views/medical/medical_create.php?animal_id=" . $animal_id, "Error al guardar el registro médico.");
    }
} else {
    // Redirección por defecto si intentan saltarse la validación
    header("Location: " . BASE_URL . "views/admin/animals.php");
    exit();
}