# syntax=docker/dockerfile:1.4
ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-fpm-alpine

LABEL fly_launch_runtime="laravel"

WORKDIR /var/www/html

# Install Composer and other PHP extensions needed by your Laravel app
# Add any missing extensions if your app fails later
# Install PHP extensions using apk
RUN apk add --no-cache \
    php83-zip \
    php83-pdo_mysql \
    php83-bcmath \
    php83-gd \
    php83-exif \
    php83-mbstring \
    php83-pcntl \
    php83-intl \
    # Add any other required extensions. Common ones:
    php83-dom \
    php83-xml \
    php83-ctype \
    php83-session \
    php83-fileinfo \
    php83-tokenizer \
    php83-opcache \
    php83-curl \
    php83-openssl \
    php83-json \
    php83-xmlreader \
    php83-phar

# Set up Node.js for frontend assets (if you use Vite/NPM/Yarn)
ARG NODE_VERSION=18
RUN apk add --no-cache nodejs npm

COPY . .

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets if package.json exists and build script is defined
# Adjust 'npm run build' if your package.json uses a different script (e.g., 'vite build')
RUN if [ -f "package.json" ]; then npm install && npm run build; fi

# Optimize Laravel
RUN php artisan optimize

# Make storage directory writable
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 8080 for Nginx
EXPOSE 8080

# Command to run Nginx and PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
