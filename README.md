# 🦁 ZooManager - Sistema de Gestión de Zoológico

**ZooManager** es una plataforma web integral desarrollada en **PHP Nativo (Vanilla PHP)** bajo una arquitectura modular tipo MVC. Este sistema administra hábitats, animales y registros médicos, implementando reglas de negocio complejas, seguridad defensiva y una interfaz moderna basada en principios de **HCI/UX**.

---

## 📋 Características Destacadas

### 🧠 Lógica de Negocio y Validaciones ("Complex Validation")
El sistema implementa reglas estrictas para mantener la coherencia biológica y operativa:
1.  **Compatibilidad Climática:** El sistema impide asignar un animal a un hábitat con un clima incompatible (ej: Un Pingüino [Polar] no puede vivir en la Sabana).
2.  **Control de Capacidad:** No permite exceder el límite máximo de animales por hábitat.
3.  **Integridad en Actualizaciones:** No permite reducir la capacidad de un hábitat si la cantidad de animales actuales supera el nuevo límite propuesto.

### 🔐 Seguridad Avanzada
* **Protección CSRF:** Formularios de eliminación protegidos contra ataques *Cross-Site Request Forgery* (uso estricto de POST).
* **Defensa en Profundidad:**
    * Archivos críticos (`config/`, `includes/`) protegidos contra acceso directo vía `.htaccess` y bloqueos a nivel de PHP.
    * **Anti-Intrusión (Error 403):** Sistema de disuasión personalizado para accesos no autorizados.
* **Manejo de Errores:** Páginas personalizadas para errores 404 (No encontrado) y 500 (Error del servidor) para evitar exponer rutas o datos técnicos.
* **Sanitización:** Prevención de XSS (Cross-Site Scripting) en todas las entradas y salidas de datos.

### 💻 Interfaz y Experiencia de Usuario (UX)
* Diseño **Glassmorphism** limpio y moderno usando Bootstrap 5.
* Feedback visual inmediato (Alertas de éxito/error).
* Iconografía intuitiva (Bootstrap Icons) para facilitar la navegación.

---

## 📂 Estructura del Proyecto

La estructura está organizada para separar la lógica de la presentación:

```text
zoo-system/
├── actions/           # MOTOR: Recibe peticiones POST, procesa lógica y redirige
│   ├── animals/       # Lógica para Animales
│   ├── auth/          # Lógica de Autenticación (Login/Register)
│   ├── habitats/      # Lógica para Hábitats
│   └── medical/       # Lógica para Historial Médico
├── assets/            # RECURSOS: CSS, JS, Imágenes y Sonidos
│   ├── img/           # Incluye recursos de error (404, 500, troll)
│   └── sounds/        # Audio para alertas de seguridad
├── config/            # CONFIGURACIÓN: Base de datos e instalación
│   ├── db_example.php # Plantilla de conexión segura
│   └── install.php    # Script de instalación automática
├── includes/          # COMPONENTES: Header, Footer, Funciones Globales
├── views/             # VISTA: Interfaz de usuario (HTML + PHP)
│   ├── admin/         # Paneles de gestión
│   ├── auth/          # Login y Registro
│   ├── errors/        # Páginas de error personalizadas (403, 404, 500)
│   └── medical/       # Vistas de historial médico
├── index.php          # Dashboard principal
└── .htaccess          # Reglas de seguridad del servidor Apache

🚀 Instalación y Despliegue
Requisitos Previos
Servidor Web (Apache/Nginx)

PHP 8.0 o superior

MySQL / MariaDB

Pasos
Clonar/Descargar el repositorio en tu carpeta htdocs o www.

Configurar Base de Datos:

Ve a la carpeta config/.

Renombra db_example.php a db.php.

Edita db.php con tus credenciales (Host, Usuario, Contraseña, Puerto).

Instalar Tablas:

Desde el navegador, accede a: http://localhost/zoo-system/config/install.php

Esto creará la base de datos zoo_system y las tablas necesarias automáticamente.

Nota: Se creará un usuario administrador por defecto (ver pantalla de instalación).

Finalizar:

Por seguridad, elimina o bloquea el acceso a install.php una vez finalizada la instalación.