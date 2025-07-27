# syntax=docker/dockerfile:1.4
ARG PHP_VERSION=8.3
FROM flyio/php:8.3-fpm-alpine

LABEL fly_launch_runtime="laravel"

WORKDIR /var/www/html

# Install Composer and other PHP extensions needed by your Laravel app
# Add any missing extensions if your app fails later
RUN install-php-extensions \
    zip \
    pdo_mysql \
    bcmath \
    gd \
    exif \
    mbstring \
    pcntl \
    intl

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
