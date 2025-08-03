# Dockerfile

FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git zip unzip curl libicu-dev libonig-dev libzip-dev \
    libxml2-dev libcurl4-openssl-dev libssl-dev \
    && docker-php-ext-install intl zip opcache curl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html/metricalo-test

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/metricalo-test

# Expose FPM port
EXPOSE 9000

CMD ["php-fpm"]
