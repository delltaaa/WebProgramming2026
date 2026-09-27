FROM php:8.2-apache

# Install dependencies & ekstensi PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Bersihkan konfigurasi MPM yang bikin ganda/bentrok
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork rewrite

# Copy project
COPY . /var/www/html/

EXPOSE 80
