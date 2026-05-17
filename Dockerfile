FROM php:8.0-apache

# Install MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module (useful for clean URLs)
RUN a2enmod rewrite

COPY . /var/www/html/