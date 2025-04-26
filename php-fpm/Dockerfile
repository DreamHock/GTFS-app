# myapp/php-fpm/Dockerfile
FROM php:8.3-fpm-alpine

RUN apk --update --no-cache add \
    git \
    make \
    g++ \
    autoconf \
    rabbitmq-c-dev \
    libtool \
    tzdata \
    linux-headers \
    openssl-dev \
    bash \
    curl \
    && pecl config-set php_ini /etc/php.ini \
    && pecl install mongodb-1.21.0 \
    && docker-php-ext-enable mongodb

# Install Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli

ENV TZ=Africa/Casablanca

RUN apk del --purge make g++ autoconf libtool \
    && rm -rf /var/cache/apk/*

RUN cd /usr/local/etc/php/conf.d/ && \
  echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

EXPOSE 9000

CMD composer install && php-fpm