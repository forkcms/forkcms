FROM php:7.1-apache
MAINTAINER Fork CMS <info@fork-cms.com>

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install the dependencies and PHP extensions needed
RUN apt-get update && \
    apt-get install -y libpng12-dev libjpeg-dev libmcrypt-dev libicu-dev && rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr && \
    docker-php-ext-install gd mysqli opcache mcrypt intl mbstring pdo pdo_mysql zip

# Custom php.ini settings
COPY docker/php/php.ini /usr/local/etc/php/

# Enable Xdebug
COPY docker/php/xdebug.ini xdebug.ini
RUN pecl install xdebug-2.5.0 && \
        docker-php-ext-enable xdebug && \
        cat xdebug.ini  >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Set recommended PHP.ini settings
# We use opcache.revalidate_freq=0 in local development!
# See https://secure.php.net/manual/en/opcache.installation.php
# See https://www.scalingphpbook.com/blog/2014/02/14/best-zend-opcache-settings.html
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

WORKDIR /var/www/html

# Install composer and install dependencies. Instead of copying the source files, we first copy composer.json
# and the lock to speed-up the build. They will rarely change or invalidate the docker cache.
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer
COPY composer.json .
COPY composer.lock .
RUN composer install --no-scripts --no-interaction --no-autoloader

# Bundle source code into container. Certain files
COPY . /var/www/html

# Dump the autoloader and execute post-install scripts.
RUN composer dump-autoload --optimize && \
    composer run-script post-install-cmd

# Give apache write access to host
RUN chown -R www-data:www-data /var/www/html
