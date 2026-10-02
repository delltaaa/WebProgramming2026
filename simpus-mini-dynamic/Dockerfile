FROM php:8.2-cli

# Install PostgreSQL extension
RUN docker-php-ext-install pdo_pgsql pgsql

# Set working directory
WORKDIR /app

# Copy project
COPY . /app

# Railway menggunakan PORT dari environment variable
CMD php -S 0.0.0.0:${PORT} -t /app
