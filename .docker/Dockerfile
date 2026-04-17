#################
# PHP
#################
FROM php:8.4-fpm AS php

COPY ./.docker/php-override.ini /usr/local/etc/php/conf.d/override.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN pecl install xdebug && docker-php-ext-enable xdebug

ARG UID=1000
ARG GID=1000
ARG USER=user
ENV ENV="/etc/profile"
RUN addgroup --gid $GID $USER \
    && adduser --gid $GID --disabled-password --uid $UID $USER
COPY .docker/.bashrc /home/user/.bashrc
RUN echo "export PS1='photos \w \$ '" >> /home/user/.bashrc

RUN rm -f /var/run/apache2/apache2.pid

# Install system dependencies and PHP extensions (e.g., MySQL)
RUN apt-get update
RUN apt-get install -y libfreetype6-dev
RUN apt-get install -y libjpeg62-turbo-dev
RUN apt-get install -y libpng-dev
RUN apt-get install -y libnss3
RUN apt-get install -y libxkbcommon0
RUN apt-get install -y libxext6
RUN apt-get install -y libgbm-dev
RUN apt-get install -y libasound2
RUN apt-get install -y libgmp-dev
RUN apt-get -y --no-install-recommends install default-mysql-client
RUN apt-get install -y git
RUN apt-get install -y zip
RUN apt-get install -y nano

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install gmp && docker-php-ext-enable gmp
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /var/www/
USER $USER

#################
# JS
#################
FROM node:24 AS js
WORKDIR /var/www
COPY ./package*.json ./
RUN npm install
CMD ["npm", "run", "dev"]
