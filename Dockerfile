FROM php:7.4-apache
LABEL maintainer="Fork CMS <info@fork-cms.com>"

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install GD2
RUN apt-get update && apt-get install -y --no-install-recommends --allow-downgrades \
    libonig-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libz-dev \
    zlib1g-dev \
    libpng-dev && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd && \
    rm -rf /var/lib/apt/lists/*

# Install pdo_mysql
RUN docker-php-ext-install pdo_mysql

# Install zip & unzip
RUN apt-get update && apt-get install -y libzip-dev zip && \
    docker-php-ext-install zip && \
    rm -rf /var/lib/apt/lists/*

# Install intl
RUN apt-get update && apt-get install -y --no-install-recommends \
    g++ \
    libicu-dev \
    zlib1g-dev && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl && \
    rm -rf /var/lib/apt/lists/*

# Custom php.ini settings
COPY var/docker/php/php.ini ${PHP_INI_DIR}/php.ini

# Install and configure XDebug
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    rm -rf /tmp/pear

# Install composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

# Set the work directory to /var/www/html so all subsequent commands in this file start from that directory.
# Also set this work directory so that it uses this directory everytime we use docker exec.
WORKDIR /var/www/html

# Install the composer dependencies (no autoloader yet as that invalidates the docker cache)
COPY composer.json ./
COPY composer.lock ./
RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress && \
    composer clear-cache

# Bundle source code into container. Important here is that copying is done based on the rules defined in the .dockerignore file.
COPY . /var/www/html

# Dump the autoloader
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# Give apache write access to host
RUN chown -R www-data:www-data /var/www/html

# This specifies on which port the application will run. This is pure communicative and makes this 12 factor app compliant
# (see https://12factor.net/port-binding).
EXPOSE 80 443
