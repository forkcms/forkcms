FROM php:7.1-apache
MAINTAINER Fork CMS <info@fork-cms.com>

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install GD2
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng12-dev && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd

# Install pdo_mysql
RUN docker-php-ext-install pdo_mysql

# Install mbstring
RUN docker-php-ext-install mbstring

# Install zip
RUN docker-php-ext-install zip

# Install intl
RUN apt-get update && apt-get install -y \
    g++ \
    libicu-dev \
    zlib1g-dev && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl

# Custom php.ini settings
COPY var/docker/php/php.ini ${PHP_INI_DIR}/php.ini

# Install and configure XDebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY var/docker/php/xdebug.ini ${PHP_INI_DIR}/conf.d/xdebug.init
RUN echo 'xdebug.remote_host="${DOCKER_HOST_IP}"' >> ${PHP_INI_DIR}/conf.d/xdebug.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /var/www/html

# Install the composer dependencies (no autoloader yet as that invalidates the docker cache)
COPY composer.json ./
COPY composer.lock ./
RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest && \
    composer clear-cache

# Bundle source code into container
COPY . /var/www/html

# Dump the autoloader
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# Give apache write access to host
RUN chown -R www-data:www-data /var/www/html
