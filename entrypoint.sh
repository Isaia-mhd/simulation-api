#!/bin/sh

# Démarre PHP-FPM en arrière-plan
php-fpm &

# Démarre Nginx en mode non daemon (au premier plan)
nginx -g "daemon off;"
