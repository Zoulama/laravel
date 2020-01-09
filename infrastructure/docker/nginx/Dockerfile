# Configure and start Nginx
FROM nginx
RUN mkdir -p /var/www/html
ADD . /var/www/html
WORKDIR /var/www/html
ADD infrastructure/docker/nginx/server.conf /etc/nginx/conf.d/default.conf