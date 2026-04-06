FROM php:8.3-fpm-alpine

# ── System dependencies ──────────────────────────────────────────────────────
RUN apk add --no-cache \
    nginx \
    icu-libs \
    libzip \
    libpng \
    freetype \
    libjpeg-turbo \
    oniguruma \
    libxml2 \
    postgresql-libs \
    mariadb-connector-c \
    && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    icu-dev \
    libzip-dev \
    libpng-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    mariadb-connector-c-dev

# ── PHP extensions ───────────────────────────────────────────────────────────
RUN docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    pdo_pgsql \
    pgsql \
    mysqli \
    pdo_mysql \
    gd \
    intl \
    mbstring \
    zip \
    opcache

RUN pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# ── PHP production config ─────────────────────────────────────────────────────
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php-opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# ── Nginx ────────────────────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# ── Composer ─────────────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    && rm -rf /root/.composer

COPY . .

# ── Permissions ──────────────────────────────────────────────────────────────
RUN mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar /run/nginx \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/writable

# ── Entrypoint ────────────────────────────────────────────────────────────────
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
