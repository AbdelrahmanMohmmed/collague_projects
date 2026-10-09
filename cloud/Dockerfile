# Use the official PHP image
FROM php:7.4-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy your PHP script into the container
COPY . /var/www/html/

# Expose port 80
EXPOSE 80
