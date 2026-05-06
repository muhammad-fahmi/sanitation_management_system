#!/bin/sh
set -e

echo "==> Writing .env file from environment variables..."
cat > /var/www/html/.env << EOF
CI_ENVIRONMENT = ${CI_ENVIRONMENT:-production}

app.baseURL = ${APP_BASE_URL:-http://localhost/}
cookie.domain = ${COOKIE_DOMAIN:-}

database.default.hostname = ${DB_HOST:-db}
database.default.database = ${DB_NAME:-bionic_db}
database.default.username = ${DB_USER:-bionic_user}
database.default.password = ${DB_PASS:-secret}
database.default.DBDriver = ${DB_DRIVER:-Postgre}
database.default.DBPrefix =
database.default.port = ${DB_PORT:-5432}

session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
session.savePath = /var/www/html/writable/session
EOF

echo "==> Preparing writable directories..."
mkdir -p \
    /var/www/html/writable/cache \
    /var/www/html/writable/logs \
    /var/www/html/writable/session \
    /var/www/html/writable/uploads \
    /var/www/html/writable/uploads/revisions \
    /var/www/html/writable/debugbar
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

echo "==> Preparing public upload directories..."
mkdir -p \
    /var/www/html/public/uploads/revisions
chown -R www-data:www-data /var/www/html/public/uploads
chmod -R 775 /var/www/html/public/uploads

echo "==> Migrating legacy uploads to writable volume (if any)..."
if [ -d /var/www/html/public/uploads/revisions ]; then
    cp -an /var/www/html/public/uploads/revisions/. /var/www/html/writable/uploads/revisions/ || true
fi

echo "==> Clearing persisted framework caches..."
rm -f /var/www/html/writable/cache/FactoriesCache_* \
    /var/www/html/writable/cache/FileLocatorCache

echo "==> Waiting for database at ${DB_HOST:-db}:${DB_PORT:-5432}..."
MAX_TRIES=30
COUNT=0
until php -r "
\$driver = '${DB_DRIVER:-Postgre}';
\$host   = '${DB_HOST:-db}';
\$port   = '${DB_PORT:-5432}';
\$db     = '${DB_NAME:-bionic_db}';
\$user   = '${DB_USER:-bionic_user}';
\$pass   = '${DB_PASS:-secret}';
if (stripos(\$driver, 'postgre') !== false) {
    \$dsn = \"host=\$host port=\$port dbname=\$db user=\$user password=\$pass\";
    \$c = @pg_connect(\$dsn);
    if (\$c) { pg_close(\$c); exit(0); }
} else {
    \$c = @new mysqli(\$host, \$user, \$pass, \$db, (int)\$port);
    if (!\$c->connect_error) { \$c->close(); exit(0); }
}
exit(1);
" 2>/dev/null; do
    COUNT=$((COUNT+1))
    if [ "$COUNT" -ge "$MAX_TRIES" ]; then
        echo "ERROR: Database did not become ready in time."
        exit 1
    fi
    echo "   Database not ready yet ($COUNT/$MAX_TRIES), retrying in 3s..."
    sleep 3
done
echo "   Database is ready."

echo "==> Running migrations..."
php /var/www/html/spark migrate --all -f

AUTO_SEED="${AUTO_SEED:-true}"
SEED_FORCE="${SEED_FORCE:-false}"
SEEDER_CLASS="${SEEDER_CLASS:-MainSeeder}"

if [ "$AUTO_SEED" = "true" ]; then
    echo "==> Checking whether seeding is required..."

    SHOULD_SEED=0
    if [ "$SEED_FORCE" = "true" ]; then
        SHOULD_SEED=1
        echo "   Seed force is enabled; seeding will run."
    else
        USER_COUNT=$(php -r "
\$driver = '${DB_DRIVER:-Postgre}';
\$host   = '${DB_HOST:-db}';
\$port   = '${DB_PORT:-5432}';
\$db     = '${DB_NAME:-bionic_db}';
\$user   = '${DB_USER:-bionic_user}';
\$pass   = '${DB_PASS:-secret}';
if (stripos(\$driver, 'postgre') !== false) {
    \$dsn = \"host=\$host port=\$port dbname=\$db user=\$user password=\$pass\";
    \$c = @pg_connect(\$dsn);
    if (!\$c) { echo '0'; exit(0); }
    \$res = @pg_query(\$c, 'SELECT COUNT(*) AS total FROM m_users');
    if (!\$res) { pg_close(\$c); echo '0'; exit(0); }
    \$row = pg_fetch_assoc(\$res);
    pg_close(\$c);
    echo (string) ((int) (\$row['total'] ?? 0));
} else {
    \$c = @new mysqli(\$host, \$user, \$pass, \$db, (int)\$port);
    if (\$c->connect_error) { echo '0'; exit(0); }
    \$res = @\$c->query('SELECT COUNT(*) AS total FROM m_users');
    if (!\$res) { \$c->close(); echo '0'; exit(0); }
    \$row = \$res->fetch_assoc();
    \$c->close();
    echo (string) ((int) (\$row['total'] ?? 0));
}
" 2>/dev/null)

        if [ "${USER_COUNT:-0}" -eq 0 ]; then
            SHOULD_SEED=1
            echo "   Database appears empty; seeding will run."
        else
            echo "   Database already has data; skipping seed."
        fi
    fi

    if [ "$SHOULD_SEED" -eq 1 ]; then
        echo "==> Running seeder ${SEEDER_CLASS}..."
        php /var/www/html/spark db:seed "$SEEDER_CLASS" -f
    fi
else
    echo "==> AUTO_SEED is disabled; skipping seed."
fi

echo "==> Ensuring Apache vhost config is valid..."
cat > /etc/apache2/sites-available/000-default.conf << 'EOF'
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    ServerName localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

apache2ctl -t

echo "==> Starting Apache..."
exec apache2-foreground
