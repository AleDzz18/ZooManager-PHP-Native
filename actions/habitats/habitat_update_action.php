<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/functions.php';

soloMetodoPost();

// 1. SOLO PROCESAR SI ES POST
// $_SERVER['REQUEST_METHOD'] ya está verificado en soloMetodoPost()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. CAPTURAR DATOS
    $id = $_POST['id'];
    $nombre = limpiar($_POST['nombre']);
    $clima = limpiar($_POST['clima']);
    $climas_permitidos = obtenerClimasValidos();
    $capacidad_nueva = (int) $_POST['capacidad'];
    $descripcion = limpiar($_POST['descripcion']);

    if (!in_array($clima, $climas_permitidos)) {
        $_SESSION['error'] = "Error de seguridad: El clima '$clima' no es válido.";
        header("Location: ../../views/admin/habitat_edit.php?id=" . $id);
        exit();
    }

    if (strlen($nombre) > 50) {
        $_SESSION['error'] = "El nombre del hábitat es muy largo (Máx 50 caracteres).";
        header("Location: ../../views/admin/habitat_create.php");
        exit();
    }
    // 2. VALIDAR QUE NO ESTÉN VACÍOS
    if (empty($nombre) || empty($clima) || $capacidad_nueva < 1) {
        $_SESSION['error'] = "Datos inválidos. La capacidad debe ser mayor a 0.";
        header("Location: ../../views/admin/habitat_edit.php?id=" . $id);
        exit();
    }

    try {
        // 3. VALIDACIÓN LÓGICA DE NEGOCIO (INTEGRIDAD)
        // No podemos reducir la capacidad a un número menor a la cantidad de animales que ya viven ahí.
        
        // A) Contamos cuántos animales hay actualmente en este hábitat
        $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM animals WHERE habitat_id = ?");
        $stmt_count->execute([$id]);
        $ocupacion_actual = $stmt_count->fetchColumn();

        // B) Si intentas bajar la capacidad a 5, pero hay 8 animales, damos error.
        if ($capacidad_nueva < $ocupacion_actual) {
            $_SESSION['error'] = "No puedes reducir la capacidad a $capacidad_nueva. Actualmente hay $ocupacion_actual animales asignados.";
            header("Location: ../../views/admin/habitat_edit.php?id=" . $id);
            exit();
        }

        // 4. PREPARAR LA ACTUALIZACIÓN
        $sql = "UPDATE habitats SET nombre = ?, clima = ?, capacidad = ?, descripcion = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        // 5. EJECUTAR
        if ($stmt->execute([$nombre, $clima, $capacidad_nueva, $descripcion, $id])) {
            $_SESSION['success'] = "Hábitat actualizado correctamente.";
            header("Location: ../../views/admin/habitats.php");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
        header("Location: ../../views/admin/habitat_edit.php?id=" . $id);
        exit();
    }
}