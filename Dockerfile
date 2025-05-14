FROM php:8.4-fpm
RUN apt-get update && apt-get install -y libicu-dev git libpq-dev redis-server \
    && docker-php-ext-install intl opcache pdo pdo_pgsql
WORKDIR /var/www/project
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://get.symfony.com/cli/installer | bash