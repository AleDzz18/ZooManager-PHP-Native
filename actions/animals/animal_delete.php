<?php
/*
    ---------------------------------------------------
    LÓGICA DE ELIMINACIÓN (DELETE)
    ---------------------------------------------------
*/
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php'; // Solo usuarios logueados borran

// 1. OBTENER EL ID POR URL
// El ID viene de: animal_delete.php?id=5
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    try {
        // 2. PREPARAR LA ELIMINACIÓN
        $sql = "DELETE FROM animals WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        // 3. EJECUTAR
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Animal eliminado correctamente.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar el registro.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    }
}

// 4. REDIRECCIONAR DE VUELTA A LA LISTA
header("Location: ../../views/admin/animals.php");
exit();
?>