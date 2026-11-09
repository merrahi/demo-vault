FROM php:8.2-apache

# Installation de pdo_mysql et curl
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql

COPY index.php /var/www/html/