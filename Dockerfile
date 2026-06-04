FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction

RUN mkdir -p /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/database

RUN echo "DocumentRoot /var/www/html/public" > /etc/apache2/sites-available/000-default.conf \
    && echo "<Directory /var/www/html/public>" >> /etc/apache2/sites-available/000-default.conf \
    && echo "    AllowOverride All" >> /etc/apache2/sites-available/000-default.conf \
    && echo "    Require all granted" >> /etc/apache2/sites-available/000-default.conf \
    && echo "</Directory>" >> /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

EXPOSE 80
