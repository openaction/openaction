# Repository Guidelines

## Project Structure & Module Organization
- `console/`: Symfony back‑office and API. Code in `src/`, templates in `templates/`, DB migrations in `migrations/`, assets in `assets/{modern,legacy,projects}`, tests in `tests/`.
- `public/`: Symfony frontend renderer for public websites. Code in `src/`, templates in `templates/`, assets in `assets/`, tests in `tests/`.
- `docs/`, `docker compose.yaml`, `bin/`, and `.github/` contain documentation, local stack, binaries, and CI/config.

## Build, Test, and Development Commands
- Start services: `docker compose up -d`.
- Install PHP deps: `docker compose exec console composer install` and `docker compose exec public composer install`.
- Build assets:
  - Console (per package):
    - `cd console/assets/modern && yarn install && yarn build`
    - `cd console/assets/legacy && yarn install && yarn build`
    - `cd console/assets/projects && yarn install && yarn build`
  - Public: `cd public && yarn install && yarn build`.
- Run tests:
  - Console: `docker compose exec console bin/phpunit`
  - Public: `docker compose exec public bin/phpunit`

## Coding Style & Naming Conventions
- PHP: PSR‑12, 4‑space indentation. Follow Symfony structure (`src/Controller`, `src/Command`, `src/Entity`, etc.).
- JS/TS: ESLint in `console/assets/modern` (`yarn lint`); Prettier used in legacy/projects. React components in PascalCase; other files kebab‑case.
- Formatting: PHP-CS-Fixer configs are provided (`.php-cs-fixer.dist.php`). Example: `cd console && php php-cs-fixer.phar fix`.

## Testing Guidelines
- Framework: PHPUnit. Place tests under `tests/` and name files `*Test.php`.
- Coverage is configured via `phpunit.xml.dist` (includes `src/`).
- Console tests use `DAMA\\DoctrineTestBundle` for DB isolation; avoid hitting real services—mock external clients.

## Commit & Pull Request Guidelines
- Commits: short, imperative subject; include scope or area when helpful; reference issues/PRs like `(#201)`.
- Pull Requests: provide a clear description, linked issues, and screenshots/GIFs for UI changes. Note DB migrations and data impacts. Ensure tests pass locally and CI is green.

## Security & Configuration Tips
- Configure via `.env` and `.env.local`; never commit secrets. Use `docker compose.override.yaml` to expose ports as needed and map `console` in `/etc/hosts`.
