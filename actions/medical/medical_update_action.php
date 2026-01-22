<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// 1. SOLO PROCESAR SI ES POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. CAPTURAR DATOS DEL FORMULARIO
    $id = $_POST['id'];             // ID del registro médico
    $animal_id = $_POST['animal_id']; // ID del animal (para volver al historial)
    
    $fecha = $_POST['fecha'];
    $descripcion = limpiar($_POST['descripcion']);
    $diagnostico = limpiar($_POST['diagnostico']);
    $tratamiento = limpiar($_POST['tratamiento']);
    $severidad = $_POST['severidad'];

    // 3. VALIDACIÓN BÁSICA
    if (empty($descripcion) || empty($fecha)) {
        $_SESSION['error'] = "La fecha y la descripción no pueden estar vacías.";
        // Si falla, volvemos al formulario de edición
        header("Location: ../../views/medical/medical_edit.php?id=" . $id);
        exit();
    }

    try {
        // 4. ACTUALIZAR EN BASE DE DATOS
        $sql = "UPDATE medical_records 
                SET fecha = ?, 
                    descripcion = ?, 
                    diagnostico = ?, 
                    tratamiento = ?, 
                    severidad = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        // El orden del array debe coincidir con los signos de interrogación (?)
        $stmt->execute([$fecha, $descripcion, $diagnostico, $tratamiento, $severidad, $id]);

        // 5. MENSAJE DE ÉXITO Y REDIRECCIÓN
        $_SESSION['success'] = "Registro médico actualizado correctamente.";
        
        // Redirigimos al historial del animal (usando su ID)
        header("Location: ../../views/medical/medical_history.php?id=" . $animal_id);
        exit();

    } catch (PDOException $e) {
        // ERROR TÉCNICO
        $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
        header("Location: ../../views/medical/medical_edit.php?id=" . $id);
        exit();
    }
} else {
    // Si intentan entrar directo, fuera.
    header("Location: ../../views/admin/animals.php");
    exit();
}