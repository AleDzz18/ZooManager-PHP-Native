<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// PASO 1: SEGURIDAD DE ACCESO
soloMetodoPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // PASO 2: CAPTURA Y LIMPIEZA DE DATOS MODIFICADOS
    $id = $_POST['id']; // ID del animal que estamos editando
    $nombre = limpiar($_POST['nombre']);
    $especie = limpiar($_POST['especie']);
    $clima_animal = limpiar($_POST['clima']);
    $clima_permitidos = obtenerClimasValidos();
    $edad = (int) $_POST['edad'];
    $dieta = limpiar($_POST['dieta']);
    $fecha_llegada = $_POST['fecha_llegada'];
    $habitat_id = $_POST['habitat_id']; // Puede ser el mismo u otro nuevo

    // PASO 3: PROTECCIÓN CONTRA MANIPULACIÓN DEL DOM (F12)
    if (!esFechaValida($fecha_llegada)) {
        $_SESSION['error'] = "Error de seguridad: La fecha proporcionada no es válida o es futura.";
        header("Location: " . BASE_URL . "views/admin/animal_edit.php?id=" . $id);
        exit();
    }

    if (!in_array($clima_animal, $clima_permitidos)) {
        $_SESSION['error'] = "Error de seguridad: El clima '$clima_animal' no es válido.";
        header("Location: " . BASE_URL . "views/admin/animal_edit.php?id=" . $id);
        exit();
    }

    try {
        // --- INICIO LÓGICA BIOLÓGICA ---
        
        // A) Comprobación de Viaje en el Tiempo (Fecha vs Edad)
        $fecha_nacimiento_estimada = date('Y-m-d', strtotime("-$edad years"));
        if ($fecha_llegada < $fecha_nacimiento_estimada) {
            $_SESSION['error'] = "Error: La fecha de llegada ($fecha_llegada) es anterior al nacimiento estimado ($fecha_nacimiento_estimada).";
            header("Location: " . BASE_URL . "views/admin/animal_edit.php?id=" . $id);
            exit();
        }

        // B) INICIO DE LA TRANSACCIÓN PROTEGIDA
        $pdo->beginTransaction();

        // C) EVALUACIÓN DEL HÁBITAT DESTINO
        // Buscamos el hábitat al que el usuario quiere asignar este animal y lo bloqueamos temporalmente.
        $stmt_habitat = $pdo->prepare("SELECT clima, capacidad, (SELECT COUNT(*) FROM animals WHERE habitat_id = h.id) as total FROM habitats h WHERE id = ? FOR UPDATE");
        $stmt_habitat->execute([$habitat_id]);
        $info_habitat = $stmt_habitat->fetch();

        // 1. ¿El nuevo clima o hábitat coinciden?
        if ($info_habitat['clima'] !== $clima_animal) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error: No puedes mover este animal a un hábitat '{$info_habitat['clima']}' porque requiere clima '$clima_animal'.";
            header("Location: " . BASE_URL . "views/admin/animal_edit.php?id=" . $id);
            exit();
        }

        // 2. ¿Lo estamos moviendo de casa?
        // Consultamos en qué hábitat estaba guardado el animal ANTES de esta actualización.
        $stmt_current = $pdo->prepare("SELECT habitat_id FROM animals WHERE id = ?");
        $stmt_current->execute([$id]);
        $current_habitat_id = $stmt_current->fetchColumn();

        // Si el hábitat cambió, necesitamos verificar si el destino tiene espacio.
        // Si NO cambió de hábitat (solo le editamos el nombre o la dieta), ignoramos la capacidad
        // porque el animal ya forma parte de la cuenta actual de ese hábitat.
        if ($current_habitat_id != $habitat_id) {
            if ($info_habitat['total'] >= $info_habitat['capacidad']) {
                $pdo->rollBack(); // Deshacemos el bloqueo y abortamos
                $_SESSION['error'] = "El hábitat destino está lleno. Capacidad máxima: " . $info_habitat['capacidad'];
                header("Location: " . BASE_URL . "views/admin/animal_edit.php?id=" . $id);
                exit();
            }
        }

        // PASO 4: EJECUCIÓN DE LA ACTUALIZACIÓN
        $sql = "UPDATE animals SET nombre = ?, especie = ?, clima = ?, edad = ?, fecha_llegada = ?, dieta = ?, habitat_id = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $especie, $clima_animal, $edad, $fecha_llegada, $dieta, $habitat_id, $id]);

        // PASO 5: CONFIRMACIÓN EXITOSA
        $pdo->commit();

        $_SESSION['success'] = "Datos y validaciones biológicas actualizadas correctamente.";
        header("Location: " . BASE_URL . "views/admin/animals.php");
        exit();

    } catch (PDOException $e) {
        // En caso de desastre (caída de BD, error de sintaxis), cerramos la transacción
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        registrarErrorCritico($e, "views/admin/animal_edit.php?id=" . $id, "Error al actualizar la información del animal.");
    }
} else {
    // Si entró de alguna forma por GET y esquivó soloMetodoPost()
    header("Location: " . BASE_URL . "views/admin/animals.php");
    exit();
}