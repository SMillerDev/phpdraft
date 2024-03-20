FROM debian:bullseye-slim AS drafter-build
RUN apt-get update && \
    apt-get install --yes curl ca-certificates

RUN curl -L --fail -o drafter.tar.gz https://github.com/apiaryio/drafter/releases/download/v5.1.0/drafter-v5.1.0.tar.gz
RUN install -d /usr/src/drafter
RUN tar -xvf drafter.tar.gz --strip-components=1 --directory /usr/src/drafter

WORKDIR /usr/src/drafter

RUN apt-get install --yes cmake g++

RUN cmake -S . -B build -DCMAKE_BUILD_TYPE=Release
RUN cmake --build build
RUN cmake --install build

FROM debian:bullseye-slim AS drafter
COPY --from=drafter-build /usr/local/bin/drafter /usr/local/bin/drafter

CMD drafter

FROM composer:latest AS composer

WORKDIR /usr/src/phpdraft
COPY . /usr/src/phpdraft/
RUN composer install --ignore-platform-req=ext-uopz

FROM php:8.3-cli-bullseye AS phpdraft-build

ARG PHPDRAFT_RELEASE_ID=0.0.0

RUN echo $PHPDRAFT_RELEASE_ID

COPY --from=composer /usr/src/phpdraft /usr/src/phpdraft
WORKDIR /usr/src/phpdraft

RUN echo "phar.readonly=0" >> /usr/local/etc/php/conf.d/phar.ini

RUN php ./vendor/bin/phing phar-nightly
RUN cp /usr/src/phpdraft/build/out/phpdraft-nightly.phar /usr/local/bin/phpdraft

FROM php:8.3-cli-bullseye AS phpdraft

LABEL maintainer="Sean Molenaar sean@seanmolenaar.eu"

COPY --from=drafter-build /usr/local/bin/drafter /usr/local/bin/drafter
COPY --from=phpdraft-build /usr/local/bin/phpdraft /usr/local/bin/phpdraft

CMD phpdraft
