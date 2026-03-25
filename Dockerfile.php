# Use official PHP with Apache
FROM php:8.2-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libicu-dev \
    git curl zip unzip \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath gd intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite (needed for Laravel routes)
RUN a2enmod rewrite

# Copy project files
WORKDIR /var/www/html
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 80
EXPOSE 80

apache2-foreground
