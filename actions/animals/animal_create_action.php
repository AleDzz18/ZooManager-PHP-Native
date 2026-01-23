<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php'; // Solo usuarios logueados crean
require_once '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiar($_POST['nombre']);
    $especie = limpiar($_POST['especie']);
    $edad = limpiar($_POST['edad']);
    $dieta = limpiar($_POST['dieta']);
    $fecha = $_POST['fecha_llegada'];
    $habitat_id = $_POST['habitat_id'];

    if (empty($nombre) || empty($especie) || empty($habitat_id)) {
        $_SESSION['error'] = "Por favor completa todos los campos obligatorios.";
        header("Location: ../../views/admin/animal_create.php");
        exit();
    }

    try {
        // --- INICIO DE VALIDACIÓN COMPLEJA ---
        
        // 1. Consultar la capacidad máxima del hábitat y cuántos animales tiene ya.
        $sql_check = "SELECT 
                        h.capacidad, 
                        (SELECT COUNT(*) FROM animals WHERE habitat_id = h.id) as total_actual 
                        FROM habitats h 
                        WHERE h.id = ?";
        
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$habitat_id]);
        $info_habitat = $stmt_check->fetch();

        // 2. Lógica de negocio: Comparar capacidad vs actual
        if ($info_habitat['total_actual'] >= $info_habitat['capacidad']) {
            $_SESSION['error'] = "El hábitat seleccionado ya alcanzó su capacidad máxima ({$info_habitat['capacidad']}).";
            header("Location: ../../views/admin/animal_create.php");
            exit();
        }
        
        // --- FIN DE VALIDACIÓN COMPLEJA ---

        // 4. INSERTAR (Si pasó la validación)
        $sql = "INSERT INTO animals (nombre, especie, edad, fecha_llegada, dieta, habitat_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $especie, $edad, $fecha, $dieta, $habitat_id]);

        // 5. MENSAJE DE ÉXITO (Consistencia)
        $_SESSION['success'] = "¡Animal registrado exitosamente en el sistema!";
        header("Location: ../../views/admin/animals.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error técnico: " . $e->getMessage();
        header("Location: ../../views/admin/animal_create.php");
        exit();
    }
}