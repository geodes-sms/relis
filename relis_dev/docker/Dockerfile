FROM php:7.1-apache-buster

ARG DIRECTORY_INDEX
ARG DOCUMENT_ROOT

RUN apt-get autoclean && apt-get update && apt-get install -y \
    curl \
    vim \
    gnupg \
    wget \
    ksh \
    unzip \
    zip \
    git

RUN apt-get install -y libicu-dev
RUN docker-php-ext-install intl mysqli pdo pdo_mysql

RUN pecl install xdebug-2.8.0 \
    && docker-php-ext-enable xdebug


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

## Make a lsb-release file so the installer knows the os (necessary for debian)
RUN cp /etc/os-release /etc/lsb-release

## Create and switch user for a non-root installation
RUN useradd -ms /bin/bash docker
USER docker

USER root

#Set php.ini configuration

COPY ./php/conf/apache/php.ini "$PHP_INI_DIR/conf.d/apache/php.ini"
COPY ./php/conf/cli/php.ini  "$PHP_INI_DIR/conf.d/cli/php.ini"
COPY ./php/conf/php.ini "$PHP_INI_DIR/conf.d/php.ini"
COPY ./php/conf/50_xdebug.ini "$PHP_INI_DIR/conf.d/50_xdebug.ini"

# Set apache vhost
RUN echo ${DIRECTORY_INDEX}
RUN echo ${DOCUMENT_ROOT}
ENV APACHE_DOCUMENT_ROOT ${DOCUMENT_ROOT}
COPY ./apache/conf/vhost.conf /etc/apache2/sites-available
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite
COPY ./entrypoint.sh /usr/local/bin
RUN sed 's/\r$//g' /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR ${DOCUMENT_ROOT}
