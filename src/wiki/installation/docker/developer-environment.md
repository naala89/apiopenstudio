Developer environment setup
===========================

This is suitable for a local dev environment.

Directory and files
-------------------

The following files and directory structure needs to be added to a project:

    gaterdata
    │   .env
    │   docker-compose.yml
    │
    └───docker
    │   │
    │   └───nginx
    │       │   admin.conf
    │       │   api.conf
    │       │   wiki.conf
    │   
    └───php
        │   Dockerfile
        │   php.conf

### .env

Replace the values with whatever you wish.

    APP_NAME=gaterdata
    
    MYSQL_DATABASE=gaterdata
    MYSQL_USER=gaterdata
    MYSQL_PASSWORD=gaterdata
    MYSQL_ROOT_PASSWORD=gaterdata
    
    ADMIN_DOMAIN=admin.gaterdata.local
    API_DOMAIN=api.gaterdata.local

### config/settings.ini

Update the following keys to take values from .env:

    [db]
    host = ${MYSQL_HOST}
    root_password = ${MYSQL_ROOT_PASSWORD}
    username = ${MYSQL_USER}
    password = ${MYSQL_PASSWORD}
    database = ${MYSQL_DATABASE}
    
    [api]
    url = ${API_DOMAIN}
    
    [admin]
    url = ${ADMIN_DOMAIN}

### docker/nginx/admin.conf

Replace server_name with whatever domain you want to host locally.

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

### docker/nginx/api.conf

Replace server_name with whatever domain you want to host locally.

    server {
        listen 80;
        server_name api.gaterdata.local;
        index index.php;
        error_log /var/log/nginx/error.log debug;
        access_log /var/log/nginx/access.log;
        root /var/www/html/public;
    
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
            fastcgi_pass php:9000;
        }
        
        location ~ /\.ht {
            deny all;
        }
    }

### docker/nging/wiki.conf

Replace server_name with whatever domain you want to host locally.

    server {
        listen 80;
        server_name wiki.gaterdata.local;
        index index.html;
        error_log /var/log/nginx/error.log debug;
        access_log /var/log/nginx/access.log;
        root /var/www/html;
    
        location ~* \.(js|jpg|png|svg|css)$ {
            expires 1d;
        }
        
        location ~ /\.ht {
            deny all;
        }
    }

### docker/php/Dockerfile

    FROM php:fpm
                  
    RUN apt-get update \
        && apt-get install -y iputils-ping \
        && docker-php-ext-install mysqli && docker-php-ext-enable mysqli

### docker/php/php.conf

    [www]
    user = www-data
    group = www-data
    listen = 0.0.0.0:9000
    pm = dynamic
    pm.max_children = 5
    pm.start_servers = 2
    pm.min_spare_servers = 1
    pm.max_spare_servers = 3
    
    catch_workers_output = yes
    php_admin_flag[log_errors] = on
    php_admin_flag[display_errors] = off
    php_admin_value[error_reporting] = E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT & ~E_DEPRECATED
    php_admin_value[error_log] = /var/log/error.log
    access.log = /var/log/access.log
    php_value[memory_limit] = 512M
    php_value[post_max_size] = 24M
    php_value[upload_max_filesize] = 24M

### docker-compose.yml

    version: '3.7'
    
    services:
    
      # Reverse Proxy.
      nginx-proxy:
        image: jwilder/nginx-proxy:alpine
        container_name: "${APP_NAME}-proxy"
        ports:
          - "80:80"
        volumes:
          - /var/run/docker.sock:/tmp/docker.sock:ro
        networks:
          - api_network
    
      # NGINX API server.
      api:
        image: nginx:stable
        container_name: "${APP_NAME}-api"
        hostname: "${API_DOMAIN}"
        ports:
          - 80
        volumes:
          - ./docker/nginx/api.conf:/etc/nginx/conf.d/default.conf
          - ${PWD}:/var/www/html
          - ${PWD}/logs/api:/var/log/nginx
        environment:
          - VIRTUAL_HOST=${API_DOMAIN}
        depends_on:
          - php
        networks:
          api_network:
            aliases:
              - ${API_DOMAIN}
    
      # NGINX Admin server.
      admin:
        image: nginx:stable
        container_name: "${APP_NAME}-admin"
        hostname: "${ADMIN_DOMAIN}"
        ports:
          - 80
        volumes:
          - ./docker/nginx/admin.conf:/etc/nginx/conf.d/default.conf
          - ${PWD}:/var/www/html
          - ${PWD}/logs/admin:/var/log/nginx
        environment:
          - VIRTUAL_HOST=${ADMIN_DOMAIN}
        depends_on:
          - php
        networks:
          api_network:
            aliases:
              - ${ADMIN_DOMAIN}
    
      # Bookdown container
      bookdown:
        image: sandrokeil/bookdown
        container_name: "${APP_NAME}-bookdown"
        volumes:
          - ./src/wiki:/app
          - ./public/wiki:/wiki
        command: ["bookdown.json"]
        networks:
          - api_network
    
      # NGINX Wiki server
      wiki:
        image: nginx:stable
        container_name: "${APP_NAME}-wiki"
        hostname: "${WIKI_DOMAIN}"
        ports:
          - 80
        volumes:
          - ./docker/nginx/wiki.conf:/etc/nginx/conf.d/default.conf
          - ${PWD}/public/wiki:/var/www/html
          - ${PWD}/logs/wiki:/var/log/nginx
        environment:
          - VIRTUAL_HOST=${WIKI_DOMAIN}
        networks:
          api_network:
            aliases:
              - ${WIKI_DOMAIN}
    
      #  Generic PHP container.
      php:
        image: php:fpm-stretch
        container_name: "${APP_NAME}-php"
        build:
          context: ./docker/php
          args:
            - WITH_XDEBUG=true
        env_file:
          .env
        ports:
          - "9000:9000"
        volumes:
          - ./composer:/composer
          - .:/var/www/html
          - ./docker/php/php.conf:/usr/local/etc/php-fpm.d/zzz-phpSettings.conf
          - ${PWD}/logs/php:/var/log
        environment:
          - MYSQL_HOST=db
          - MYSQL_DATABASE=${MYSQL_DATABASE}
          - MYSQL_USER=${MYSQL_USER}
          - MYSQL_PASSWORD=${MYSQL_PASSWORD}
          - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
        networks:
          - api_network
    
      # Node container to install npm requires and run gulp.
      node:
        image: node:11
        container_name: "${APP_NAME}-node"
        volumes:
          - .:/usr/src/service
        working_dir: /usr/src/service
        command: bash -c "npm install && npm install -g gulp && gulp all"
        networks:
          - api_network
    
      # Install composer requires.
      composer:
        image: composer:latest
        container_name: "${APP_NAME}-composer"
        ports:
          - "9001:9000"
        volumes:
          - .:/app
        command: install
        networks:
          - api_network
    
      # Database container.
      db:
        image: mariadb:latest
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

Spinning up docker
------------------

    $ docker-compose up -d