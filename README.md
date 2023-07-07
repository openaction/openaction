# OpenAction

OpenAction is a platform to organize and grow communities organized in federations.

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

### 1. Copy the docker-compose.override.yaml.dist file (optional)

If you want to access Console and Public using your host native network,
you can copy the docker-compose.override.yaml.dist file which associate
various ports to your hosts machine, helping you get started quickly.

You can also use a local reverse proxy if you wish.

### 2. Start the project and its services

```
$ cd path/to/OpenAction

# Login to the GitHub Docker registry to get the required images
$ docker login docker.pkg.github.com

# Start Docker services
$ docker-compose up -d

# Build Console assets (Yarn is outside of the containers to ease usage)
$ cd console
$ yarn install
$ yarn build
$ cd projects (console/projects)
$ yarn install && yarn build
```

The project uses several services:

* PostgreSQL as its database engine;
* Redis as its cache and key-value storage engine;
* RabbitMQ as its queuing mechanism (with Symfony Messenger);

### 3. Configure bash/zsh aliases (optional)

You can configure aliases to ease development using this setup (in your `.bashrc` file):

```
alias dc='docker-compose'
alias dconsole='dc exec console'
alias dstandard='dc exec standard'
```

This way, to run a command inside the Console container is much easier:

```
dconsole bin/console make:controller 
```

### 4. API

The API is based on [fractal.thephpleague.com](https://fractal.thephpleague.com).

## Technical considerations and choices

Some technical conventions:

* Always use "private" entities: entities should not expose any setter and should only use
  private properties. They should always be mutated using well-named methods. The only exception
  is when the entity is meant to be edited only by the admin panel, in which case it's easier to
  use getters and setters.
 
