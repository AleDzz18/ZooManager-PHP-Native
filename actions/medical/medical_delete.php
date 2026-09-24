<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// PASO 1: SEGURIDAD DE RUTA
soloMetodoPost(); 

// PASO 2: VERIFICACIÓN DE PERMISOS FLEXIBLE (RBAC)
// Usamos nuestra función personalizada que permite tanto a Administradores como a Cuidadores
// eliminar un registro médico en caso de que se hayan equivocado al redactarlo.
if (!puedeEliminarHistorialMedico()) {
    $_SESSION['error'] = "No tienes permisos para eliminar registros médicos.";
    header("Location: " . BASE_URL . "views/admin/animals.php");
    exit();
}

// Obtenemos el ID del registro médico a borrar.
$id = $_POST['id'] ?? null;

if ($id) {
    try {
        // PASO 3: RECUPERAR CONTEXTO ANTES DE BORRAR
        // Antes de eliminar el registro, necesitamos saber a qué animal pertenecía.
        // Así, cuando termine de borrarse, podemos redirigir al usuario al historial de ese mismo animal.
        $stmt_find = $pdo->prepare("SELECT animal_id FROM medical_records WHERE id = ?");
        $stmt_find->execute([$id]);
        $registro = $stmt_find->fetch(PDO::FETCH_ASSOC);

        // Si el registro no existe, abortamos.
        if (!$registro) {
            $_SESSION['error'] = "El registro médico no existe o ya fue eliminado.";
            header("Location: " . BASE_URL . "views/admin/animals.php");
            exit();
        }

        // Guardamos el ID del animal para la redirección final.
        $animal_id = $registro['animal_id'];

        // PASO 4: ELIMINACIÓN SEGURA
        $sql = "DELETE FROM medical_records WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Registro eliminado correctamente.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar el registro de la base de datos.";
        }

        // PASO 5: REDIRECCIÓN INTELIGENTE
        // Volvemos al historial del paciente específico en lugar de un listado general.
        header("Location: " . BASE_URL . "views/medical/medical_history.php?id=" . $animal_id);
        exit();

    } catch (PDOException $e) {
        // Control de excepciones silencioso
        registrarErrorCritico($e, "views/admin/animals.php", "Error técnico al intentar eliminar el registro médico.");
    }
} else {
    // Manejo de error si el formulario fue manipulado y no envió el ID
    $_SESSION['error'] = "Error: ID no recibido. Verifica el formulario.";
    header("Location: " . BASE_URL . "views/admin/animals.php");
    exit();
}