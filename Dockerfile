FROM php:8.3-fpm

   # Installieren Sie Abhängigkeiten
   RUN apt-get update && apt-get install -y \
       git \
       curl \
       libpng-dev \
       libonig-dev \
       libxml2-dev \
       nodejs \
       zip \
       unzip

   # PHP-Erweiterungen installieren
   RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

   # Composer installieren
   COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

   # Arbeitsverzeichnis festlegen
   WORKDIR /var/www/html

   # Projekt-Dateien kopieren
   COPY . /var/www/html

   # Composer-Abhängigkeiten installieren
   RUN composer install

   # Berechtigungen anpassen
   RUN chown -R www-data:www-data /var/www/html/storage

   # Port freigeben
   EXPOSE 8000

   # Server starten
   CMD php artisan serve --host=0.0.0.0 --port=8000
