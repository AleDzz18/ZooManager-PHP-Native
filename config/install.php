<?php
/*
    ---------------------------------------------------
    SCRIPT DE INSTALACIÓN Y MIGRACIÓN (install.php)
    ---------------------------------------------------
    Ejecuta este archivo para:
    1. Crear la BD si no existe.
    2. Crear tablas faltantes.
    3. Agregar columnas nuevas sin borrar datos (Migraciones).
*/

// 1. CONFIGURACIÓN
$host = 'localhost';
$port = '3306';
$username = 'root';
$password = '';
$dbname = 'zoo_system';

// FUNCIÓN AUXILIAR PARA AGREGAR COLUMNAS SI FALTAN
function agregarColumnaSiNoExiste($pdo, $tabla, $columna, $definicion) {
    try {
        $check = $pdo->query("SHOW COLUMNS FROM `$tabla` LIKE '$columna'");
        if ($check->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `$tabla` ADD COLUMN $columna $definicion");
            echo "<p style='color:blue'>🛠️ Columna <strong>$columna</strong> agregada a la tabla '$tabla'.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error verificando columna $columna: " . $e->getMessage() . "</p>";
    }
}

try {
    // 2. CONEXIÓN AL SERVIDOR (Sin DB)
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>🦁 ZooManager Updater & Installer</h1><hr>";

    // 3. CREAR DB SI NO EXISTE
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "<p style='color:green'>✅ Base de datos verificada.</p>";
    
    // Conectarse a la DB específica
    $pdo->query("USE `$dbname`");

    // 4. CREACIÓN DE TABLAS (Estructura Base)
    
    // --- TABLA USERS ---
    $sql_users = "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nombre_completo` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL UNIQUE,
        `password` varchar(255) NOT NULL,
        `rol` enum('admin','cuidador') NOT NULL DEFAULT 'cuidador',
        `ultimo_acceso` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_users);
    
    // --- TABLA HABITATS ---
    $sql_habitats = "CREATE TABLE IF NOT EXISTS `habitats` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nombre` varchar(50) NOT NULL,
        `clima` varchar(50) NOT NULL,
        `capacidad` int(11) NOT NULL,
        `descripcion` text DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_habitats);

    // --- TABLA ANIMALS ---
    $sql_animals = "CREATE TABLE IF NOT EXISTS `animals` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nombre` varchar(50) NOT NULL,
        `especie` varchar(50) NOT NULL,
        `clima` varchar(50) NOT NULL,
        `edad` int(11) NOT NULL,
        `fecha_llegada` date NOT NULL,
        `dieta` varchar(50) DEFAULT NULL,
        `habitat_id` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`habitat_id`) REFERENCES `habitats` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_animals);

    // --- TABLA MEDICAL_RECORDS (LA QUE FALTABA) ---
    // Importante: ON DELETE CASCADE borra el historial si se borra el animal
    $sql_medical = "CREATE TABLE IF NOT EXISTS `medical_records` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `animal_id` int(11) NOT NULL,
        `fecha` date NOT NULL,
        `descripcion` text NOT NULL,
        `diagnostico` text DEFAULT NULL,
        `tratamiento` text DEFAULT NULL,
        `severidad` enum('Baja','Media','Alta') NOT NULL DEFAULT 'Baja',
        PRIMARY KEY (`id`),
        FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_medical);

    echo "<p style='color:green'>✅ Tablas verificadas (Users, Habitats, Animals, Medical Records).</p>";

    // 5. MIGRACIONES (Agrega aquí campos nuevos en el futuro)
    // Ejemplo: Si mañana quieres agregar fotos a los animales, descomenta esto:
    // agregarColumnaSiNoExiste($pdo, 'animals', 'foto_url', 'VARCHAR(255) DEFAULT NULL');

    // 6. USUARIO ADMIN POR DEFECTO
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'usuario1@gmail.com'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $pass = password_hash('12345678', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (nombre_completo, email, password, rol, ultimo_acceso) 
                    VALUES ('Admin Inicial', 'usuario1@gmail.com', '$pass', 'admin', NOW())");
        echo "<p style='color:blue'>ℹ️ Admin creado (usuario1@gmail.com / 12345678).</p>";
    }

} catch (PDOException $e) {
    die("Error Crítico: " . $e->getMessage());
}
?>