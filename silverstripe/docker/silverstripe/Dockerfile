FROM --platform=linux/arm64/v8 brettt89/silverstripe-web:8.0-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#RUN pecl install xdebug \
#    && docker-php-ext-enable xdebug \

#RUN apt-get update && apt-get install -y \
#      libfreetype6-dev \
#      libjpeg62-turbo-dev \
#      libpng-dev \
#      libicu-dev \
#    && docker-php-ext-configure gd --with-freetype --with-jpeg \
##    && docker-php-ext-install -j$(nproc) gd \
 #   && docker-php-ext-configure intl \
 #   && docker-php-ext-install intl mysqli

