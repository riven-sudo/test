# Use official PHP + Apache image
FROM php:8.2-apache

# Install PostgreSQL driver dependencies
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Enable PassEnv in Apache for environment variables
RUN echo "PassEnv DB_HOST DB_USER DB_PASSWORD DB_DATABASE" >> /etc/apache2/apache2.conf

# Copy app files
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html/

EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
