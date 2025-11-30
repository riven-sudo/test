# Use official PHP + Apache image
FROM php:8.2-apache

# Install PostgreSQL driver dependencies
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Enable Apache rewrite module
RUN a2enmod rewrite

# 🚀 FIX: Pass Render environment vars to PHP
RUN echo "PassEnv DB_HOST\n\
PassEnv DB_USER\n\
PassEnv DB_PASSWORD\n\
PassEnv DB_DATABASE" \
>> /etc/apache2/conf-available/passenv.conf \
    && a2enconf passenv

# Copy app files
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html/

EXPOSE 80
