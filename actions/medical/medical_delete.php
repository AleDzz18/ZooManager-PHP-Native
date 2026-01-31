<?php
// actions/medical/medical_delete.php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// 1. SEGURIDAD: Solo permitir peticiones POST
soloMetodoPost(); 

// 2. VERIFICAR PERMISOS (Solo Admin)
if (!esAdmin()) {
    $_SESSION['error'] = "No tienes permisos para eliminar registros médicos.";
    header("Location: ../../views/admin/animals.php");
    exit();
}

// 3. CAPTURAR EL ID
// Usamos $_POST['id'] directamente. Si no existe, probamos $_REQUEST por seguridad.
$id = $_POST['id'] ?? null;

if ($id) {
    try {
        // A) BUSCAR EL ANIMAL_ID ANTES DE BORRAR
        // Necesitamos saber a qué animal pertenecía este registro para volver a su historial
        $stmt_find = $pdo->prepare("SELECT animal_id FROM medical_records WHERE id = ?");
        $stmt_find->execute([$id]);
        $registro = $stmt_find->fetch(PDO::FETCH_ASSOC);

        if (!$registro) {
            $_SESSION['error'] = "El registro médico no existe o ya fue eliminado.";
            header("Location: ../../views/admin/animals.php");
            exit();
        }

        $animal_id = $registro['animal_id'];

        // B) EJECUTAR EL BORRADO
        $sql = "DELETE FROM medical_records WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Registro eliminado correctamente.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar el registro de la base de datos.";
        }

        // C) REDIRECCIONAR AL HISTORIAL DEL ANIMAL
        header("Location: ../../views/medical/medical_history.php?id=" . $animal_id);
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error técnico: " . $e->getMessage();
        header("Location: ../../views/admin/animals.php");
        exit();
    }
} else {
    // Si caemos aquí, es porque $_POST['id'] llegó vacío
    $_SESSION['error'] = "Error: ID no recibido. Verifica el formulario.";
    header("Location: ../../views/admin/animals.php");
    exit();
}
?>