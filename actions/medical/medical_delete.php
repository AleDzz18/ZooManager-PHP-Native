<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// 1. SOLO ADMIN PUEDE BORRAR HISTORIAL
if (!esAdmin()) {
    $_SESSION['error'] = "No tienes permisos para eliminar registros médicos.";
    header("Location: ../../views/admin/animals.php");
    exit();
}

// 2. OBTENER ID
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    try {
        // --- PASO CLAVE: RECUPERAR EL ANIMAL_ID ANTES DE BORRAR ---
        // Necesitamos saber de quién es este historial para redirigir correctamente
        // después de borrarlo.
        $stmt_find = $pdo->prepare("SELECT animal_id FROM medical_records WHERE id = ?");
        $stmt_find->execute([$id]);
        $registro = $stmt_find->fetch();

        // Si no existe el registro, nos vamos a la lista general
        if (!$registro) {
            header("Location: ../../views/admin/animals.php");
            exit();
        }

        $animal_id = $registro['animal_id'];

        // 3. AHORA SÍ, BORRAR EL REGISTRO
        $sql = "DELETE FROM medical_records WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Registro eliminado del historial.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar el registro.";
        }

        // 4. REDIRECCIONAR AL HISTORIAL DE ESE ANIMAL
        header("Location: ../../views/medical/medical_history.php?id=" . $animal_id);
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        // En caso de error, volvemos a la lista general para no romper el flujo
        header("Location: ../../views/admin/animals.php");
    }
} else {
    header("Location: ../../views/admin/animals.php");
    exit();
}
?>