FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
	git \
	unzip \
	libzip-dev \
	libpng-dev \
	libonig-dev \
	libxml2-dev \
	&& docker-php-ext-install pdo_mysql mbstring zip exif pcntl \
	&& rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY ./backend/bank-sampah-app /var/www/html

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
	
EXPOSE 9000