FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    sqlite3 \
    libsqlite3-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_sqlite
RUN docker-php-ext-install pdo_pgsql pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Create .env file
RUN if [ ! -f .env ]; then \
    echo "APP_NAME=Franklin_Agent" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=https://backend_ai-beaker.onrender.com" >> .env && \
    echo "APP_KEY=" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "SESSION_DRIVER=database" >> .env && \
    echo "CACHE_DRIVER=database" >> .env; \
    fi

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Create storage directories
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs
RUN chmod -R 777 storage bootstrap/cache

# Generate app key
RUN php artisan key:generate || true

# Cache configurations
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Run migrations
RUN php artisan migrate --force || true

# Nginx configuration
RUN echo 'server { \
    listen 0.0.0.0:8080; \
    server_name _; \
    root /var/www/html/public; \
    index index.php; \
    add_header X-Frame-Options "SAMEORIGIN"; \
    add_header X-Content-Type-Options "nosniff"; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
    location ~ /\.(?!well-known).* { \
        deny all; \
    } \
}' > /etc/nginx/sites-available/default

# Supervisor configuration
RUN echo '[supervisord] \n\
nodaemon=true \n\
user=root \n\
\n\
[program:php-fpm] \n\
command=php-fpm -F \n\
autostart=true \n\
autorestart=true \n\
\n\
[program:nginx] \n\
command=nginx -g "daemon off;" \n\
autostart=true \n\
autorestart=true' > /etc/supervisor/conf.d/laravel.conf

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]