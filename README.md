# 🦁 ZooManager - Sistema de Gestión de Zoológico

**ZooManager** es una aplicación web robusta desarrollada en **PHP Nativo (Vanilla PHP)** bajo una arquitectura MVC simplificada. Este sistema permite la administración integral de hábitats, animales y registros médicos, implementando reglas de negocio complejas y una interfaz moderna basada en **Glassmorphism**.

![Estado](https://img.shields.io/badge/Estado-Finalizado-success) ![PHP](https://img.shields.io/badge/PHP-8.2-blue) ![MySQL](https://img.shields.io/badge/DB-MySQL-orange) ![Bootstrap](https://img.shields.io/badge/Frontend-Bootstrap_5-purple)

---

## 📋 Características Técnicas

Este proyecto fue diseñado priorizando la seguridad, la integridad de datos y la independencia de librerías externas pesadas.

### 🔐 Seguridad y Arquitectura
* **Autenticación Segura:** Login y registro con hash de contraseñas (`password_hash`), protección contra fuerza bruta y manejo de sesiones seguras.
* **Prevención de Cache:** Cabeceras HTTP implementadas para evitar que el botón "Atrás" del navegador muestre páginas protegidas tras el logout.
* **Consultas Preparadas (PDO):** Protección total contra inyección SQL.
* **Arquitectura Modular:** Separación clara entre Vistas (`views/`), Lógica (`actions/`) y Configuración.

### 🧠 Reglas de Negocio (Validaciones)
El sistema implementa lógica estricta para garantizar la coherencia biológica:
1.  **Control de Capacidad:** No permite añadir animales si el hábitat ha alcanzado su límite.
2.  **Compatibilidad Climática:** Valida que el clima del animal coincida con el del hábitat (ej. no permite un animal *Polar* en un hábitat *Desértico*).
3.  **Consistencia Temporal:** Impide registrar una fecha de llegada anterior a la fecha de nacimiento estimada del animal.

### 🎨 Frontend
* **Diseño Glassmorphism:** Interfaz moderna con efectos de desenfoque y transparencias.
* **Modo Offline:** Utiliza **Bootstrap Icons** descargados localmente, eliminando la dependencia de CDNs (funciona sin internet).

---

## 🚀 Guía de Instalación (Paso a Paso)

Para poner en marcha el proyecto en tu servidor local (XAMPP, WAMP, etc.), sigue estos pasos:

### 1. Clonar el Proyecto
Coloca la carpeta del proyecto dentro de tu directorio público (ej. `C:/xampp/htdocs/zoo-system`).

### 2. Base de Datos
Hemos incluido un script de instalación automática. No necesitas importar SQL manualmente.
1.  Enciende tu servidor **Apache** y **MySQL**.
2.  Abre tu navegador y ejecuta la siguiente ruta:
    ```
    http://localhost/zoo-system/config/install.php
    ```
3.  El script creará la base de datos `zoo_system`, las tablas y las relaciones automáticamente.

### 3. Configuración de Conexión
Para que el sistema se conecte a la base de datos recién creada:
1.  Ve a la carpeta `config/`.
2.  Busca el archivo `db_example.php`.
3.  **Renómbralo** a `db.php`.
4.  Ábrelo y verifica tus credenciales (por defecto en XAMPP suelen ser):
    ```php
    $host = 'localhost';
    $dbname = 'zoo_system';
    $username = 'root';
    $password = ''; // Vacío en XAMPP
    ```

### 4. Acceder
¡Listo! Ya puedes ir a la página de inicio:

---

## 👤 Credenciales por Defecto

El instalador crea automáticamente un usuario **Administrador** para que puedas empezar a gestionar:

* **Email:** `usuario1@gmail.com`
* **Contraseña:** (La contraseña por defecto se define en el script `install.php`, generalmente configurada durante el desarrollo).

> **Nota:** Puedes registrar nuevos usuarios desde la pantalla de registro. El primer usuario siempre tendrá rol de Administrador si se usa el script por defecto.

---

## 📂 Estructura del Directorio

Para facilitar la navegación del código a otros desarrolladores:

```text
zoo-system/
├── actions/           # Lógica del servidor (Recibe POST, procesa y redirige)
│   ├── animals/       # CRUD de Animales
│   ├── auth/          # Login, Register, Logout
│   ├── habitats/      # CRUD de Hábitats
│   └── medical/       # Lógica de historial médico
├── assets/            # Recursos estáticos
│   ├── bootstrap-icons/ # Iconos locales (svg/fonts)
│   ├── css/           # Estilos personalizados (Glassmorphism)
│   └── img/           # Imágenes del sitio
├── config/            # Archivos de conexión a BD (db.php)
├── includes/          # Fragmentos PHP reutilizables (Header, Footer, Auth Check)
├── views/             # Interfaz de Usuario (HTML + PHP para mostrar datos)
│   ├── admin/         # Vistas de gestión
│   ├── auth/          # Formularios de acceso
│   └── medical/       # Vistas de historia clínica
└── index.php          # Dashboard principal