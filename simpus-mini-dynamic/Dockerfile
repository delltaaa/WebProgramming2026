FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . /app

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t /app"]
