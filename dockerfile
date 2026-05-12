FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev \
    libpq-dev nginx supervisor \
    sqlite3 libsqlite3-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_sqlite pdo_pgsql pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Storage permissions (IMPORTANT FIX)
RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs

RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Clear cached config (prevents broken boot)
RUN php artisan config:clear || true
RUN php artisan cache:clear || true
RUN php artisan route:clear || true
RUN php artisan view:clear || true
# ===== AUTO FIX ENTRYPOINT (RUNS MIGRATIONS ON START) =====
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'echo "Starting Laravel setup..."' >> /start.sh && \
    echo 'php artisan config:clear' >> /start.sh && \
    echo 'php artisan migrate --force || true' >> /start.sh && \
    echo 'php-fpm -D' >> /start.sh && \
    echo 'nginx -g "daemon off;"' >> /start.sh && \
    chmod +x /start.sh

# Nginx config
RUN echo 'server { \
    listen 0.0.0.0:8080; \
    server_name _; \
    root /var/www/html/public; \
    index index.php; \
\
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
\
    location ~ \.php$ { \
        include fastcgi_params; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
    } \
\
    location ~ /\.(?!well-known).* { \
        deny all; \
    } \
}' > /etc/nginx/sites-available/default

EXPOSE 8080

CMD ["/start.sh"]