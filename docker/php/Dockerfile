FROM php:8-fpm as base

RUN apt-get update \
    && apt-get install -y curl zip git

# pcntl
# RUN docker-php-ext-configure pcntl --enable-pcntl \
#   && docker-php-ext-install \
#     pcntl

# pdo
RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# igbinary
#RUN pecl install igbinary \
#    && docker-php-ext-enable igbinary

# redis
# RUN pecl install redis \
#    && docker-php-ext-enable redis

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin

WORKDIR /var/www

FROM base as dev

# xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN apt-get update && apt-get install -y htop

COPY docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN mkdir /home/v1acl && chown -R 1000:1000 /home/v1acl && \
    useradd -d /home/v1acl --uid 1000 -U v1acl

USER v1acl

FROM base as prod

COPY . .
COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/conf.d/php.ini /usr/local/etc/php/conf.d/php.ini
COPY docker/php/conf.d/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/php/conf.d/workers.ini /usr/local/etc/php/conf.d/workers.ini

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --no-scripts && \
    composer dump-autoload --no-dev --classmap-authoritative

CMD ["./docker/php/setup.sh"]