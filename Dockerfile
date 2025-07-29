FROM fideloper/fly-laravel:8.2

WORKDIR /var/www/html

# Copier tout le projet
COPY . .

# Installer psmisc (optionnel, si tu en as besoin)
RUN apt-get update && apt-get install -y psmisc && rm -rf /var/lib/apt/lists/*

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --no-dev

# Droits sur les dossiers nécessaires
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copier la config nginx personnalisée
COPY nginx-socket.conf /etc/nginx/nginx.conf

# Copier le script d’entrée et le rendre exécutable
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Exposer le port HTTP
EXPOSE 8080

# Lancer l’entrypoint qui démarre PHP-FPM et nginx
ENTRYPOINT ["/entrypoint.sh"]
