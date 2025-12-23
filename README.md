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

OpenAction uses Docker both in development and production.
  
## Setup the development environment

To setup the development environment, you need [Docker](https://docs.docker.com/get-docker/) 
and [Yarn](https://classic.yarnpkg.com/en/docs/install).

### 1. Copy the docker compose.override.yaml.dist file (optional)

If you want to access Console and Public using your host native network,
you can copy the docker compose.override.yaml.dist file which associate
various ports to your hosts machine, helping you get started quickly.

You can also use a local reverse proxy if you wish.

### 2. Start the project and its services

```
cd path/to/openaction

# Start Docker services
docker compose up -d

# Build assets (Yarn is outside of the containers to ease usage, you should use node 14)
cd console
yarn install
yarn build

cd projects (console/projects)
yarn install
yarn build

cd public
yarn install
yarn build

# Install PHP dependencies
docker compose exec console composer install
docker compose exec public composer install

# Update /etc/hosts to add console as localhost
127.0.0.1 console
```

The project uses several services:
* PostgreSQL as its database engine;
* Redis as its cache and session storage engine;
* Symfony Messenger queues (RabbitMQ by default; other backends via `docs/messenger-transports.md`);

### 3. Prepare the local database

```
docker compose exec console bin/console d:d:d --force
docker compose exec console bin/console d:d:c
docker compose exec console bin/console d:m:m -n
docker compose exec console bin/console d:f:l --group test

# Populate the search engine index
docker compose exec console bin/console app:search:index-crm
```

Once done, you can access the Console on the container console
(for example, http://localhost if you use the default docker compose.override.yaml file).

You can also access the default public website on the container public (http://localhost:9000).

The default Console username/password is:

```
titouan.galopin@citipo.com
password
```

### 3. Configure bash/zsh aliases (optional)

You can configure aliases to ease development using this setup (in your `.bashrc` file):

```
alias dc='docker compose'
alias dconsole='dc exec console'
alias dstandard='dc exec standard'
```

This way, to run a command inside the Console container is easier:

```
dconsole bin/console make:controller 
```
