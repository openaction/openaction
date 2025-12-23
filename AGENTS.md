# Agent Playbook: Reproduce CI Locally (Symfony CLI)

This guide describes how to replicate the CI pipeline defined in `.github/workflows/ci.yaml` on your machine 
using Symfony CLI for running the PHP applications locally. Follow it before pushing to ensure your changes pass CI.

## Prerequisites

- PHP 8.1 with Composer v2.
- Symfony CLI installed (`symfony -v`).
- Node.js 22 + Yarn (`corepack enable` sets up Yarn 4 on Node 22).
- Docker (services only) to run dependencies (PostgreSQL, Redis, Meilisearch).
- GitHub CLI (`gh`) logged in (`gh auth login`). Always use `gh` for PRs and CI checks.
- Brevo marketing SDK installed via Composer (`getbrevo/brevo-php`).

## Services (Postgres, Redis, Meilisearch)

The CI brings up services with Docker. Locally, do the same:

```
cp compose.override.yaml.dist compose.override.yaml
docker compose up -d --quiet-pull
```

## PHP Dependencies

Install Composer deps with Symfony CLI (runs PHP locally, not in Docker):

```
cd console && symfony composer install --prefer-dist --no-interaction --no-ansi --no-progress
cd ../public && symfony composer install --prefer-dist --no-interaction --no-ansi --no-progress
cd ..
```

## Coding Style and Translations

- Console PHP CS:
  - `cd console && symfony composer php-cs-fixer-install && symfony composer php-cs-fixer-check`
- Public PHP CS:
  - `cd public && symfony composer php-cs-fixer-install && symfony composer php-cs-fixer-check`
- Translations checker (same as CI):
  - `cd console && symfony composer translations-checker-install && symfony composer translations-checker-check`
  - `cd public && symfony composer translations-checker-install && symfony composer translations-checker-check`

## JavaScript (Console)

- Legacy coding style:
  - `cd console/assets/legacy && yarn install`
  - `yarn prettier --check js-stimulus ts-react --config .prettierrc.json`
- Projects coding style:
  - `cd console/assets/projects && yarn install`
  - `yarn prettier --check . --config .prettierrc.json`
- Legacy tests:
  - `cd console/assets/legacy && yarn install && yarn test`
- Builds (matches CI build jobs):
  - `cd console/assets/legacy && yarn install && yarn build`
  - `cd console/assets/projects && yarn install && yarn build`
  - Optional (release parity): `cd console/assets/modern && yarn install && yarn build`

## JavaScript (Public)

For release parity: `cd public && yarn install && yarn build`.

## Console: Database, Fixtures, Search, Tests

Run everything with Symfony CLI and align to CI behavior. Use truncate purge for idempotency locally.

```
cd console

# Assets fixtures used by functional tests
cp -R tests/Fixtures/assets/* public/

# Prepare database (test env)
symfony console doctrine:migrations:migrate -n -e test
symfony console doctrine:fixtures:load -n --group test --purge-with-truncate -e test

# Prepare Meilisearch index used by tests
symfony console app:search:index-crm

# PHPUnit (match CI default group: transactions enabled)
symfony php bin/phpunit --exclude-group without-transaction

# Optional: also run transactions-disabled group (CI runs it in a second job)
# Careful: when running this, the database will be altered (without reset) and 
# fixtures will need to be reloaded for tests to passe afterwares
# You don't need to use this command often, most likely if the transaction-enabled tests 
# pass this one will too
symfony php bin/phpunit --group without-transaction
```

## Public: Fixtures, Domains Cache, Tests

```
# Ensure PHP deps installed as above for console and public

# Copy test assets like CI
cp -R console/tests/Fixtures/assets/* console/public/
cp -R public/tests/Fixtures/assets public/public/build

# Prepare DB and domains cache
cd console
symfony console doctrine:migrations:migrate -n
symfony console doctrine:fixtures:load -n --group test --purge-with-truncate

# Run Symfony local web server for public tests
symfony server:ca:install
symfony serve -d

# Run Public tests
cd ../public
symfony php bin/phpunit
```

## GitHub Workflow Using `gh`

- Create or update your branch and push:
  - `git checkout -b my-branch` (if needed)
  - `git add -A && git commit -m "chore: update docs and cleanup"`
  - `git push -u origin my-branch`
- Open a PR (base: `main`):
  - `gh pr create --fill --base main --head my-branch`
- Check PR and CI status:
  - `gh pr status`
  - `gh pr checks --watch`

## Notes

- Always run PHP via Symfony CLI locally; use Docker only for infra services.
- Tests must not hit real external services; mock them. Console tests rely on `DAMA\DoctrineTestBundle` for DB isolation.
- Keep PHP PSR-12 and existing project conventions. Use provided `.php-cs-fixer.dist.php` configs.
- Brevo campaigns: sync marketing stats with `symfony console app:community:sync-brevo-campaigns-reports "YYYY-MM-DD"` when needed.
- Public liveness/readiness: GET `/health` on the public app returns `OK` with HTTP 200 (text/plain) and bypasses domain resolution. Use this for Kubernetes probes without needing domain token cache.
