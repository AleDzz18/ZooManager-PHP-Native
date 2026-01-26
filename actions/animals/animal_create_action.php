<?php
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiar($_POST['nombre']);
    $especie = limpiar($_POST['especie']);
    $clima_animal = limpiar($_POST['clima']); // Nuevo campo
    $edad = (int) $_POST['edad'];
    $dieta = limpiar($_POST['dieta']);
    $fecha_llegada = $_POST['fecha_llegada'];
    $habitat_id = $_POST['habitat_id'];

    if (empty($nombre) || empty($especie) || empty($habitat_id) || empty($clima_animal)) {
        $_SESSION['error'] = "Por favor completa todos los campos obligatorios.";
        header("Location: ../../views/admin/animal_create.php");
        exit();
    }

    try {
        // --- INICIO VALIDACIONES COMPLEJAS ---

        // 1. VALIDACIÓN DE FECHA VS EDAD
        // Calculamos la fecha de nacimiento aproximada: (Fecha Actual - Edad)
        $fecha_nacimiento_estimada = date('Y-m-d', strtotime("-$edad years"));
        
        // Si la fecha de llegada es ANTERIOR a cuando nació, es un error lógico
        if ($fecha_llegada < $fecha_nacimiento_estimada) {
            $_SESSION['error'] = "Error de Lógica: El animal tiene $edad años (nació aprox. en $fecha_nacimiento_estimada), no pudo haber llegado al zoológico en $fecha_llegada.";
            header("Location: ../../views/admin/animal_create.php");
            exit();
        }

        // 2. CONSULTAR DATOS DEL HÁBITAT (Capacidad y Clima)
        $sql_check = "SELECT 
                        h.capacidad, 
                        h.clima,
                        (SELECT COUNT(*) FROM animals WHERE habitat_id = h.id) as total_actual 
                        FROM habitats h 
                        WHERE h.id = ?";
        
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$habitat_id]);
        $info_habitat = $stmt_check->fetch();

        if (!$info_habitat) {
            $_SESSION['error'] = "El hábitat seleccionado no existe.";
            header("Location: ../../views/admin/animal_create.php");
            exit();
        }

        // 3. VALIDACIÓN DE CLIMA (Compatibilidad Biológica)
        if ($info_habitat['clima'] !== $clima_animal) {
            $_SESSION['error'] = "Incompatibilidad: El animal requiere clima '$clima_animal' pero el hábitat es '{$info_habitat['clima']}'.";
            header("Location: ../../views/admin/animal_create.php");
            exit();
        }

        // 4. VALIDACIÓN DE CAPACIDAD
        if ($info_habitat['total_actual'] >= $info_habitat['capacidad']) {
            $_SESSION['error'] = "El hábitat seleccionado ya alcanzó su capacidad máxima ({$info_habitat['capacidad']}).";
            header("Location: ../../views/admin/animal_create.php");
            exit();
        }
        
        // --- FIN VALIDACIONES ---

        // 5. INSERTAR
        $sql = "INSERT INTO animals (nombre, especie, clima, edad, fecha_llegada, dieta, habitat_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $especie, $clima_animal, $edad, $fecha_llegada, $dieta, $habitat_id]);

        $_SESSION['success'] = "¡Animal registrado exitosamente bajo normativas biológicas!";
        header("Location: ../../views/admin/animals.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error técnico: " . $e->getMessage();
        header("Location: ../../views/admin/animal_create.php");
        exit();
    }
}