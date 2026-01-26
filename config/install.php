<?php
/*
    ---------------------------------------------------
    SCRIPT DE INSTALACIÓN AUTOMÁTICA (DB INSTALLER)
    ---------------------------------------------------
    Ejecuta este archivo una única vez para crear la
    base de datos y las tablas necesarias.
    
    Abre el navegador y ejecutar: http://localhost/zoo-system/config/install.php
*/

// CONFIGURACIÓN DE CONEXIÓN
$host = 'localhost';
$port = '3306';     // Cambia esto si usas otro puerto (ej. 3307)
$username = 'root'; // Usuario por defecto de XAMPP
$password = '';     // Password por defecto (vacío en XAMPP)
$dbname = 'zoo_system';

try {
    // 1. CONEXIÓN AL SERVIDOR (Sin seleccionar DB aún)
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>🦁 ZooManager Installer</h1>";
    echo "<hr>";

    // 2. CREAR BASE DE DATOS
    echo "<p>⏳ Creando base de datos '<strong>$dbname</strong>'...</p>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "<p style='color:green'>✅ Base de datos creada o verificada.</p>";

    // 3. SELECCIONAR LA BASE DE DATOS
    $pdo->exec("USE `$dbname`");

    // 4. CREACIÓN DE TABLAS

    // --- Tabla: Users ---
    $sql_users = "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nombre_completo` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `rol` enum('admin','cuidador') DEFAULT 'cuidador',
        `ultimo_acceso` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    $pdo->exec($sql_users);
    echo "<p style='color:green'>✅ Tabla 'users' creada.</p>";

    // --- Tabla: Habitats ---
    $sql_habitats = "CREATE TABLE IF NOT EXISTS `habitats` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nombre` varchar(50) NOT NULL,
        `clima` varchar(30) NOT NULL,
        `capacidad` int(11) NOT NULL,
        `descripcion` text DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `nombre` (`nombre`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    $pdo->exec($sql_habitats);
    echo "<p style='color:green'>✅ Tabla 'habitats' creada.</p>";

    // --- Tabla: Animals ---
    $sql_animals = "CREATE TABLE IF NOT EXISTS `animals` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nombre` varchar(50) NOT NULL,
        `especie` varchar(50) NOT NULL,
        `clima` varchar(30) NOT NULL,
        `edad` int(11) NOT NULL,
        `dieta` text DEFAULT NULL,
        `fecha_llegada` date NOT NULL,
        `habitat_id` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `habitat_id` (`habitat_id`),
        CONSTRAINT `animals_ibfk_1` FOREIGN KEY (`habitat_id`) REFERENCES `habitats` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    $pdo->exec($sql_animals);
    echo "<p style='color:green'>✅ Tabla 'animals' creada.</p>";

    // --- Tabla: Medical Records ---
    $sql_medical = "CREATE TABLE IF NOT EXISTS `medical_records` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `animal_id` int(11) NOT NULL,
        `fecha` date NOT NULL,
        `descripcion` text NOT NULL,
        `diagnostico` text DEFAULT NULL,
        `tratamiento` text DEFAULT NULL,
        `severidad` enum('Baja','Media','Alta') NOT NULL DEFAULT 'Baja',
        PRIMARY KEY (`id`),
        KEY `animal_id` (`animal_id`),
        CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    $pdo->exec($sql_medical);
    echo "<p style='color:green'>✅ Tabla 'medical_records' creada.</p>";

    // 5. INSERTAR DATOS INICIALES (ADMINISTRADOR)
    // Verificamos si ya existe el admin para no duplicarlo
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'usuario1@gmail.com'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Hash de la contraseña del SQL original
        $pass = '$2y$10$OqdrQ//SeDUDRCI5x1wxxeY8MmHhmNbgCihZi2y/3WhoyM4kuWZ2.';
        
        $sql_admin = "INSERT INTO `users` (`nombre_completo`, `email`, `password`, `rol`, `ultimo_acceso`) VALUES
        ('angelito', 'usuario1@gmail.com', '$pass', 'admin', NOW())";
        
        $pdo->exec($sql_admin);
        echo "<p style='color:blue'>ℹ️ Usuario Administrador por defecto creado (usuario1@gmail.com).</p>";
    } else {
        echo "<p style='color:orange'>⚠️ El usuario administrador ya existía.</p>";
    }

    echo "<hr>";
    echo "<h3>🚀 ¡Instalación completada con éxito!</h3>";
    echo "<p>Ya puedes configurar tu archivo <code>config/db.php</code> y usar el sistema.</p>";
    echo "<a href='../index.php'>Ir al Inicio</a>";

} catch (PDOException $e) {
    echo "<h3 style='color:red'>❌ Error Fatal:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>