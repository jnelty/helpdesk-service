FROM php:8.4-fpm
RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev libpq-dev zip libzip-dev zip redis-server \
    && docker-php-ext-install intl opcache pdo pdo_pgsql
WORKDIR /var/www/project
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS https://get.symfony.com/cli/installer | bash