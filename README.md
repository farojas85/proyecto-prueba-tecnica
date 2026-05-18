# Proyecto Técnico Modernizado: Laravel 11 + Vue 3 + Pinia + Redis + Tailwind CSS

Este repositorio contiene la solución completa de la modernización de la plataforma legacy. Se ha refactorizado profundamente el Backend a **Laravel 11**, el Frontend a **Vue 3 con Composition API y Pinia**, y se han integrado herramientas de alto rendimiento como **Redis** para caché y sesiones, **Swagger/Scramble** para documentación automatizada, y **Laravel Telescope** para auditoría local.

---

## 🚀 Guía de Inicio Rápido (Con Docker)

Sigue estos sencillos pasos para compilar, levantar y poblar la base de datos de tu aplicación en contenedores aislados de Docker. Esta ruta garantiza que puedas ejecutar todo el proyecto completo **sin necesidad de instalar PHP, Composer, MySQL o Node** en tu máquina host:

### 1. Configurar Variables de Entorno (.env)
Asegúrate de tener copiado el archivo de entorno en el backend si no existe:
```bash
cp backend-actualizado/.env.example backend-actualizado/.env
```

### 2. Iniciar los Contenedores
Compila las imágenes e inicia todos los servicios del ecosistema (Nginx, MySQL, Redis, Backend, Frontend):
```bash
docker compose up -d --build
```

### 3. Instalar Dependencias del Backend (Composer)
Descarga e instala las librerías necesarias del proyecto (incluyendo Scramble y Telescope):
```bash
docker compose exec backend composer install
```

### 4. Generar Clave de Aplicación (APP_KEY)
Genera la firma de cifrado de Laravel en tu archivo `.env`:
```bash
docker compose exec backend php artisan key:generate
```

### 5. Ejecutar Migraciones y Poblado de Datos (Seeders)
Crea las tablas (incluyendo la nueva tabla de auditoría `audit_logs`) y llena el inventario con datos de volumen optimizados en bloques:
```bash
docker compose exec backend php artisan migrate --seed
```

*(Opcional: Si deseas limpiar y reiniciar la base de datos por completo, puedes correr `docker compose exec backend php artisan migrate:fresh --seed`)*

### 6. Instalar y Configurar Laravel Telescope
Instala y publica los assets y base de datos para la interfaz de Telescope:
```bash
docker compose exec backend php artisan telescope:install
docker compose exec backend php artisan migrate
```

### 7. Ejecutar Pruebas Unitarias y Funcionales
Corre la suite completa de pruebas de Feature aisladas en memoria SQLite:
```bash
docker compose exec backend php artisan test
```

### 8. Monitorear Logs del Backend
Visualiza los registros y la salida del contenedor backend en tiempo real:
```bash
docker compose logs -f backend
```

### 9. Apagar los Contenedores
Detén y limpia los recursos del sistema al terminar de evaluar:
```bash
docker compose down
```

---

## 🌐 URLs de Acceso Directo

*   **Frontend (Aplicación de Inventario)**: 👉 [http://localhost:5173](http://localhost:5173)
*   **Backend (Servidor Nginx de API)**: 👉 [http://localhost:8000](http://localhost:8000)
*   **Documentación Interactiva (Swagger/Scramble)**: 👉 [http://localhost:8000/api/docs](http://localhost:8000/api/docs)
*   **Laravel Telescope (Auditoría de Consultas, Errores y Logs)**: 👉 [http://localhost:8000/telescope](http://localhost:8000/telescope)

*Credenciales por defecto para ingresar*:
*   **Usuario**: `admin@legacy.test`
*   **Contraseña**: `password`

---

## 🧪 Pruebas Automatizadas (Isolated Memory SQLite)

Se han implementado pruebas funcionales de Feature exhaustivas para validar la integridad del negocio (como la protección de borrado de categorías y movimientos de stock). Corre los tests de forma totalmente aislada sin afectar tu base de datos de desarrollo mediante:

```bash
sudo docker compose exec backend php artisan test
```

## 📊 Análisis y Planificación Inicial

Antes de proceder con la modernización, se realizaron auditorías completas de la deuda técnica, fallos de seguridad y diseño de arquitectura. Te invitamos a leer los documentos de análisis y planificación iniciales:

*   **[Análisis de Deuda Técnica y Seguridad del Backend](file:///home/fredy/Documentos/Prueba_tecnica/proyecto-prueba/analisis_backend.md)**: Identificación de inyección SQL, vulnerabilidades XSS, middleware de autenticación ausente y optimizaciones de consultas.
*   **[Análisis de Deuda Técnica del Frontend Legacy](file:///home/fredy/Documentos/Prueba_tecnica/proyecto-prueba/analisis_frontend.md)**: Diagnóstico del desorden de estado, acoplamiento de lógica, e ineficiencias de estilo.
*   **[Plan de Migración Paso a Paso a Laravel 11](file:///home/fredy/Documentos/Prueba_tecnica/proyecto-prueba/plan_migracion_laravel11.md)**: Estrategia estructurada para la actualización progresiva de dependencias y estructura de directorios.

---

## 🛠️ Decisiones Técnicas y Optimizaciones Aplicadas

### 1. Backend (Laravel 11 + Clean Architecture)
*   **Services Pattern**: Separamos la lógica de negocio y base de datos pesada de los controladores, moviéndola a clases de servicio especializadas (`ProductService`, `CategoryService`, `AuthService`).
*   **Requests Saneados**: Implementamos clases FormRequest que interceptan las peticiones e inyectan saneamiento `strip_tags()` automático para anular vulnerabilidades de XSS (Cross-Site Scripting).
*   **Resources de Datos**: Estandarizamos todas las respuestas JSON utilizando Eloquent API Resources (`ProductResource`, `CategoryResource`, `StockMovementResource`), garantizando la total consistencia de salida y seguridad en datos sensibles.

### 2. Frontend (Vue 3 + Composition API + Pinia)
*   **Pinia Store**: Desvinculamos el manejo de token manual de los componentes de vista, implementando un Store reactivo global (`src/stores/auth.js`) para manejar el ciclo de vida del inicio y cierre de sesión de forma segura.
*   **Estados UX**: Incorporamos variables de carga `isLoading` e `isSaving` en todas las interacciones de formularios y tablas para congelar inputs durante la comunicación con el servidor y evitar pulsaciones dobles de red.
*   **Tailwind CSS v3 Premium**: Rediseñamos el 100% de la interfaz con elementos interactivos, sombras, desenfoques ("glassmorphism"), degradados profesionales y adaptabilidad total responsive para móviles, tabletas y ordenadores.

### 3. Base de Datos, Rendimiento y Concurrencia
*   **Paginación y Búsqueda**: Añadimos controles dinámicos de tamaño de página (10, 25, 50, 75, 100) y búsquedas directas en base de datos.
*   **Bloqueo Pesimista (Pessimistic Lock)**: Para evitar condiciones de carrera (Race Conditions) y asegurar la exactitud física en los movimientos de stock, el sistema bloquea los registros concurrentes utilizando `lockForUpdate` antes de procesar cualquier entrada o salida.
*   **Redis Cache & Session**: Migramos los archivos de almacenamiento de Laravel a una infraestructura de memoria Redis, disparando la velocidad de lectura/escritura de la sesión del usuario.

### 4. Sistema de Auditoría de Acciones (Crear, Editar, Borrar y Stock)
Diseñamos un **Trait `Auditable`** genérico y altamente extensible que se auto-registra en los modelos `Product`, `Category` y `StockMovement` mediante hooks de Eloquent (`created`, `updated`, `deleted`).
Cada vez que un usuario realiza una acción, el sistema registra en la tabla `audit_logs`:
*   El usuario autenticado que inició la petición.
*   El tipo de acción (`create`, `update`, `delete`).
*   El modelo y el ID afectado.
*   **Valores Antiguos (`old_values`)** y **Valores Nuevos (`new_values`)** guardados en formato JSON estructurado.
*   La dirección IP desde donde se emitió el ajuste.
