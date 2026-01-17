<?php
session_start();
require_once '../../config/db.php';

// 1. VERIFICAR MÉTODO POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. CAPTURAR DATOS
    $nombre = trim($_POST['nombre']);
    $especie = trim($_POST['especie']);
    $edad = $_POST['edad'];
    $fecha = $_POST['fecha_llegada'];
    $habitat_id = $_POST['habitat_id'];

    // 3. VALIDACIÓN BÁSICA
    if (empty($nombre) || empty($especie) || empty($habitat_id)) {
        $_SESSION['error'] = "Por favor completa todos los campos obligatorios.";
        header("Location: ../../views/admin/animal_create.php");
        exit();
    }

    try {
        // 4. INSERTAR EN BASE DE DATOS
        // Fíjate que usamos "?" para evitar inyección SQL.
        $sql = "INSERT INTO animals (nombre, especie, edad, fecha_llegada, habitat_id) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $especie, $edad, $fecha, $habitat_id]);

        // 5. REDIRECCIONAR CON ÉXITO
        // No creamos mensaje de éxito en sesión todavía para no complicar, 
        // pero volvemos a la lista para ver el nuevo animal.
        header("Location: ../../views/admin/animals.php");
        exit();

    } catch (PDOException $e) {
        // Manejo de error técnico
        $_SESSION['error'] = "Error al guardar: " . $e->getMessage();
        header("Location: ../../views/admin/animal_create.php");
        exit();
    }

} else {
    // Si intentan entrar directo por URL
    header("Location: ../../views/admin/animals.php");
    exit();
}