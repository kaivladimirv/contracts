FROM php:8.3-fpm

RUN apt-get update && apt-get install -y libpq-dev \
    && pecl install redis xdebug \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install sockets pdo_pgsql \
    && docker-php-ext-enable redis xdebug \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN mkdir -p /var/run/php \
    && apt install passwd \
    && usermod -u 82 www-data && groupmod -g 82 www-data \
    && chown -R www-data:www-data /var/run/php \
    && chmod 755 /var/run/php

COPY php.ini xdebug.ini /usr/local/etc/php/conf.d/
COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /app
