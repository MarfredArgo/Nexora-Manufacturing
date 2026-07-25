FROM php:8.4-cli


RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

RUN npm install && npm run build

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

CMD ["sh", "-c", "php artisan config:clear --no-interaction && php artisan route:clear --no-interaction && php artisan view:clear --no-interaction && php artisan hr:ensure-employees-table --no-interaction && php artisan inventory:install-schema --no-interaction && php artisan ecommerce:install-schema --no-interaction && php artisan manufacturing:install-schema --no-interaction && php artisan finance:install-schema --no-interaction && php artisan bi:install-schema --no-interaction && php artisan procurement:install-schema --force --no-interaction && php artisan procurement:ensure-client-columns --no-interaction && php artisan order-fulfillment:install-schema --force --no-interaction && php artisan order-fulfillment:ensure-client-columns --no-interaction && php artisan itsm:install-schema --no-interaction && php artisan serve --host=0.0.0.0 --port=$PORT"]
