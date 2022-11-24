FROM php:8.1-fpm-alpine
# $PHPIZE_DEPS servono per xDebug
RUN apk update && \
    apk add  \
    		openldap-dev \
    		$PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable pdo_mysql \
    && docker-php-ext-install ldap \
    && docker-php-ext-enable ldap \
    && apk del $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/*
EXPOSE 80
