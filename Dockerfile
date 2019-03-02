#
# PHP Dependencies
#
FROM composer as gaterdata-vendor
COPY composer.json composer.json
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

#
# Admin
#
FROM richarvey/nginx-php-fpm as gaterdata-admin
COPY . /usr/share/nginx/html
COPY ./.docker/vhost.d/admin.gaterdata.conf /etc/nginx/vhost.d/admin.gaterdata.local
COPY --from=gaterdata-vendor /app/vendor/ /usr/share/nginx/html/vendor/

#
# Api
#
FROM richarvey/nginx-php-fpm as gaterdata-api
COPY . /usr/share/nginx/html
COPY ./.docker/vhost.d/api.gaterdata.conf /etc/nginx/vhost.d/api.gaterdata.local
COPY --from=gaterdata-vendor /app/vendor/ /usr/share/nginx/html/vendor/
