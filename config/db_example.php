<?php
/*
    ---------------------------------------------------
    ARCHIVO DE CONEXIÓN A BASE DE DATOS (PDO)
    ---------------------------------------------------
*/

// 1. Credenciales de la Base de Datos
$host = '';
$port = '';          // ¡IMPORTANTE! Tu puerto personalizado
$dbname = '';  // El nombre que le pusimos en phpMyAdmin
$username = '';      // Usuario por defecto de XAMPP
$password = '';          // En XAMPP, la contraseña por defecto es vacía

try {
    // 2. Crear la cadena de conexión (DSN)
    // Es como marcar el número de teléfono exacto de la base de datos
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    // 3. Crear la instancia PDO (El objeto que conecta)
    $pdo = new PDO($dsn, $username, $password);

    // 4. Configurar el modo de errores
    // Le decimos a PHP: "Si algo falla, lanza una ALERTA (Excepción) inmediatamente"
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Opcional: Configurar el modo de fetch por defecto a Array Asociativo
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Si llegamos aquí, la conexión fue exitosa (pero no mostramos nada para no ensuciar la web)

} catch (PDOException $e) {
    // 5. Manejo de Errores (El "Airbag")
    // Si la conexión falla, el código salta aquí para no mostrar datos sensibles al usuario
    die("Error crítico de conexión: " . $e->getMessage());
}