<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. CAPTURAR DATOS
    $animal_id = $_POST['animal_id'];
    $fecha = $_POST['fecha'];
    $descripcion = limpiar($_POST['descripcion']);
    $diagnostico = limpiar($_POST['diagnostico']);
    $tratamiento = limpiar($_POST['tratamiento']);

    // 2. VALIDAR
    if (empty($animal_id) || empty($descripcion) || empty($fecha)) {
        $_SESSION['error'] = "La fecha y la descripción son obligatorias.";
        header("Location: ../../views/medical/medical_create.php?animal_id=" . $animal_id);
        exit();
    }

    try {
        // 3. INSERTAR EN LA BASE DE DATOS
        // Usamos la tabla medical_records que ya definiste en el SQL
        $sql = "INSERT INTO medical_records (animal_id, fecha, descripcion, diagnostico, tratamiento) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$animal_id, $fecha, $descripcion, $diagnostico, $tratamiento]);

        // 4. ÉXITO
        $_SESSION['success'] = "Registro médico agregado correctamente.";
        
        // Regresamos al historial de ESE animal específico
        header("Location: ../../views/medical/medical_history.php?id=" . $animal_id);
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al guardar: " . $e->getMessage();
        header("Location: ../../views/medical/medical_create.php?animal_id=" . $animal_id);
        exit();
    }
} else {
    // Si no es POST, redirigimos al listado de animales
    header("Location: ../../views/admin/animals.php");
    exit();
}