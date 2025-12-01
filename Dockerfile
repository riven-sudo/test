# Use official PHP + Apache image
FROM php:8.2-apache

# Install system dependencies for PHP extensions
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libpq-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql pgsql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy app files
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Set working directory
WORKDIR /var/www/html/

# Expose port 80 (Render maps automatically)
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
