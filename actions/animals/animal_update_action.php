<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

soloMetodoPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = limpiar($_POST['nombre']);
    $especie = limpiar($_POST['especie']);
    $clima_animal = limpiar($_POST['clima']); // Nuevo campo
    $clima_permitidos = obtenerClimasValidos();
    $edad = (int) $_POST['edad'];
    $dieta = limpiar($_POST['dieta']);
    $fecha_llegada = $_POST['fecha_llegada'];
    $habitat_id = $_POST['habitat_id'];

    if (!esFechaValida($fecha_llegada)) {
        $_SESSION['error'] = "Error de seguridad: La fecha proporcionada no es válida o es futura.";
        header("Location: ../../views/admin/animal_create.php");
        exit();
    }

    if (!in_array($clima_animal, $clima_permitidos)) {
        $_SESSION['error'] = "Error de seguridad: El clima '$clima_animal' no es válido.";
        // Opcional: Podrías loguear esto como un intento de hackeo
        header("Location: ../../views/admin/animal_edit.php?id=" . $id);
        exit();
    }

    try {
        // --- INICIO VALIDACIONES COMPLEJAS ---

        // 1. VALIDACIÓN DE FECHA VS EDAD
        $fecha_nacimiento_estimada = date('Y-m-d', strtotime("-$edad years"));
        if ($fecha_llegada < $fecha_nacimiento_estimada) {
            $_SESSION['error'] = "Error: La fecha de llegada ($fecha_llegada) es anterior al nacimiento estimado ($fecha_nacimiento_estimada).";
            header("Location: ../../views/admin/animal_edit.php?id=" . $id);
            exit();
        }

        // 2. VALIDAR CLIMA Y CAPACIDAD (Si cambió de hábitat o de requerimiento climático)
        
        // Obtenemos info del hábitat destino
        $stmt_habitat = $pdo->prepare("SELECT clima, capacidad, (SELECT COUNT(*) FROM animals WHERE habitat_id = h.id) as total FROM habitats h WHERE id = ?");
        $stmt_habitat->execute([$habitat_id]);
        $info_habitat = $stmt_habitat->fetch();

        // A) Validar Clima
        if ($info_habitat['clima'] !== $clima_animal) {
            $_SESSION['error'] = "Error: No puedes mover este animal a un hábitat '{$info_habitat['clima']}' porque requiere clima '$clima_animal'.";
            header("Location: ../../views/admin/animal_edit.php?id=" . $id);
            exit();
        }

        // B) Validar Capacidad (Solo si cambiamos de hábitat)
        // Consultamos el habitat actual del animal antes de guardar
        $stmt_current = $pdo->prepare("SELECT habitat_id FROM animals WHERE id = ?");
        $stmt_current->execute([$id]);
        $current_habitat_id = $stmt_current->fetchColumn();

        if ($current_habitat_id != $habitat_id) {
            if ($info_habitat['total'] >= $info_habitat['capacidad']) {
                $_SESSION['error'] = "El hábitat destino está lleno. Capacidad máxima: " . $info_habitat['capacidad'];
                header("Location: ../../views/admin/animal_edit.php?id=" . $id);
                exit();
            }
        }

        // --- ACTUALIZACIÓN ---
        $sql = "UPDATE animals SET nombre = ?, especie = ?, clima = ?, edad = ?, fecha_llegada = ?, dieta = ?, habitat_id = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $especie, $clima_animal, $edad, $fecha_llegada, $dieta, $habitat_id, $id]);

        $_SESSION['success'] = "Datos y validaciones biológicas actualizadas correctamente.";
        header("Location: ../../views/admin/animals.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
        header("Location: ../../views/admin/animal_edit.php?id=" . $id);
        exit();
    }
} else {
    header("Location: ../../views/admin/animals.php");
    exit();
}