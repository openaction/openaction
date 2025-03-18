FROM ghcr.io/openaction/docker-php/prod

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
    printf "opcache.preload=/app/config/preload.php\nopcache.preload_user=root\n" > $PHP_INI_DIR/conf.d/preload.ini

COPY cron/check-domains /etc/periodic/15min/
COPY cron/refresh-domains-cache /etc/periodic/1min/
COPY cron/admin-stats-refresh /etc/periodic/15min/
COPY cron/contacts-stats-refresh /etc/periodic/daily/
COPY cron/index-crm /etc/periodic/daily/
COPY cron/index-cms /etc/periodic/daily/
COPY cron/contacts-resolve-ambiguities /etc/periodic/hourly/
COPY cron/integration-revue /etc/periodic/15min/
COPY cron/synchronize-customers /etc/periodic/hourly/
COPY cron/website-sessions-build /etc/periodic/15min/
COPY cron/website-sessions-clear /etc/periodic/daily/
COPY cron/synchronize-24h-mailchimp-campaigns-reports /etc/periodic/1min/
COPY cron/synchronize-1w-mailchimp-campaigns-reports /etc/periodic/hourly/
COPY cron/synchronize-6m-mailchimp-campaigns-reports /etc/periodic/daily/

RUN chmod a+x /etc/periodic/15min/check-domains && \
    chmod a+x /etc/periodic/1min/refresh-domains-cache && \
    chmod a+x /etc/periodic/15min/admin-stats-refresh && \
    chmod a+x /etc/periodic/daily/contacts-stats-refresh && \
    chmod a+x /etc/periodic/daily/index-crm && \
    chmod a+x /etc/periodic/daily/index-cms && \
    chmod a+x /etc/periodic/hourly/contacts-resolve-ambiguities && \
    chmod a+x /etc/periodic/15min/integration-revue && \
    chmod a+x /etc/periodic/hourly/synchronize-customers && \
    chmod a+x /etc/periodic/15min/website-sessions-build && \
    chmod a+x /etc/periodic/daily/website-sessions-clear && \
    chmod a+x /etc/periodic/1min/synchronize-24h-mailchimp-campaigns-reports && \
    chmod a+x /etc/periodic/hourly/synchronize-1w-mailchimp-campaigns-reports && \
    chmod a+x /etc/periodic/daily/synchronize-6m-mailchimp-campaigns-reports
