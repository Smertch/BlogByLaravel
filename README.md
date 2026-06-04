# Blog (Laravel)

Laravel **13** application with **Filament** admin, **Fortify** authentication, Docker-based local stack (Nginx, PHP 8.5-FPM, MySQL 8, Redis, Mailhog), and GitLab CI (Pint + tests).

## Requirements

- **Docker** and **Docker Compose** (recommended for full stack)
- **PHP 8.5+** and **Composer 2** (optional: Artisan/Composer on the host while MySQL runs in Docker)
- **Node.js 20+** and **npm** (only if you change frontend assets built with Vite)

## Quick start (Docker)

1. **Clone** the repository and go to the project root.

2. **Environment file**

   ```bash
   cp .env.example .env
   ```

   Adjust values if needed. For Docker, keep MySQL-related settings aligned with `docker-compose.yml` (see below).

3. **Install PHP dependencies**

   ```bash
   docker compose run --rm php-fpm composer install
   ```

   Or, on the host (PHP 8.5+):

   ```bash
   composer install
   ```

4. **Application key**

   ```bash
   docker compose run --rm php-fpm php artisan key:generate
   ```

   Or: `php artisan key:generate` on the host.

5. **Start containers**

   ```bash
   docker compose build
   docker compose up -d
   ```

   Wait until MySQL is healthy (php-fpm waits on it).

6. **Database migrations**

   From the host (MySQL is exposed on port **1517**):

   ```bash
   php artisan migrate
   ```

   Or inside the app container:

   ```bash
   docker compose exec php-fpm php artisan migrate
   ```

7. **Open the app**

   - Site: **http://localhost:1515**
   - Mailhog UI: **http://localhost:1516**
   - Filament admin: **http://localhost:1515/admin**

   Create an admin-capable user (from the host or via `docker compose exec php-fpm`):

   ```bash
   php artisan filament:user
   ```

### Published ports

| Service   | Host port | Purpose        |
|-----------|-----------|----------------|
| webserver | 1515      | HTTP (Nginx)   |
| mysql     | 1517      | MySQL (CLI/tools on host) |
| mailhog   | 1516      | Mail UI        |

### Database: host vs container

- **`.env` for Artisan on the host** (e.g. `php artisan migrate`): use `DB_HOST=127.0.0.1` and `DB_PORT=1517` with the credentials from `docker-compose.yml` (`blog` / `user` / `password`).
- **PHP-FPM in Docker** gets `DB_HOST=mysql` and `DB_PORT=3306` from `docker-compose.yml` (overrides `.env`), so the web app talks to the MySQL service by name.

Do not commit `.env` (it is listed in `.gitignore`).

## Optional: Vite assets

If you modify JS/CSS managed by Vite:

```bash
npm install
npm run dev
# or for a production build:
npm run build
```

## Quality checks

```bash
composer lint        # Laravel Pint (no writes)
composer lint:fix    # Pint fixes
composer test        # PHPUnit via artisan
php artisan test
```

## CI

`.gitlab-ci.yml` runs **Pint** (`vendor/bin/pint --test`) and **`php artisan test`** on PHP 8.5.

## Project layout (essentials)

- `app/` — application code
- `bootstrap/`, `config/`, `routes/` — bootstrap and configuration
- `database/migrations/` — schema
- `docker/`, `docker-compose.yml` — local stack
- `lang/`, `resources/views/` — translations and Blade
- `public/` — web root
- `pint.json` — Pint configuration
- `.env.example` — environment template (copy to `.env`)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
