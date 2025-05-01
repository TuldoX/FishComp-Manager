FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    zip unzip curl libzip-dev

# Set working directory
WORKDIR /var/www

# Composer install
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

EXPOSE 9000

CMD ["php-fpm"]