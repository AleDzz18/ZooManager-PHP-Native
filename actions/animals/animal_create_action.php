<?php
// INICIO DE SESIÓN Y ARCHIVOS BASE
// Necesitamos session_start() para usar $_SESSION y mostrar alertas al usuario.
session_start();
require_once '../../config/db.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/functions.php';

// PASO 1: SEGURIDAD CONTRA ACCESO DIRECTO
// Evita que un usuario acceda a este archivo escribiendo la URL directamente en el navegador (GET).
soloMetodoPost();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // PASO 2: LIMPIEZA DE DATOS (SANITIZACIÓN)
    // Usamos la función limpiar() para evitar ataques XSS (Cross-Site Scripting) 
    // convirtiendo caracteres especiales en texto inofensivo.
    $nombre = limpiar($_POST['nombre']);
    $especie = limpiar($_POST['especie']);
    $clima_animal = limpiar($_POST['clima']);
    $clima_permitidos = obtenerClimasValidos();
    $edad = (int) $_POST['edad']; // Forzamos a que sea un número entero
    $dieta = limpiar($_POST['dieta']);
    $fecha_llegada = $_POST['fecha_llegada'];
    $habitat_id = $_POST['habitat_id'];

    // PASO 3: VALIDACIONES DE SEGURIDAD BÁSICAS
    // Verificamos que no inyecten fechas falsas o climas modificados desde el inspector de elementos.
    if (!esFechaValida($fecha_llegada)) {
        $_SESSION['error'] = "Error de seguridad: La fecha proporcionada no es válida o es futura.";
        header("Location: " . BASE_URL . "views/admin/animal_create.php");
        exit();
    }

    if (!in_array($clima_animal, $clima_permitidos)) {
        $_SESSION['error'] = "Error de seguridad: El clima '$clima_animal' no es válido.";
        header("Location: " . BASE_URL . "views/admin/animal_create.php");
        exit();
    }

    if (empty($nombre) || empty($especie) || empty($habitat_id) || empty($clima_animal)) {
        $_SESSION['error'] = "Por favor completa todos los campos obligatorios.";
        header("Location: " . BASE_URL . "views/admin/animal_create.php");
        exit();
    }

    try {
        // --- INICIO VALIDACIONES LÓGICAS DEL ZOOLÓGICO ---
        
        // A) VALIDACIÓN DE FECHA VS EDAD
        // Si el animal tiene 5 años hoy, restamos 5 años a la fecha actual para saber cuándo nació aprox.
        $fecha_nacimiento_estimada = date('Y-m-d', strtotime("-$edad years"));
        
        // Un animal no puede haber llegado al zoo antes de nacer.
        if ($fecha_llegada < $fecha_nacimiento_estimada) {
            $_SESSION['error'] = "Error de Lógica: El animal tiene $edad años, no pudo llegar en $fecha_llegada.";
            header("Location: " . BASE_URL . "views/admin/animal_create.php");
            exit();
        }

        // B) CONCEPTO AVANZADO: TRANSACCIONES Y BLOQUEOS (RACE CONDITIONS)
        // Iniciamos una transacción. Esto significa que los cambios no se guardan permanentemente
        // hasta que estemos 100% seguros de que todo es correcto (commit). Si algo falla, revertimos (rollback).
        $pdo->beginTransaction();

        // C) CONSULTAR DATOS DEL HÁBITAT
        // FOR UPDATE es crucial aquí: Bloquea esta fila específica en la base de datos temporalmente.
        // Si otro cuidador intenta guardar un animal en este mismo hábitat exactamente al mismo
        // milisegundo, la base de datos lo pondrá en espera hasta que nosotros terminemos. 
        // Esto evita que se sobrepase la capacidad máxima por accidente.
        $sql_check = "SELECT 
                        h.capacidad, 
                        h.clima,
                        (SELECT COUNT(*) FROM animals WHERE habitat_id = h.id) as total_actual 
                        FROM habitats h 
                        WHERE h.id = ? FOR UPDATE";
        
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$habitat_id]);
        $info_habitat = $stmt_check->fetch();

        if (!$info_habitat) {
            $pdo->rollBack(); // Deshacemos la transacción porque hubo un error
            $_SESSION['error'] = "El hábitat seleccionado no existe.";
            header("Location: " . BASE_URL . "views/admin/animal_create.php");
            exit();
        }

        // D) VALIDACIÓN DE COMPATIBILIDAD BIOLÓGICA
        if ($info_habitat['clima'] !== $clima_animal) {
            $pdo->rollBack();
            $_SESSION['error'] = "Incompatibilidad: El animal requiere clima '$clima_animal' pero el hábitat es '{$info_habitat['clima']}'.";
            header("Location: " . BASE_URL . "views/admin/animal_create.php");
            exit();
        }

        // E) VALIDACIÓN DE CAPACIDAD MÁXIMA
        if ($info_habitat['total_actual'] >= $info_habitat['capacidad']) {
            $pdo->rollBack();
            $_SESSION['error'] = "El hábitat seleccionado ya alcanzó su capacidad máxima ({$info_habitat['capacidad']}).";
            header("Location: " . BASE_URL . "views/admin/animal_create.php");
            exit();
        }

        // --- FIN VALIDACIONES ---

        // PASO 4: INSERCIÓN SEGURA (PREPARED STATEMENTS)
        // Usamos los signos de interrogación (?) para evitar Inyección SQL.
        $sql = "INSERT INTO animals (nombre, especie, clima, edad, fecha_llegada, dieta, habitat_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $especie, $clima_animal, $edad, $fecha_llegada, $dieta, $habitat_id]);

        // PASO 5: CONFIRMAR TRANSACCIÓN
        // Todo salió perfecto, guardamos los cambios definitivamente y liberamos el bloqueo del hábitat.
        $pdo->commit();

        $_SESSION['success'] = "¡Animal registrado exitosamente bajo normativas biológicas!";
        header("Location: " . BASE_URL . "views/admin/animals.php");
        exit();

    } catch (PDOException $e) {
        // MANEJO DE ERRORES CRÍTICOS
        // Si hubo un fallo en la base de datos (ej. se cayó el servidor en medio de la operación),
        // revertimos cualquier cambio a medias.
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Registramos el error en silencio (para el log del servidor) y mandamos un mensaje amigable al usuario.
        registrarErrorCritico($e, "views/admin/animal_create.php", "Error al registrar el animal. Intente nuevamente.");
    }
}