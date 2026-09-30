#!/bin/sh
set -e

echo "──────────────────────────────────────────"
echo "  Ranklio — iniciando container"
echo "──────────────────────────────────────────"

# ── Validação de variáveis obrigatórias ───────────────────────────────────────
MISSING=""
[ -z "$APP_KEY" ]    && MISSING="$MISSING APP_KEY"
[ -z "$DB_HOST" ]    && MISSING="$MISSING DB_HOST"
[ -z "$DB_DATABASE" ] && MISSING="$MISSING DB_DATABASE"
[ -z "$DB_USERNAME" ] && MISSING="$MISSING DB_USERNAME"

if [ -n "$MISSING" ]; then
    echo "ERRO: variáveis não definidas:$MISSING"
    echo "Configure no EasyPanel → Environment."
    exit 1
fi

# ── Aguarda MySQL ficar disponível ────────────────────────────────────────────
DB_PORT="${DB_PORT:-3306}"
echo "→ Aguardando MySQL em $DB_HOST:$DB_PORT..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD:-}');" 2>/dev/null; do
    sleep 2
done
echo "→ MySQL disponível."

# ── Permissões (seguro para volumes montados em runtime) ──────────────────────
mkdir -p \
    /var/www/html/storage/logs \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/bootstrap/cache

chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# ── Migrations ────────────────────────────────────────────────────────────────
echo "→ Rodando migrations..."
php /var/www/html/artisan migrate --force --no-interaction

# ── Cache de config, rotas e views ────────────────────────────────────────────
echo "→ Gerando caches de produção..."
php /var/www/html/artisan optimize --no-interaction

echo "→ Serviços iniciando..."
echo "──────────────────────────────────────────"

exec /usr/bin/supervisord -c /etc/supervisord.conf
