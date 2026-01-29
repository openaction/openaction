# OpenAction

OpenAction is a platform to organize and grow online communities.

## Architecture

The overall architecture of the OpenAction project is the following:

```
┌─────────────┐        ┌────────────┐        ┌─────────────┐
│             │        │            │        │             │
│             │        │   Public   │  API   │ Back-office │
│  Visitors   ├───────►│  websites  ├───────►│  (console)  │
│             │        │  (public)  │        │             │
│             │        │            │        │             │
└─────────────┘        └────────────┘        └─────────────┘
```

It consists in two main components:

* The **public** directory is a generic frontend: it receives the public HTTP
  requests, find the concerned project and render the page using the API of 
  the Console. Its role is mostly focused on rendering HTML/CSS for projects, 
  but it also needs to handle some forms/data collection.
* The **console** directory is the backend platform used by customers to create 
  content, organize their community, change their design, etc. It is the same for
  every project and its role is to build an API to be used by the frontend ;
  
The console also contains two related tools:

* The **CDN endpoint**, a tiny service providing content through Cloudflare 
  to act as a CDN ;
* The **Proxy**, a series of Symfony consumers synchronizing projects domains
  with Cloudflare for load balancing and SSL.
  
## Technical aspects

The project uses Symfony both for Console and Public, with a HTTP API in between.
It also uses StimulusJS and React when it's useful.

OpenAction uses Docker for infrastructure services only. PHP runs locally via
Symfony CLI for Console and Public.
  
## Local development (Symfony CLI)

### Prerequisites

- PHP 8.4 (see `console/.php-version` and `public/.php-version`) + Composer v2
- Symfony CLI (`symfony -v`)
- Node.js 22 + Yarn 4 (`corepack enable`)
- Docker (services only)

### 1. Start infrastructure services (required)

The local `.env` files point to `localhost`, so you must expose the service
ports from Docker:

```
cp compose.override.yaml.dist compose.override.yaml
docker compose up -d --quiet-pull
```

Services started by Docker:
* PostgreSQL (database)
* Redis (cache/sessions)
* RabbitMQ (Symfony Messenger)
* Meilisearch (search)
* Gotenberg (PDF rendering)
* Mailcatcher (local emails)

### 2. Install PHP dependencies (Symfony CLI)

```
cd console
symfony composer install --prefer-dist --no-interaction --no-ansi --no-progress

cd ../public
symfony composer install --prefer-dist --no-interaction --no-ansi --no-progress

cd ..
```

### 3. Install JS dependencies and build assets

Console assets:

```
cd console/assets/legacy
yarn install
yarn build

cd ../projects
yarn install
yarn build

# Optional (release parity / modern stack used in CI)
cd ../modern
yarn install
yarn build
```

Public assets:

```
cd public
yarn install
yarn build
```

### 4. Prepare database and search (Console)

```
cd console
symfony console doctrine:migrations:migrate -n

# Optional but recommended for local demo data
symfony console doctrine:fixtures:load -n --group test --purge-with-truncate

# Populate the search engine index
symfony console app:search:index-crm
```

### 5. Run locally

Install the local TLS CA once if you want trusted HTTPS:

```
symfony server:ca:install
```

Start Console and Public (ports are defined in `.symfony.local.yaml`):

```
cd console
symfony serve -d

cd ../public
symfony serve -d
```

By default, the apps are reachable at:

* Console: https://localhost:8000 (from `console/.env`)
* Public: https://localhost:8001 (from `public/.env`)

If you prefer HTTP, use `symfony serve --no-tls` and set matching URLs in
`.env.local` (for example `APP_CONSOLE_ENDPOINT=http://localhost:8000`).

### Default credentials (fixtures only)

If you loaded the `test` fixtures, you can log in to Console with:

```
Email: titouan.galopin@citipo.com
Password: password
```

For CI parity (translations, coding style, tests), follow `AGENTS.md`.
