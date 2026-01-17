<?php
session_start();
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = trim($_POST['nombre']);
    $especie = trim($_POST['especie']);
    $edad = $_POST['edad'];
    $fecha = $_POST['fecha_llegada'];
    $habitat_id = $_POST['habitat_id'];

    try {
        // Usamos la sentencia UPDATE con WHERE id = ?
        $sql = "UPDATE animals SET nombre = ?, especie = ?, edad = ?, fecha_llegada = ?, habitat_id = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $especie, $edad, $fecha, $habitat_id, $id]);

        $_SESSION['success'] = "Animal actualizado correctamente.";
        header("Location: ../../views/admin/animals.php");
        exit();

    } catch (PDOException $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
}