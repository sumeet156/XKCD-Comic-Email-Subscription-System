# Use official PHP Apache image
FROM php:8.3-apache

# Copy everything to the web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Optional: install PHP extensions (none required for this app)
RUN docker-php-ext-install mysqli
