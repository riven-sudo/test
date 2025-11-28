# Step 1: Use official PHP + Apache image
FROM php:8.2-apache

# Step 2: Copy project files into container
COPY . /var/www/html/

# Step 3: Enable mod_rewrite (optional)
RUN a2enmod rewrite

# Step 4: Set working directory
WORKDIR /var/www/html/

# Step 5: Expose port 80
EXPOSE 80
