FROM php:8.3-fpm

WORKDIR /var/www

#copy all BE files to working directory
COPY . /var/www/

#update and install necessary libs
RUN apt-get update
RUN apt-get install -y libpq-dev
RUN apt-get install -y unzip
#install postgre extension
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_pgsql pgsql

#copy compose
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]