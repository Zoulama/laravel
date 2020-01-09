FROM php:7.2.0-cli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer clearcache

RUN apt-get update && apt-get install -y
RUN apt-get install git zip libcurl4-openssl-dev libssl-dev pkg-config -y

#### MySQL support
#RUN docker-php-ext-install pdo pdo_mysql

#### MongoDb support
#RUN pecl install mongodb && docker-php-ext-enable mongodb

WORKDIR /var/www/html
ENTRYPOINT composer update

