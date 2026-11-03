FROM php:8.5-fpm AS base

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libicu-dev libpq-dev \
    && docker-php-ext-install -j"$(nproc)" intl pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# php-fpm workers run under the host user so files created in bind mounts are not root-owned.
RUN groupmod -o -g "${GID}" www-data && usermod -o -u "${UID}" -g "${GID}" www-data

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/uploads.ini $PHP_INI_DIR/conf.d/zz-uploads.ini
ENV LC_ALL=C.UTF-8

WORKDIR /app

FROM base AS dev

RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/php/xdebug.ini $PHP_INI_DIR/conf.d/zz-xdebug.ini

# Production: the code is baked into the image (no bind mount), prod dependencies only, opcache on.
FROM base AS prod

ENV APP_ENV=prod \
    APP_DEBUG=false

COPY docker/php/prod.ini $PHP_INI_DIR/conf.d/zz-prod.ini
COPY backend/ /app/
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --classmap-authoritative \
    && composer clear-cache \
    && mkdir -p /app/runtime/cache /app/runtime/logs /app/storage/uploads \
    && chown -R www-data:www-data /app/runtime /app/storage
