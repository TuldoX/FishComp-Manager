FROM php:8.1-fpm

WORKDIR /var/www

COPY backend/ /var/www/backend/
RUN chmod -R 755 /var/www/backend/

EXPOSE 9000
CMD ["php-fpm"]
