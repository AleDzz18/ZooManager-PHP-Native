# 🦁 ZooManager - Sistema de Gestión de Zoológico

**ZooManager** es una plataforma web integral desarrollada en **PHP Nativo (Vanilla PHP 8.2)** y **MySQL** bajo una arquitectura modular tipo MVC. Este sistema administra hábitats, animales y registros médicos, destacando por su implementación de reglas de negocio complejas, control estricto de concurrencia y un enfoque de defensa en profundidad para la seguridad.

---

## 📋 Características Destacadas

### 🧠 Lógica de Negocio y Validaciones
El sistema implementa reglas estrictas para mantener la coherencia biológica y operativa:
*   **Compatibilidad Climática:** Impide asignar un animal a un hábitat con un clima incompatible (ej: Un animal de clima 'Polar' no puede vivir en la 'Sabana').
*   **Control de Capacidad:** No permite exceder el límite máximo de animales por hábitat.
*   **Integridad en Actualizaciones:** Bloquea la reducción de la capacidad de un hábitat si la cantidad de animales actuales supera el nuevo límite propuesto.
*   **Coherencia Temporal:** Calcula fechas de nacimiento estimadas basadas en la edad para prevenir inconsistencias lógicas (ej: un animal no puede tener una fecha de llegada al zoológico anterior a su nacimiento).

### 🔐 Seguridad de Grado Empresarial (Defensa en Profundidad)
*   **Prevención de Condición de Carrera (Race Conditions):** Uso de transacciones SQL (`beginTransaction`, `commit`) y bloqueos de fila (`FOR UPDATE`) para garantizar la integridad de los datos si múltiples usuarios actualizan la capacidad de un hábitat simultáneamente.
*   **Manejo de Errores Silencioso:** Atrapa excepciones técnicas (como caídas de base de datos) mediante `registrarErrorCritico()`, registrándolas en un log del servidor y mostrando vistas genéricas al usuario (Error 500) para prevenir fugas de información (Information Disclosure).
*   **Blindaje de Rutas y Anti-Acceso Directo:** Redireccionamiento dinámico mediante constante `BASE_URL`. Las acciones operativas están blindadas con `soloMetodoPost()` y los archivos sensibles protegidos mediante reglas estrictas en `.htaccess`.
*   **Control de Acceso Basado en Roles (RBAC):** Privilegios segmentados de forma granular. Los administradores gestionan infraestructura y personal, mientras que los cuidadores pueden gestionar y eliminar los historiales médicos de sus pacientes.
*   **Autenticación Robusta:** Encriptación irreversible de claves con `password_hash`, mitigación de secuestro de sesión mediante `session_regenerate_id(true)` y prevención de ataques CSRF mediante tokens únicos de sesión.

### 💻 Interfaz y Experiencia de Usuario (UX)
*   **Estética Moderna:** Diseño **Glassmorphism** (efecto de cristal esmerilado) creado con Bootstrap 5 y CSS personalizado, totalmente responsivo y visualmente inmersivo.
*   **Indicadores Dinámicos:** Barras de progreso de ocupación que cambian semánticamente de color (verde, amarillo, rojo) según la disponibilidad del hábitat.
*   **Páginas de Error Temáticas:** Vistas personalizadas y amigables para errores 404 (No encontrado) y 500 (Error del Servidor). Incluye un sistema disuasivo interactivo ("trolleo") en caso de accesos no autorizados (Error 403).
*   **Offline-Ready:** Los recursos visuales, hojas de estilo y tipografías (Bootstrap Icons) se ejecutan localmente sin depender de CDNs externas.

---

## 📂 Estructura del Proyecto

La estructura separa limpiamente la lógica de la presentación:

```text
zoo-system/
├── actions/           # MOTOR (Controladores): Procesa lógica, transacciones SQL y redirige
│   ├── animals/       # CRUD y validaciones biológicas para Animales
│   ├── auth/          # Login, Registro (CSRF tokens) y Cierre de sesión seguro
│   ├── habitats/      # CRUD y control de concurrencia para Hábitats
│   └── medical/       # Gestión clínica y RBAC de Historial Médico
├── assets/            # RECURSOS: CSS (Glassmorphism), JS, Imágenes y Sonidos
├── config/            # CONFIGURACIÓN:
│   ├── db_example.php # Plantilla segura para credenciales
│   ├── db.php         # Conexión local PDO con fallback de errores (No rastreado)
│   └── .htaccess      # Bloqueo total de directorio
├── includes/          # COMPONENTES CORE: 
│   ├── auth_check.php # Middleware de barrera de sesión
│   ├── functions.php  # Utilidades, sanitización, RBAC y errores silenciosos
│   └── header/footer  # UI base con menús dinámicos
├── views/             # VISTAS: Interfaz de usuario (HTML + PHP)
│   ├── admin/         # Paneles de gestión de entidades
│   ├── auth/          # Formularios de acceso blindados
│   ├── errors/        # Páginas 403 (Troll), 404 y 500 personalizadas
│   └── medical/       # Interfaces de historial clínico
├── index.php          # Dashboard principal adaptable al rol
└── .htaccess          # Prevención de listado y ocultación de firma Apache

🚀 Instalación y Despliegue
Requisitos Previos
Servidor Web (Apache/Nginx).

PHP 8.0 o superior.

MySQL / MariaDB.

Pasos de Instalación
Clonar el Repositorio: Descarga el proyecto en tu carpeta local (ej: htdocs en XAMPP o www).

Preparar Credenciales:

Ve a la carpeta config/ y duplica el archivo db_example.php.

Renombra la copia a db.php.

Edita db.php colocando el host, puerto (ej. 3306 o 3307), usuario (ej: root) y la contraseña de tu entorno local.

Base de Datos: Crea una base de datos local llamada zoo_system e importa tu esquema SQL a través de phpMyAdmin o terminal para restaurar la estructura.

Acceso: Abre tu navegador y navega a http://localhost/zoo-system (Asegúrate de que la constante BASE_URL en tu archivo db.php coincida con la ruta de tu entorno).