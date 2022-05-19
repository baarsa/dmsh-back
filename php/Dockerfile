FROM php:8.1-apache
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN apt update && apt install -y zlib1g-dev libpng-dev libzip-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install gd && docker-php-ext-enable gd
RUN docker-php-ext-install zip && docker-php-ext-enable zip
RUN apt-get update && apt-get upgrade -y
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = host.docker.internal" >> \
/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN a2enmod rewrite
COPY /src/composer.json /var/www/html
WORKDIR /var/www/html/
RUN composer install
