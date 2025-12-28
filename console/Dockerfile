FROM ghcr.io/openaction/docker-php/prod:8.4

LABEL org.opencontainers.image.source=https://github.com/openaction/openaction

COPY . /app

ENV APP_ENV=prod \
    APP_DEBUG=0 \
    DATABASE_URL="pgsql://openaction:openaction@db/openaction?server_version=12" \
    MAILER_DSN="smtp://localhost"

RUN mkdir var && \
    composer install --prefer-dist --no-interaction --no-ansi --no-autoloader --no-scripts --no-progress && \
    composer clear-cache && \
    composer dump-autoload --optimize --classmap-authoritative && \
    composer dump-env prod && \
    bin/console cache:clear --no-warmup && \
    bin/console cache:warmup && \
    bin/console assets:install && \
    chmod -R 777 var && \
    chmod -R a+x /app/cron && \
    printf "opcache.preload=/app/config/preload.php\nopcache.preload_user=root\n" > $PHP_INI_DIR/conf.d/preload.ini
