FROM php:8.3-apache-bookworm

# ── System dependencies ──────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    libpq-dev libicu-dev libzip-dev \
    libpng-dev libfreetype6-dev libjpeg62-turbo-dev \
    libonig-dev libxml2-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    pdo_pgsql pgsql mysqli pdo_mysql gd \
    intl mbstring zip opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── PHP production config ─────────────────────────────────────────────────────
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php-opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# ── Apache ───────────────────────────────────────────────────────────────────
RUN a2enmod rewrite
RUN echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

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
    --no-scripts \
    && rm -rf /root/.composer

COPY --chown=www-data:www-data . .

# ── Permissions ──────────────────────────────────────────────────────────────
RUN mkdir -p writable/cache writable/logs writable/temp writable/uploads \
    && chown -R www-data:www-data writable/

# ── Entrypoint ────────────────────────────────────────────────────────────────
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
