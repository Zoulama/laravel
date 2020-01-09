FROM php:7.2.0-cli

RUN mkdir -p /var/www/html
ADD  . /var/www/html
WORKDIR /var/www/html

COPY infrastructure/docker/tests/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT entrypoint.sh