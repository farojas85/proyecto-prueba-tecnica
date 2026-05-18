# Plan de Migración: Laravel 8 a Laravel 11

Migrar una aplicación desde Laravel 8 hasta Laravel 11 es un salto de tres versiones mayores (8 → 9 → 10 → 11). Requiere una estrategia cuidadosa, ya que ha habido cambios arquitectónicos profundos, especialmente en Laravel 11 (estructura de carpetas minimalista).

A continuación, se detalla la hoja de ruta recomendada para llevar este proyecto a la última versión.

## Fase 1: Actualización de la Infraestructura (Docker)

Laravel 11 requiere estrictamente **PHP 8.2** o superior.
* **[MODIFICAR] `docker/backend/Dockerfile`**:
  * Cambiar la imagen base de `php:8.0-fpm` a `php:8.3-fpm-alpine`.
  * Instalar extensiones actualizadas requeridas por PHP 8.3.
* **[MODIFICAR] `docker-compose.yml`**:
  * Confirmar que MySQL está en la versión 8.0 o superior (ya lo hemos configurado correctamente en este proyecto).

## Fase 2: Estrategia de Actualización de Dependencias

Se desaconseja saltar de Laravel 8 directamente a 11 modificando el `composer.json` de golpe. Lo recomendado es hacer saltos incrementales para detectar qué paquete de terceros se rompe:

1. **Salto a Laravel 9 (PHP 8.0+)**:
   * Cambiar `"laravel/framework": "^9.0"`.
   * Migrar de Flysystem 1.x a 3.x (si hubiese archivos en disco).
2. **Salto a Laravel 10 (PHP 8.1+)**:
   * Cambiar `"laravel/framework": "^10.0"`.
   * Revisar `$casts` en los Modelos (los dates cambiaron su comportamiento).
   * Eliminar métodos obsoletos de las validaciones.
3. **Salto a Laravel 11 (PHP 8.2+)**:
   * Cambiar `"laravel/framework": "^11.0"`.
   * Actualizar dependencias core como `"nunomaduro/collision"`.

*(Nota: Alternativamente, se recomienda altamente utilizar un servicio automatizado como **Laravel Shift** o la herramienta open-source **Rector PHP** para hacer la actualización de sintaxis automáticamente).*

## Fase 3: Adopción de la Nueva Estructura de Laravel 11

Laravel 11 introdujo una arquitectura "Minimalista" que reduce la cantidad de archivos repetitivos.

> [!WARNING]
> Laravel 11 cambia radicalmente el arranque de la aplicación. Tu proyecto actual usa el sistema tradicional, por lo que tendrás que decidir si mantener la estructura vieja (Laravel 11 es retrocompatible) o migrar a la nueva.

Si decides modernizar la estructura (Recomendado):

1. **Nuevo `bootstrap/app.php`**:
   * En Laravel 11, los archivos `app/Http/Kernel.php`, `app/Console/Kernel.php` y `app/Exceptions/Handler.php` desaparecen.
   * Toda la configuración de Middlewares (incluyendo el CORS que reparamos) y el ruteo, debe registrarse ahora en `bootstrap/app.php`.
2. **Instalación de la API**:
   * En Laravel 11, el archivo `routes/api.php` ya no viene por defecto. Debes ejecutar `php artisan install:api` para que Laravel genere el enrutador de API y configure Laravel Sanctum automáticamente.
3. **Limpieza de Configuración (`config/`)**:
   * Irónicamente, el desarrollador que borró los archivos de la carpeta `config/` en este proyecto se adelantó a su tiempo. Laravel 11 funciona mediante configuración en cascada y la carpeta `config/` puede estar completamente vacía. Se podrán eliminar los archivos `session.php`, `cors.php` y `view.php` que restauramos, ya que Laravel 11 los carga transparentemente desde el framework base a menos que quieras sobreescribirlos mediante comandos.

## Fase 4: Refactorización de Código y Seguridad (Sanctum)

* **Migración a Laravel Sanctum:**
  * El actual sistema implementa un inicio de sesión "manual" devolviendo un string. Para Laravel 11, se integrará **Laravel Sanctum** para autenticar SPAs o generar Tokens de API.
  * Habrá que cambiar el `guard` en `api` para que use `sanctum`.
* **Tipado Estricto (Strict Types):**
  * Con PHP 8.2+, se deben agregar tipados estrictos a los Controladores (ej: `public function index(Request $request): JsonResponse`).

## Fase 5: Pruebas y Despliegue

* **Pest PHP:** Reemplazar el antiguo PHPUnit por el nuevo framework de pruebas estándar de Laravel (Pest), que tiene una sintaxis mucho más limpia.
* Ejecutar la suite de pruebas automatizadas contra la nueva base de datos levantada por Docker.
* Construir las imágenes finales y verificar latencia de la aplicación.
