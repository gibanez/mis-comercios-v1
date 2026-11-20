# Proyecto — documentación específica

> **Qué es este archivo.** Documenta **este proyecto en particular**: qué módulos tiene, qué decisiones propias tomó y cómo se despliega. Se completa/edita en cada fork.
> `AGENTS.md` contiene las convenciones **genéricas** del stack (iguales en todos los proyectos): no hace falta tocarlo acá.
> OpenCode carga este archivo automáticamente porque está listado en `opencode.jsonc` (`instructions`).
> Si una decisión de este proyecto contradice a `AGENTS.md`, gana este archivo (se carga después).

## Identidad

- **Nombre:** mis-comercios
- **Descripción:** Backend PHP 8.2 + Slim 4 + DDD liviano + MySQL 8. Base con Auth, Admin, Page e Inquiry; a extender con los módulos propios del proyecto.
- **Estado:** en desarrollo.

## Stack

- **PHP:** 8.2 (`config.platform` en `composer.json`).
- **Base de datos:** MySQL 8 (`mis_comercios`), migrada con Phinx.
- **Frontends:** HTML + CSS + JS sin build (sitio en `public/`, CMS en `public/admin/`).
- **Hosting / deploy:** `php -S localhost:9980 -t public` para desarrollo; Apache con `public/.htaccess` para producción.

## Módulos

| Módulo | Ruta base | Entidad principal | Estado |
| --- | --- | --- | --- |
| `Auth` | `POST /api/auth/login` | – | listo (JWT HS256) |
| `Admin` | – | `Admin` | listo (repositorio + seed) |
| `Page` | `/api/pages` (protegido) y `/api/public/pages/{slug}` | `Page` | listo (CRUD + lectura pública) |
| `Inquiry` | `POST /api/inquiries` y `GET /api/inquiries` (protegido) | `Inquiry` | listo |

Rutas de salud: `GET /api/ping`.

## Autenticación

- `POST /api/auth/login` recibe `{ email, password }` y devuelve `{ token, admin }`.
- Token JWT HS256 firmado con `JWT_SECRET`, con `sub` = id del admin y vencimiento `JWT_TTL`.
- `JwtAuthMiddleware` protege las rutas de escritura de `Page` y el listado de `Inquiry`.
- El CMS guarda el token en `localStorage` (`cms_token`) y lo manda como `Authorization: Bearer …`.
- Usuario semilla de desarrollo: `admin@example.com` / `admin1234`.

## Frontends / apps

- **Sitio** (`public/`): Vue 3 + Tailwind CSS. SPA o multi-página según evolución del proyecto. Consume la API pública (`/api/public/...`).
- **CMS** (`public/admin/`): Vue 3 + PrimeVue. Panel de administración con login JWT, listados con DataTable de Prime y formularios en diálogos/modales de Prime. Consume la API protegida (`Authorization: Bearer …`).
- Ambos frontends se buildean con Vite y el output va a `public/` y `public/admin/` respectivamente.

## Base de datos y migraciones propias

- Motor: MySQL 8, charset `utf8mb4`, collation `utf8mb4_unicode_ci`.
- `admins`: `id` (UUID), `name`, `email` único, `password_hash`, `status`.
- `pages`: `id` (UUID), `slug` único, `title`, `body`, `status`.
- `inquiries`: `id` (UUID), `name`, `email`, `message`, `created_at`.
- Semillas: un admin de desarrollo y las páginas `home`, `about`, `contact`.
- Comandos: `vendor/bin/phinx migrate`, `vendor/bin/phinx rollback`, `vendor/bin/phinx status`.
- Crear la base antes de migrar: `CREATE DATABASE IF NOT EXISTS mis_comercios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`

## Decisiones y patrones propios

- El CMS es **vanilla JS** a propósito: cero build, se abre en `/admin/` y consume la API con `fetch`.
- El sitio también es estático salvo la home, que se hidrata desde la API.
- El slug se normaliza en el controller (`strtolower`, espacios → `-`, solo `[a-z0-9-]`).
- `Page::STATUS_ACTIVE` / `STATUS_INACTIVE`; la ruta pública devuelve 404 si la página está inactiva.
- `ext-sodium` se declara en `config.platform` de Composer porque el PHP local no la trae; HS256 no la usa.

## Deploy

1. `composer install --no-dev --optimize-autoloader` (con `--ignore-platform-req=ext-sodium` si hiciera falta).
2. Apuntar el document root a `public/` (o usar el `.htaccess` raíz descripto en `AGENTS.md`).
3. Generar `.env` con `JWT_SECRET` propio y `APP_DEBUG=false`.
4. Subir `storage/database.sqlite` ya migrado (o correr `phinx migrate` en el server).

## Gotchas

- `lcobucci/jwt 4.3`: `ValidAt` recibe un `Lcobucci\Clock\Clock`, no un `DateTimeImmutable`; usar `new ValidAt(SystemClock::fromUTC())`.
- `php -S` no lee `.htaccess`: las rutas caen al `index.php` por el fallback del servidor embebido.
- El sitio vive en `public/`; el CMS en `public/admin/`. No moverlos sin ajustar las rutas de los assets.
- `APP_DEBUG=true` muestra el detalle de las excepciones: apagarlo en producción.
- Si se cambia el usuario semilla, actualizar también la leyenda del login del CMS.
- MySQL debe estar corriendo y la base `mis_comercios` creada antes de correr `phinx migrate`.

## Pendientes

- [ ] Marcar el repo como template en GitHub (Settings → Template repository).
- [ ] Agregar tests de integración HTTP (hoy hay tests de dominio/aplicación con Pest).
- [ ] Sumar un módulo de usuarios/administradores con CRUD (hoy el admin solo se siembra).
- [ ] Reemplazar el front vanilla por Astro/Vue si el proyecto lo requiere.
