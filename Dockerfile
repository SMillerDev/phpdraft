FROM apiaryio/drafter:latest as drafter
FROM composer:latest as composer

FROM php:7.4-cli-alpine as build
RUN apk add --no-cache \
		$PHPIZE_DEPS \
		openssl-dev
RUN pecl install uopz \
    && docker-php-ext-enable uopz
RUN echo "phar.readonly = 0" > "$PHP_INI_DIR/conf.d/phar.ini"
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY . /usr/src/phpdraft
WORKDIR /usr/src/phpdraft
RUN /usr/bin/composer install
RUN vendor/bin/phing phar-nightly
COPY build/out/phpdraft-nightly.phar /usr/local/bin/phpdraft

FROM php:7.4-cli-alpine
LABEL maintainer="Sean Molenaar sean@seanmolenaar.eu"
RUN apk add --no-cache gcc
COPY --from=drafter /usr/local/bin/drafter /usr/local/bin/drafter
COPY --from=build /usr/local/bin/phpdraft /usr/local/bin/phpdraft
ENTRYPOINT /usr/local/bin/phpdraft -f /tmp/drafter/full_test.apib
