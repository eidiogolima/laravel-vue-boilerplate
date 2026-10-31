FROM php:8.5.12-fpm-alpine AS base

RUN docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        pcntl \
        bcmath \
    && rm -rf /tmp/* /usr/local/lib/php/test /usr/local/php/man

WORKDIR /var/www/html

FROM base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

COPY artisan ./
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY routes ./routes

RUN composer dump-autoload --no-dev --optimize --classmap-authoritative \
    && find vendor \( -type d \( -name tests -o -name Tests -o -name docs \) -o -type f \( -name '*.md' -o -name '*.dist' -o -name 'phpunit*' \) \) -prune -exec rm -rf {} +

FROM vendor AS frontend

COPY package.json package-lock.json vite.config.ts tsconfig.json components.json ./
COPY public ./public
COPY resources ./resources
COPY lang ./lang

COPY --from=node:22-alpine /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-alpine /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN apk add --no-cache libstdc++ \
    && ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && npm ci \
    && mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= npm run build

FROM base AS runtime

RUN apk add --no-cache nginx supervisor

COPY docker/prod/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/prod/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/prod/supervisord.conf /etc/supervisord.conf

COPY --from=vendor --chown=www-data:www-data /var/www/html/vendor ./vendor
COPY --chown=www-data:www-data artisan composer.json ./
COPY --chown=www-data:www-data app ./app
COPY --chown=www-data:www-data bootstrap ./bootstrap
COPY --chown=www-data:www-data config ./config
COPY --chown=www-data:www-data database ./database
COPY --chown=www-data:www-data lang ./lang
COPY --chown=www-data:www-data public ./public
COPY --chown=www-data:www-data routes ./routes
COPY --chown=www-data:www-data resources/views ./resources/views
COPY --chown=www-data:www-data storage ./storage
COPY --from=frontend --chown=www-data:www-data /var/www/html/public/build ./public/build

RUN mkdir -p \
        storage/logs \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/prod/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
