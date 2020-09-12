# Developer environment setup

This is suitable for a local dev environment.

Replace ```admin.gaterdata.local```, ```api.gaterdata.local``` and ```wiki.gaterdata.local``` with whatever domains you wish.

## Directory and files

The following files and directory structure needs to be added to a project:

    gaterdata
    │   .env
    │   docker-compose.yml
    │
    └───certs
    |
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

### Setup SSL certificates

#### Mac

    $ brew install mkcert nss
    $ mkcert -install
    $ cd <gaterdata>/certs
    $ mkcert -cert-file gaterdata.local.crt -key-file gaterdata.local.key "*.gaterdata.local"
    $ cp "$(mkcert -CAROOT)/rootCA.pem" ca.crt

### .env

These values are required by docker-compose.

So the values must match those in ```settings.yml```
    
    APP_NAME=gaterdata
    
    API_DOMAIN=api.gaterdata.local
    ADMIN_DOMAIN=admin.gaterdata.local
    WIKI_DOMAIN=wiki.gaterdata.local
    
    MYSQL_HOST=mariadb
    MYSQL_DATABASE=gaterdata
    MYSQL_USER=gaterdata
    MYSQL_PASSWORD=gaterdata
    MYSQL_ROOT_PASSWORD=gaterdata
    
    EMAIL_USERNAME=admin@gaterdata.com
    EMAIL_PASSWORD=secret

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
    
    ARG WITH_XDEBUG=false
                  
    RUN apt-get update \
        && apt-get install -y iputils-ping \
        && docker-php-ext-install mysqli \
        && docker-php-ext-enable mysqli
    RUN if [ $WITH_XDEBUG = "true" ] ; then \
            pecl install xdebug; \
            docker-php-ext-enable xdebug; \
            echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
            echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
            echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
            echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        fi ;

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
          - "443:443"
        volumes:
          - /var/run/docker.sock:/tmp/docker.sock:ro
          - ./certs:/etc/nginx/certs
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
          - .:/var/www/html
          - ./logs/api:/var/log/nginx
          - ./certs/ca.crt:/usr/local/share/ca-certificates/ca.crt
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
          - .:/var/www/html
          - ./logs/admin:/var/log/nginx
          - ./certs/ca.crt:/usr/local/share/ca-certificates/ca.crt
        environment:
          - VIRTUAL_HOST=${ADMIN_DOMAIN}
        depends_on:
          - php
        networks:
          api_network:
            aliases:
              - ${ADMIN_DOMAIN}
    
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
          - ./logs/php:/var/log
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
    
      email:
        image: namshi/smtp:latest
        container_name: "${APP_NAME}-email"
        networks:
          - api_network
    #    ports:
    #      - "25:25"
        environment:
    #      # MUST start with : e.g RELAY_NETWORKS=:192.168.0.0/24:10.0.0.0/16
    #      # if acting as a relay this or RELAY_DOMAINS must be filled out or incoming mail will be rejected
    #      - RELAY_NETWORKS= :192.168.0.0/24
    #      # what domains should be accepted to forward to lower distance MX server.
    #      - RELAY_DOMAINS= <domain1> : <domain2> : <domain3>
    #      # To act as a Gmail relay
          - GMAIL_USER=${EMAIL_USERNAME}
          - GMAIL_PASSWORD=${EMAIL_PASSWORD}
    #      # For use with Amazon SES relay
    #      - SES_USER=
    #      - SES_PASSWORD=
    #      - SES_REGION=
    #      # if provided will enable TLS support
    #      - KEY_PATH=certs/gaterdata.local
    #      - CERTIFICATE_PATH=certs/gateradta.local.crt
    #      # the outgoing mail hostname
    #      - MAILNAME=admin.gaterdata.local
    #      # set this to any value to disable ipv6
    #      - DISABLE_IPV6=
    #      # Generic SMTP Relay
    #      - SMARTHOST_ADDRESS=
    #      - SMARTHOST_PORT=
    #      - SMARTHOST_USER=
    #      - SMARTHOST_PASSWORD=
    #      - SMARTHOST_ALIASES=
    
    ## Uncomment this for compiling the wiki
    #  # Bookdown container
    #  bookdown:
    #    image: sandrokeil/bookdown
    #    container_name: "${APP_NAME}-bookdown"
    #    volumes:
    #      - ./src/wiki:/app
    #      - ./public/wiki:/wiki
    #    command: ["bookdown.json"]
    #    networks:
    #      - api_network
    
    ## Uncomment this to serve the wiki locally
    #  # NGINX Wiki server
    #  wiki:
    #    image: nginx:stable
    #    container_name: "${APP_NAME}-wiki"
    #    hostname: "${WIKI_DOMAIN}"
    #    ports:
    #      - 80
    #    volumes:
    #      - ./docker/nginx/wiki.conf:/etc/nginx/conf.d/default.conf
    #      - ./public/wiki:/var/www/html
    #      - ./logs/wiki:/var/log/nginx
    #      - ./certs/ca.crt:/usr/local/share/ca-certificates/ca.crt
    #    environment:
    #      - VIRTUAL_HOST=${WIKI_DOMAIN}
    #    networks:
    #      api_network:
    #        aliases:
    #          - ${WIKI_DOMAIN}
    
    networks:
      api_network:
        driver: bridge
        
Hosts file
----------

Update ```/etc/hosts``` to contain:

    127.0.0.1      admin.gaterdata.local
    127.0.0.1      api.gaterdata.local
    127.0.0.1      wiki.gaterdata.local

Spinning up docker
------------------

    $ docker-compose up -d