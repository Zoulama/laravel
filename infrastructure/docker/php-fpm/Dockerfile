FROM php:7.2.0-fpm

RUN mkdir -p /var/www/html

RUN apt-get update && apt-get install -y
RUN apt-get install libcurl4-openssl-dev libssl-dev pkg-config -y

#### MySQL support
RUN docker-php-ext-install pdo pdo_mysql

#### MongoDb support
#RUN pecl install mongodb && docker-php-ext-enable mongodb


ADD . /var/www/html
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html

