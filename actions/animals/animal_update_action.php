<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = limpiar($_POST['nombre']);
    $especie = limpiar($_POST['especie']);
    $edad = $_POST['edad'];
    $fecha = $_POST['fecha_llegada'];
    $habitat_id = $_POST['habitat_id'];

    try {
        // --- VALIDACIÓN COMPLEJA PARA EDICIÓN ---
        
        // Verificamos si el animal está cambiando de hábitat
        $stmt_current = $pdo->prepare("SELECT habitat_id FROM animals WHERE id = ?");
        $stmt_current->execute([$id]);
        $current_habitat = $stmt_current->fetchColumn();

        // Si el hábitat destino es diferente al actual, verificamos cupo
        if ($current_habitat != $habitat_id) {
            $sql_check = "SELECT capacidad, 
                        (SELECT COUNT(*) FROM animals WHERE habitat_id = h.id) as total 
                        FROM habitats h WHERE id = ?";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([$habitat_id]);
            $res = $stmt_check->fetch();

            if ($res['total'] >= $res['capacidad']) {
                $_SESSION['error'] = "No se puede mover al animal. El hábitat destino está lleno.";
                header("Location: ../../views/admin/animal_edit.php?id=" . $id);
                exit();
            }
        }

        // --- ACTUALIZACIÓN ---
        $sql = "UPDATE animals SET nombre = ?, especie = ?, edad = ?, fecha_llegada = ?, habitat_id = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $especie, $edad, $fecha, $habitat_id, $id]);

        $_SESSION['success'] = "Datos del animal actualizados correctamente.";
        header("Location: ../../views/admin/animals.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
        header("Location: ../../views/admin/animal_edit.php?id=" . $id);
        exit();
    }
}