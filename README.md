# ZooManager-PHP-Native
ZooManager: A comprehensive system in native PHP for zoo management. It includes secure authentication, user roles, and a complete CRUD interface for habitats and animals. It implements complex business logic for capacity control and species compatibility, a relational database, and a clean architecture ideal for academic defense.

🦁 ZooManager: Sistema de Gestión de Zoológico
Bienvenido al repositorio oficial del proyecto. Este sistema ha sido diseñado para cumplir con los estándares académicos de desarrollo en PHP Nativo, enfocándose en la seguridad, la integridad de los datos y una arquitectura limpia.

🛠️ Requisitos Técnicos Implementados
Para cumplir con la actividad, el proyecto incluye:

Autenticación: Registro de usuarios, inicio de sesión con contraseñas encriptadas y manejo de sesiones seguras.

CRUD Completo: Gestión de la entidad principal (Animales) con operaciones de Crear, Leer, Actualizar y Eliminar (con confirmación).

Base de Datos: Estructura relacional con 4 tablas vinculadas mediante llaves primarias y foráneas.

Arquitectura: Organización modular de carpetas para separar la lógica de procesamiento (actions/) de la interfaz visual (views/).

🧠 Regla de Negocio Compleja (Validación Especial)
Nuestro diferencial y requisito obligatorio es la Validación de Capacidad y Compatibilidad:

Validación de Capacidad: Antes de registrar un animal, el sistema consulta la tabla habitats para verificar si hay cupo disponible.

Validación de Clima: Se cruza la información entre la especie y el tipo de hábitat. No se permite asignar un animal a un entorno que no sea compatible con su clima biológico.

📂 Guía de Estructura para Programadores
Para mantener el orden, sigamos estas reglas:

Vistas (/views): Solo contienen HTML y echo de PHP para mostrar datos. No procesan formularios.

Acciones (/actions): Aquí va la lógica pura. Reciben datos por POST, validan, ejecutan SQL y redireccionan.


Seguridad: Toda página administrativa debe incluir el archivo auth_check.php al inicio para verificar la sesión activa.

🚀 Cómo empezar
Clona el repositorio en tu carpeta local del servidor (ej. htdocs).

Crea el archivo config/db.php (no se sube al repo por seguridad) con tus credenciales locales.