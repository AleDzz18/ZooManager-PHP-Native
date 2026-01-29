<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// 1. SEGURIDAD: RECHAZAR ACCESO POR URL (GET)
// Si alguien intenta escribir la dirección en el navegador, lo expulsamos.
soloMetodoPost(); 

// 2. VERIFICAR PERMISOS (Solo Admin)
if (!esAdmin()) {
    $_SESSION['error'] = "No tienes permisos para eliminar animales.";
    header("Location: ../../views/admin/animals.php");
    exit();
}

// 3. CAPTURAR EL ID DESDE EL FORMULARIO (POST)
// Ya no usamos $_GET['id']
$id = isset($_POST['id']) ? $_POST['id'] : null;

if ($id) {
    try {
        // Borrado seguro
        $sql = "DELETE FROM animals WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = "Animal eliminado correctamente.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar el registro.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Error: Identificador no recibido.";
}

// 4. VOLVER A LA LISTA
header("Location: ../../views/admin/animals.php");
exit();
?>