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
COPY docker/php/php.ini /usr/local/etc/php/

# Install & enable Xdebug
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN pecl install xdebug-2.5.0 && \
    docker-php-ext-enable xdebug

# Install composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /var/www/html

COPY composer.json ./
COPY composer.lock ./

RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest && \
    composer clear-cache

# Bundle source code into container
COPY . /var/www/html

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# Give apache write access to host
RUN chown -R www-data:www-data /var/www/html
