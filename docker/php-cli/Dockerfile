FROM php:8.4-cli

RUN apt-get update && apt-get install -y libzip-dev libpq-dev \
    && pecl install redis xdebug \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install zip sockets pdo_pgsql pcntl \
    && docker-php-ext-enable redis xdebug \
    && apt install passwd \
    && usermod -u 82 www-data && groupmod -g 82 www-data \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY php.ini xdebug.ini /usr/local/etc/php/conf.d/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV XDEBUG_MODE=coverage

WORKDIR /app
