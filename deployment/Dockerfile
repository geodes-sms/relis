# PHP2Shell fixes: PHP 8.4.27. Pin the official multi-platform image manifest.
FROM php:8.4.27-apache-bookworm@sha256:5d55c49594dd833e2e4ff677b65f7d8ff48817f443a60691d9b2b382413bba42

ARG DIRECTORY_INDEX=index.php
ARG DOCUMENT_ROOT=/u/relis/public_html
ARG ENV=production
ARG XDEBUG_VERSION=3.5.3
ARG COMPOSER_VERSION=2.11.2
# https://getcomposer.org/download/2.11.2/composer.phar.sha256sum
ARG COMPOSER_SHA256=dd158c0afd41d15fac89e1b4e47b5d42911fe316c3687cd5edac98319b25d028

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl vim gnupg wget ksh unzip zip git libzip-dev libicu-dev \
    && docker-php-ext-install -j"$(nproc)" intl mysqli pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL "https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar" -o /tmp/composer.phar \
    && echo "${COMPOSER_SHA256}  /tmp/composer.phar" | sha256sum -c - \
    && install -m 755 /tmp/composer.phar /usr/local/bin/composer \
    && rm /tmp/composer.phar

RUN if [ "$ENV" = dev ] || [ "$ENV" = development ]; then \
        pecl install "xdebug-${XDEBUG_VERSION}" && docker-php-ext-enable xdebug; \
    fi

# PHP scans this directory directly; nested apache/cli directories were not selected by SAPI.
COPY ./php/conf/php.ini /usr/local/etc/php/conf.d/zz-relis.ini
COPY ./php/conf/50_xdebug.ini /tmp/relis-xdebug.ini
RUN if [ "$ENV" = dev ] || [ "$ENV" = development ]; then \
        cp /tmp/relis-xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini; \
    fi \
    && rm /tmp/relis-xdebug.ini

ENV APACHE_DOCUMENT_ROOT=${DOCUMENT_ROOT}
ENV ENV=${ENV}
COPY ./conf/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN sed -i -e "s|__DOCUMENT_ROOT__|${DOCUMENT_ROOT}|g" \
           -e "s|__DIRECTORY_INDEX__|${DIRECTORY_INDEX}|g" \
           /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

COPY ./entrypoint.sh /usr/local/bin/relis-entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/relis-entrypoint \
    && chmod 755 /usr/local/bin/relis-entrypoint \
    && mkdir -p "${DOCUMENT_ROOT}"
WORKDIR ${DOCUMENT_ROOT}
ENTRYPOINT ["/usr/local/bin/relis-entrypoint"]
