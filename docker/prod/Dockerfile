# ─────────────────────────────────────────────────────────────────────────────
# Stage 1 — Wayfinder: gera os tipos TypeScript das rotas PHP
# ─────────────────────────────────────────────────────────────────────────────
FROM composer:2 AS wayfinder

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

COPY . .

RUN mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && APP_KEY=base64:aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa= \
       php artisan wayfinder:generate --with-form

# ─────────────────────────────────────────────────────────────────────────────
# Stage 2 — Frontend: compila os assets com Vite
# ─────────────────────────────────────────────────────────────────────────────
FROM node:22-bookworm-slim AS frontend

ARG DOCKER_BUILD=1
ENV DOCKER_BUILD=1

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

# Copia os arquivos gerados pelo wayfinder (gitignored)
COPY --from=wayfinder /app/resources/js/routes   ./resources/js/routes
COPY --from=wayfinder /app/resources/js/actions  ./resources/js/actions
COPY --from=wayfinder /app/resources/js/wayfinder ./resources/js/wayfinder

RUN npm run build

# ─────────────────────────────────────────────────────────────────────────────
# Stage 3 — Vendor: instala dependências PHP via Composer
# ─────────────────────────────────────────────────────────────────────────────
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

# ─────────────────────────────────────────────────────────────────────────────
# Stage 4 — Runtime: imagem final com Nginx + PHP-FPM + Supervisor
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS runtime

# Dependências de sistema
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libpng \
        libjpeg-turbo \
        libzip \
        oniguruma \
        icu-libs \
    && apk add --no-cache --virtual .build-deps \
        libpng-dev \
        libjpeg-turbo-dev \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        opcache \
        pcntl \
        bcmath \
        intl \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

# Configurações PHP
COPY docker/prod/php.ini /usr/local/etc/php/conf.d/zacash.ini

# Configuração Nginx (http.d sobrescreve o default do Alpine)
COPY docker/prod/nginx.conf /etc/nginx/http.d/default.conf

# Configuração Supervisor
COPY docker/prod/supervisord.conf /etc/supervisord.conf

WORKDIR /var/www/html

# Copia a aplicação
COPY --chown=www-data:www-data . .

# Copia vendor (stage 3) e assets compilados (stage 2)
COPY --from=vendor  --chown=www-data:www-data /app/vendor      ./vendor
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

# Garante estrutura de diretórios com permissões corretas
RUN mkdir -p \
        storage/logs \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint
COPY docker/prod/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
