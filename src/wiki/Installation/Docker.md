[[_TOC_]]

Single server
=============

This is suitable for a local dev environment, or a production environment with a low to medium server load, or where latency is not a large actor.

We need the following directory structure added to a project:

    docker
    docker/nginx
    docker/php

Add the following files:

docker/nginx/admin.conf
-----------------------

Replace server_name with whatever domain you want to host locally.

```
server {
    listen 80;
    server_name admin.gaterdata.local;
    index index.php;
    error_log    /var/log/nginx/error.log debug;
    access_log    /var/log/nginx/access.log;
    root         /var/www/html/public/admin;
        
    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_index index.php;
        fastcgi_pass   php:9000;
    }

    location ~* \.(js|jpg|png|svg|css)$ {
        expires 1d;
    }
    
    location ~ /\.ht {
        deny  all;
    }
}
```

docker/nginx/api.conf
-----------------------

Replace server_name with whatever domain you want to host locally.

```
server {
    listen 80;
    server_name api.gaterdata.local;
    index index.php;
    error_log    /var/log/nginx/error.log debug;
    access_log    /var/log/nginx/access.log;
    root         /var/www/html/public;

    location ~ /(?!index.php$) {
        rewrite ^/(.*)$ /index.php?request=$1 last;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_index index.php;
        fastcgi_pass   php:9000;
    }
    
    location ~ /\.ht {
        deny  all;
    }
}
```

docker/php/Dockerfile
---------------------

```
FROM php:fpm
              
RUN apt-get update \
    && apt-get install -y iputils-ping \
    && docker-php-ext-install mysqli && docker-php-ext-enable mysqli
```

.env
----

Replace the values with whatever you wish.

```
APP_NAME=gaterdata

MYSQL_DATABASE=gaterdata
MYSQL_USER=gaterdata
MYSQL_PASSWORD=gaterdata
MYSQL_ROOT_PASSWORD=gaterdata

ADMIN_DOMAIN=admin.gaterdata.local
API_DOMAIN=api.gaterdata.local
```

docker-compose.yml
------------------

```
version: '3.7'

services:

  nginx-proxy:
    image: jwilder/nginx-proxy
    container_name: "${APP_NAME}-proxy"
    ports:
      - "80:80"
    volumes:
      - /var/run/docker.sock:/tmp/docker.sock:ro
    networks:
      - api_network

  api:
    image: nginx
    container_name: "${APP_NAME}-api"
    ports:
      - 80
    volumes:
      - ./docker/nginx/api.conf:/etc/nginx/conf.d/default.conf
      - ${PWD}:/var/www/html
    environment:
      - VIRTUAL_HOST=${API_DOMAIN}
    depends_on:
      - php
    networks:
      api_network:
        aliases:
          - ${API_DOMAIN}

  admin:
    image: nginx
    container_name: "${APP_NAME}-admin"
    ports:
      - 80
    volumes:
      - ./docker/nginx/admin.conf:/etc/nginx/conf.d/default.conf
      - ${PWD}:/var/www/html
    environment:
      - VIRTUAL_HOST=${ADMIN_DOMAIN}
    depends_on:
      - php
    networks:
      api_network:
        aliases:
          - ${ADMIN_DOMAIN}
    
  php:
    image: php:fpm
    container_name: "${APP_NAME}-php"
    build: ./docker/php
    ports:
      - "9000:9000"
    volumes:
      - ./composer:/composer
      - .:/var/www/html
    environment:
      - MYSQL_HOST=db
      - MYSQL_DATABASE=${MYSQL_DATABASE}
      - MYSQL_USER=${MYSQL_USER}
      - MYSQL_PASSWORD=${MYSQL_PASSWORD}
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
    networks:
      - api_network
  
  composer:
    image: composer
    container_name: "${APP_NAME}-composer"
    ports:
      - "9001:9000"
    volumes:
      - .:/app
    command: install
    networks:
      - api_network

  db:
    image: mariadb
    container_name: "${APP_NAME}-db"
    ports:
      - "3306:3306"
    volumes:
      - ./dbdata:/var/lib/mysql
    environment:
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
      - MYSQL_DATABASE=${MYSQL_DATABASE}
      - MYSQL_USER=${MYSQL_USER}
      - MYSQL_PASSWORD=${MYSQL_PASSWORD}
    restart: always
    networks:
      - api_network

networks:
  api_network:
    driver: bridge
```