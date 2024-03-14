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

CMD drafter

FROM composer:latest AS composer

WORKDIR /usr/src/phpdraft
COPY . /usr/src/phpdraft/
RUN composer install --ignore-platform-req=ext-uopz

FROM php:8.3-cli-bullseye AS phpdraft-build



COPY --from=composer /usr/src/phpdraft /usr/src/phpdraft
WORKDIR /usr/src/phpdraft

RUN ./vendor/bin/phing phar-nightly
COPY /usr/src/phpdraft/build/out/phpdraft-nightly.phar /usr/local/bin/phpdraft
RUN chmod +x /usr/local/bin/phpdraft

FROM php:8.3-cli-bullseye AS phpdraft

LABEL maintainer="Sean Molenaar sean@seanmolenaar.eu"

COPY --from=phpdraft-build /usr/local/bin/phpdraft /usr/local/bin/phpdraft
COPY --from=drafter-build /usr/local/bin/drafter /usr/local/bin/drafter

RUN ls -al /usr/local/bin/phpdraft

CMD phpdraft
