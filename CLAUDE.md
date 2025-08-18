# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Development Commands

### Console (Backend Platform)

```bash
# Install PHP dependencies
docker-compose exec console composer install

# Database operations
docker-compose exec console bin/console d:d:d --force  # Drop database
docker-compose exec console bin/console d:d:c         # Create database
docker-compose exec console bin/console d:m:m -n      # Run migrations
docker-compose exec console bin/console d:f:l --group test  # Load fixtures

# Search engine operations
docker-compose exec console bin/console app:search:index-crm
docker-compose exec console bin/console app:search:index-cms

# Domain/proxy cache
docker-compose exec console bin/console app:proxy:refresh-domains-cache -n

# Run tests
docker-compose exec console bin/phpunit
docker-compose exec console bin/phpunit --filter <TestName>

# Generate new controllers/entities
docker-compose exec console bin/console make:controller
```

### Asset Building (Multiple Frontend Stacks)

```bash
# Console modern assets (React/TypeScript)
cd console/assets/modern
yarn install
yarn dev        # Development build
yarn build      # Production build

# Console legacy assets (React/JavaScript)  
cd console/assets/legacy
yarn install
yarn dev
yarn build

# Console project assets (Stimulus/Sass)
cd console/assets/projects
yarn install
yarn dev
yarn build

# Public assets (Stimulus/Bootstrap)
cd public
yarn install
yarn dev
yarn build
```

### Development Environment Setup

```bash
# Start Docker services
docker-compose up -d

# Install PHP dependencies for both applications
docker-compose exec console composer install
docker-compose exec public composer install

# Add to /etc/hosts for local development
127.0.0.1 console
```

## Architecture Overview

OpenAction is a SaaS platform for organizing online communities with two main applications:

### Console (Backend Platform)
- **Location**: `console/` directory
- **Purpose**: Backend platform for content creation, community management, and administration
- **Stack**: PHP 8.1 + Symfony 6.2
- **Frontend**: Multiple asset stacks:
  - Modern: TypeScript/React/Stimulus/BlueprintJS (`console/assets/modern/`)
  - Legacy: JavaScript/React/Stimulus/Bootstrap (`console/assets/legacy/`)
  - Projects: Stimulus/Sass (`console/assets/projects/`)

### Public (Frontend Application)
- **Location**: `public/` directory  
- **Purpose**: Multi-tenant frontend serving websites for different projects/organizations
- **Stack**: PHP 8.1 + Symfony 6.2 + Stimulus/Bootstrap
- **Role**: Renders HTML/CSS for projects using Console's API

### Key Components

#### APIs
- **External API**: `console/src/Controller/Api/` - Used by partners/external integrations
- **Internal API**: `console/src/Controller/Console/` - Used by OpenAction frontends
- **Public API**: `public/src/Controller/ApiController.php` - Serves public website data

#### Search & CRM
- **Search Engine**: Meilisearch integration for contact/content search
- **Indexing**: `console/src/Search/` - CRM and CMS indexing systems
- **CRM**: Contact management with React components shared between Organization and Project views

#### Infrastructure
- **CDN**: Content delivery through Cloudflare (`console/src/Cdn/`)
- **Proxy**: Domain synchronization with Cloudflare (`console/src/Proxy/`)
- **Messaging**: RabbitMQ with Symfony Messenger (`console/src/Consumer/`)

## Development Guidelines

### Backend Development
- Use PHP 8.1+ features and Symfony 6.2 conventions
- Entities are in `console/src/Entity/` with proper Doctrine mapping
- Repositories use `console/src/Repository/` with custom DQL functions in `DQL/`
- Forms are in `console/src/Form/` with proper validation

### Frontend Development
- **React Components**: Follow existing patterns in asset directories
- **Stimulus Controllers**: Use for progressive enhancement
- **API Integration**: Always use internal API endpoints, not external API
- **Styling**: Follow Bootstrap conventions for legacy, BlueprintJS for modern

### Database & Migrations
- Use Doctrine migrations in `console/migrations/`
- Test data fixtures are in `console/src/DataFixtures/`
- Multiple database schemas for analytics, community data, etc.

### Testing
- PHPUnit tests in `console/tests/` and `public/tests/`
- Use `dama/doctrine-test-bundle` for database test isolation
- API tests should extend `ApiTestCase`
- Web tests should extend `WebTestCase`

### Integration Services
External service integrations in `console/src/Bridge/`:
- Cloudflare, Mailchimp, Postmark, Sendgrid, Twilio
- All have interface abstractions and mock implementations for testing

### Permissions & Security  
- Multi-tenant architecture with organization and project-level permissions
- User roles: Admin, Organization Member, Project Member
- CSRF protection and 2FA support built-in
- Security voters in `console/src/Security/Voter/`

## Important Notes

- **Never modify external API** unless explicitly requested - always use internal API endpoints
- **Multi-asset stack**: Different parts of the application use different frontend technologies
- **Docker-based development**: All commands should be run through docker-compose
- **Multi-tenant**: Code must handle multiple organizations/projects correctly
- **Search indexing**: Changes to entities may require reindexing CRM/CMS data