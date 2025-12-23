#!/bin/sh

docker compose up -d

cd console
yarn install
yarn build

cd projects
yarn install
yarn build

cd ../../public
yarn install
yarn build

# Install dependencies
docker compose exec console composer install
docker compose exec public composer install

# Populate database
docker compose exec console bin/console d:d:d --force -n
docker compose exec console bin/console d:d:c -n
docker compose exec console bin/console d:m:m -n
docker compose exec console bin/console d:f:l --group test -n

# Populate the search engine index
docker compose exec console bin/console app:search:index-crm -n

# Refresh the domains cache (stored in PostgreSQL) for public to work
docker compose exec console bin/console app:proxy:refresh-domains-cache -n
