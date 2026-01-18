<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// 1. VERIFICAR PERMISOS DE ADMINISTRADOR
// Solo el admin puede destruir hábitats.
if (!esAdmin()) {
    $_SESSION['error'] = "Acceso denegado. Solo administradores pueden eliminar hábitats.";
    header("Location: ../../views/admin/habitats.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    try {
        // 2. VERIFICAR SI HAY ANIMALES DENTRO (OPCIONAL PERO RECOMENDADO)
        // Aunque la BD tenga "SET NULL", es bueno avisar o limpiar.
        // En este caso, confiamos en la restricción de la BD, pero ejecutamos el delete.

        $sql = "DELETE FROM habitats WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Hábitat eliminado. Los animales asociados (si había) ahora están sin asignar.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar el hábitat.";
        }

    } catch (PDOException $e) {
        // Captura si hay un error de llave foránea u otro problema técnico
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    }
}

// 3. VOLVER A LA LISTA
header("Location: ../../views/admin/habitats.php");
exit();
?>