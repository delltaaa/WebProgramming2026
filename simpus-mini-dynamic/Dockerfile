FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite

COPY . /var/www/html/

EXPOSE 80
