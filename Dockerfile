FROM php:8.1-fpm

WORKDIR /var/www/html

COPY . .


RUN apt-get update  \
    && apt-get install -y git unzip

# Install dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Allow composer to be run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Install dependencies
RUN composer install --no-interaction --no-dev --prefer-dist --no-scripts --no-progress

RUN chmod -R 777 /var/www/html/
RUN chown -R www-data:www-data /var/www/html
RUN chown -R www-data:www-data /var/www/html/vendor
RUN chmod -R 755 /var/www/html/vendor

EXPOSE 9000

CMD ["php-fpm"]