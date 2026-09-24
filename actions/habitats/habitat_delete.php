<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// PASO 1: SEGURIDAD DE MÉTODO
soloMetodoPost(); 

// PASO 2: CONTROL DE ACCESO BASADO EN ROLES (RBAC)
// Verificamos si el usuario tiene el privilegio específico para administrar la infraestructura del zoológico.
if (!puedeGestionarHabitats()) {
    $_SESSION['error'] = "Acceso denegado. No tienes permisos para eliminar hábitats.";
    header("Location: " . BASE_URL . "views/admin/habitats.php");
    exit();
}

// PASO 3: EXTRACCIÓN DEL IDENTIFICADOR
$id = isset($_POST['id']) ? $_POST['id'] : null;

if ($id) {
    try {
        // PASO 4: BORRADO SEGURO
        // Al borrar el hábitat, cualquier animal que tuviera este 'habitat_id' quedará huérfano 
        // (suponiendo que la base de datos tiene configurado ON DELETE SET NULL en su llave foránea).
        $sql = "DELETE FROM habitats WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Hábitat eliminado. Los animales asociados (si había) ahora están sin asignar.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar el hábitat.";
        }

    } catch (PDOException $e) {
        // Enmascaramos el error técnico y lo registramos en el servidor
        registrarErrorCritico($e, "views/admin/habitats.php", "No se pudo completar la eliminación del hábitat.");
        exit();
    }
} else {
    $_SESSION['error'] = "Error: ID no recibido.";
}

// PASO 5: REDIRECCIÓN
header("Location: " . BASE_URL . "views/admin/habitats.php");
exit();
?>