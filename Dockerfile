FROM php:8.3-apache

# Copy all files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Install any PHP extensions (if needed)
RUN docker-php-ext-install mysqli

# Set the default command for background worker
#CMD ["php", "cron.php"]
