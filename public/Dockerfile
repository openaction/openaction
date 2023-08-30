FROM ghcr.io/citipo/docker-php/docker-php-prod:latest

COPY . /app

ENV APP_ENV=prod \
    APP_DEBUG=0

RUN mkdir var && \
    composer install --prefer-dist --no-interaction --no-ansi --no-autoloader --no-scripts --no-progress && \
    composer clear-cache && \
    composer dump-autoload --optimize --classmap-authoritative && \
    bin/console cache:clear --no-warmup && \
    bin/console cache:warmup && \
    bin/console assets:install && \
    chmod -R 777 var && \
    printf "opcache.preload=/app/config/preload.php\nopcache.preload_user=root\n" > $PHP_INI_DIR/conf.d/preload.ini
