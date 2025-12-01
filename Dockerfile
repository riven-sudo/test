# Use official PHP + Apache image
FROM php:8.2-apache

# Install mysqli, pdo, and pdo_mysql
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy app files
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html/

EXPOSE 80

RUN apt-get update && apt-get install -y php-pgsql
