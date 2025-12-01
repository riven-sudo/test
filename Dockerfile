# Use official PHP + Apache image
FROM php:8.2-apache

# Install system dependencies for PHP extensions
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libpq-dev \
    libzip-dev \
    unzip

# Install PHP extensions (mysqli, pdo, pdo_mysql, pgsql optional)
RUN docker-php-ext-install mysqli pdo pdo_mysql pgsql

# Enable Apache rewrite
RUN a2enmod rewrite

# Copy app files
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html/

EXPOSE 80
