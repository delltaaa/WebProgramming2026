FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

CMD php -S 0.0.0.0:${PORT:-8080}
