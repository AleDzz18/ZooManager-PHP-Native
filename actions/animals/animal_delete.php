<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// 1. SEGURIDAD: BLOQUEO DE MÉTODO GET
// Expulsamos a quien intente borrar un registro escribiendo la URL manualmente.
soloMetodoPost(); 

// 2. CONTROL DE ACCESO BASADO EN ROLES (RBAC)
// Verificamos explícitamente si el usuario actual tiene permisos destructivos.
// Si es un Cuidador normal, la función devolverá false y se le denegará el paso.
if (!puedeEliminarRegistros()) {
    $_SESSION['error'] = "No tienes permisos para eliminar animales.";
    header("Location: " . BASE_URL . "views/admin/animals.php");
    exit();
}

// 3. CAPTURA DEL IDENTIFICADOR
// Usamos el operador ternario para verificar si llegó el ID en el formulario.
$id = isset($_POST['id']) ? $_POST['id'] : null;

if ($id) {
    try {
        // 4. BORRADO SEGURO
        // Preparamos la consulta para evitar Inyecciones SQL.
        $sql = "DELETE FROM animals WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        // Ejecutamos pasándole el ID que queremos borrar.
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Animal eliminado correctamente.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar el registro.";
        }

    } catch (PDOException $e) {
        // Usamos nuestro gestor seguro para no revelar nombres de tablas al usuario
        registrarErrorCritico($e, "views/admin/animals.php", "No se pudo completar la eliminación del animal.");
        exit();
    }
} else {
    $_SESSION['error'] = "Error: Identificador no recibido.";
}

// 5. REDIRECCIÓN FINAL
header("Location: " . BASE_URL . "views/admin/animals.php");
exit();
?>