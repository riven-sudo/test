# Use official PHP + Apache image
FROM php:8.2-apache

# Install mysqli, pdo, and pdo_mysql extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Copy app files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Set working directory
WORKDIR /var/www/html/

# Expose port 80
EXPOSE 80
