FROM php:8.1-fpm

WORKDIR /var/www

# Install required PHP extensions (adjust as needed)
RUN docker-php-ext-install pdo pdo_pgsql

# Set permissions
RUN chmod -R 755 /var/www/backend/

EXPOSE 9000
CMD ["php-fpm"]