FROM ghcr.io/openaction/docker-php/prod:8.4

LABEL org.opencontainers.image.source=https://github.com/openaction/openaction

COPY . /app

ENV APP_ENV=prod \
    APP_DEBUG=0 \
    DATABASE_URL="pgsql://openaction:openaction@db/openaction?server_version=12" \
    MAILER_DSN="smtp://localhost"

ARG RUNITOR_VERSION=v1.4.1-build.5
ARG TARGETARCH

RUN set -eux; \
    if ! command -v curl >/dev/null 2>&1 && ! command -v wget >/dev/null 2>&1; then \
        if command -v apt-get >/dev/null 2>&1; then \
            apt-get update; \
            apt-get install -y --no-install-recommends curl; \
            rm -rf /var/lib/apt/lists/*; \
        elif command -v apk >/dev/null 2>&1; then \
            apk add --no-cache curl; \
        else \
            echo "runitor install failed: no curl/wget and no supported package manager found" >&2; \
            exit 1; \
        fi; \
    fi; \
    case "${TARGETARCH:-}" in \
        amd64) runitor_arch="amd64" ;; \
        arm64) runitor_arch="arm64" ;; \
        *) echo "runitor install failed: unsupported TARGETARCH '${TARGETARCH:-}'" >&2; exit 1 ;; \
    esac; \
    runitor_asset="runitor-${RUNITOR_VERSION}-linux-${runitor_arch}"; \
    runitor_url="https://github.com/bdd/runitor/releases/download/${RUNITOR_VERSION}/${runitor_asset}"; \
    if command -v curl >/dev/null 2>&1; then \
        curl -fsSL "$runitor_url" -o /usr/local/bin/runitor; \
    else \
        wget -qO /usr/local/bin/runitor "$runitor_url"; \
    fi; \
    chmod +x /usr/local/bin/runitor; \
    /usr/local/bin/runitor -version

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
