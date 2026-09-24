<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/functions.php';

// PASO 1: PROTECCIÓN INICIAL
soloMetodoPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // PASO 2: RECOLECCIÓN Y LIMPIEZA
    $id = $_POST['id']; // ID del hábitat a modificar
    $nombre = limpiar($_POST['nombre']);
    $clima = limpiar($_POST['clima']);
    $climas_permitidos = obtenerClimasValidos();
    $capacidad_nueva = (int) $_POST['capacidad'];
    $descripcion = limpiar($_POST['descripcion']);

    // PASO 3: VALIDACIONES DE SEGURIDAD Y FORMATO
    if (!in_array($clima, $climas_permitidos)) {
        $_SESSION['error'] = "Error de seguridad: El clima '$clima' no es válido.";
        header("Location: " . BASE_URL . "views/admin/habitat_edit.php?id=" . $id);
        exit();
    }

    if (strlen($nombre) > 50) {
        $_SESSION['error'] = "El nombre del hábitat es muy largo (Máx 50 caracteres).";
        header("Location: " . BASE_URL . "views/admin/habitat_edit.php?id=" . $id);
        exit();
    }
    
    if (empty($nombre) || empty($clima) || $capacidad_nueva < 1) {
        $_SESSION['error'] = "Datos inválidos. La capacidad debe ser mayor a 0.";
        header("Location: " . BASE_URL . "views/admin/habitat_edit.php?id=" . $id);
        exit();
    }

    try {
        // PASO 4: CONTROL DE CONCURRENCIA (TRANSACCIONES)
        // Iniciamos una transacción. Esto nos permite hacer lecturas bloqueantes y garantizar 
        // la integridad de los datos (ACID) si varios usuarios operan al mismo tiempo.
        $pdo->beginTransaction();

        // PASO 5: VALIDACIÓN DE LÓGICA DE NEGOCIO (CAPACIDAD VS OCUPACIÓN)
        // REGLA CRÍTICA: No podemos achicar un hábitat si dejaría animales sin espacio.
        // Usamos 'FOR UPDATE' para bloquear la lectura: si alguien está agregando un animal 
        // a este hábitat en este exacto instante, esta consulta esperará a que termine para
        // contar correctamente cuántos animales hay en realidad.
        $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM animals WHERE habitat_id = ? FOR UPDATE");
        $stmt_count->execute([$id]);
        $ocupacion_actual = $stmt_count->fetchColumn();

        // Si la nueva capacidad es menor a la cantidad de animales que ya viven ahí...
        if ($capacidad_nueva < $ocupacion_actual) {
            $pdo->rollBack(); // Deshacemos la transacción y liberamos el bloqueo
            $_SESSION['error'] = "No puedes reducir la capacidad a $capacidad_nueva. Actualmente hay $ocupacion_actual animales asignados.";
            header("Location: " . BASE_URL . "views/admin/habitat_edit.php?id=" . $id);
            exit();
        }

        // PASO 6: ACTUALIZACIÓN EN BASE DE DATOS
        $sql = "UPDATE habitats SET nombre = ?, clima = ?, capacidad = ?, descripcion = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        // Ejecutamos los cambios
        if ($stmt->execute([$nombre, $clima, $capacidad_nueva, $descripcion, $id])) {
            // PASO 7: CONFIRMACIÓN DEFINITIVA
            // Si la consulta fue exitosa, aplicamos los cambios a la base de datos de forma permanente.
            $pdo->commit();
            $_SESSION['success'] = "Hábitat actualizado correctamente.";
            header("Location: " . BASE_URL . "views/admin/habitats.php");
            exit();
        } else {
            // En caso de que el execute falle por alguna restricción interna
            $pdo->rollBack();
        }

    } catch (PDOException $e) {
        // PASO 8: MANEJO DE EXCEPCIONES EN TRANSACCIONES
        // Si ocurre un error catastrófico, primero verificamos si hay una transacción activa
        // para revertirla y no dejar bloqueos colgados en el servidor.
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        registrarErrorCritico($e, "views/admin/habitat_edit.php?id=" . $id, "Error al intentar actualizar el hábitat.");
    }
}