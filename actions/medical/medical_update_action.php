<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// PASO 1: BLOQUEO POR SEGURIDAD
soloMetodoPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // PASO 2: EXTRACCIÓN DE METADATOS Y LIMPIEZA
    $id = $_POST['id'];               // El ID de la consulta médica que estamos editando
    $animal_id = $_POST['animal_id']; // El paciente (vital para saber a dónde volver luego)
    
    $fecha = $_POST['fecha'];
    
    // Limpiamos todo el texto ingresado por el usuario para prevenir inyección de scripts HTML/JS
    $descripcion = limpiar($_POST['descripcion']);
    $diagnostico = limpiar($_POST['diagnostico']);
    $tratamiento = limpiar($_POST['tratamiento']);
    $severidad = $_POST['severidad'];

    // PASO 3: VALIDACIÓN BÁSICA
    if (empty($descripcion) || empty($fecha)) {
        $_SESSION['error'] = "La fecha y la descripción no pueden estar vacías.";
        // Si hay un error, devolvemos al usuario al formulario de edición de este registro específico.
        header("Location: " . BASE_URL . "views/medical/medical_edit.php?id=" . $id);
        exit();
    }

    try {
        // PASO 4: ACTUALIZACIÓN BLINDADA (PREPARED STATEMENTS)
        $sql = "UPDATE medical_records 
                SET fecha = ?, 
                    descripcion = ?, 
                    diagnostico = ?, 
                    tratamiento = ?, 
                    severidad = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        
        // IMPORTANTE: El orden de este arreglo debe coincidir exactamente con las posiciones de los '?' en el UPDATE.
        $stmt->execute([$fecha, $descripcion, $diagnostico, $tratamiento, $severidad, $id]);

        // PASO 5: REDIRECCIÓN AL CONTEXTO ORIGINAL
        $_SESSION['success'] = "Registro médico actualizado correctamente.";
        header("Location: " . BASE_URL . "views/medical/medical_history.php?id=" . $animal_id);
        exit();

    } catch (PDOException $e) {
        // PASO 6: PROTECCIÓN CONTRA FUGAS DE INFORMACIÓN (INFORMATION DISCLOSURE)
        registrarErrorCritico($e, "views/medical/medical_edit.php?id=" . $id, "Error al actualizar el registro médico.");
    }
} else {
    header("Location: " . BASE_URL . "views/admin/animals.php");
    exit();
}