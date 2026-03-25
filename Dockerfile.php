# Use official PHP with Apache
FROM php:8.2-apache

# Copy project files into Apache's web root
COPY . /var/www/html/

# Enable Apache rewrite module (useful if you add routing later)
RUN a2enmod rewrite

# Expose port 80 for web traffic
EXPOSE 80
