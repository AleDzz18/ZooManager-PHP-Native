<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// 1. SEGURIDAD: SOLO POST
soloMetodoPost(); 

// 2. VERIFICAR PERMISOS (Solo Admin)
if (!esAdmin()) {
    $_SESSION['error'] = "Acceso denegado. Solo administradores pueden eliminar hábitats.";
    header("Location: ../../views/admin/habitats.php");
    exit();
}

// 3. CAPTURAR ID DESDE POST
$id = isset($_POST['id']) ? $_POST['id'] : null;

if ($id) {
    try {
        // Ejecutamos el borrado
        $sql = "DELETE FROM habitats WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Hábitat eliminado. Los animales asociados (si había) ahora están sin asignar.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar el hábitat.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Error: ID no recibido.";
}

header("Location: ../../views/admin/habitats.php");
exit();
?>