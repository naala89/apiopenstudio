Developer environment setup
===========================

This is suitable for a local dev environment.

Replace ```admin.apiopenstudio.local```, ```api.apiopenstudio.local``` and ```wiki.apiopenstudio.local``` with whatever domains you wish.

Directory and files
-------------------

The following files and directory structure needs to be added to a project:

    apiopenstudio
    |   .env
    |   docker-compose.yml
    └───certs
    └───docker
    │   └───nginx
    │       │   admin.conf
    │       │   api.conf
    │       │   wiki.conf
    │       │   phpdoc.conf
    └───php
        │   Dockerfile
        │   php.conf

### Setup SSL certificates

#### Mac

    brew install mkcert nss
    mkcert -install
    mkdir certs
    cd <apiopenstudio>/certs
    mkcert -cert-file apiopenstudio.local.crt -key-file apiopenstudio.local.key "*.apiopenstudio.local"
    cp "$(mkcert -CAROOT)/rootCA.pem" ca.crt

### .env

Copy ```example.env``` to ```.env```.

Edit the values so the values must match those in ```settings.yml```

    APP_NAME=apiopenstudio
    
    WITH_XDEBUG=true
    
    API_DOMAIN=api.apiopenstudio.local
    ADMIN_DOMAIN=admin.apiopenstudio.local
    WIKI_DOMAIN=wiki.apiopenstudio.local
    PHPDOC_DOMAIN=phpdoc.apiopenstudio.local
    
    MYSQL_HOST=mariadb
    MYSQL_DATABASE=apiopenstudio
    MYSQL_USER=apiopenstudio
    MYSQL_PASSWORD=apiopenstudio
    MYSQL_ROOT_PASSWORD=apiopenstudio
    
    EMAIL_USERNAME=foo@bar.com.au
    EMAIL_PASSWORD=secret

### docker/nginx/admin.conf

Replace server_name with whatever domain you want to host locally.

    server {
        listen 80;
        server_name admin.apiopenstudio.local;
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
        server_name api.apiopenstudio.local;
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

### docker/nging/phpdoc.conf

Replace server_name with whatever domain you want to host locally.

    server {
        listen 80;
        server_name phpdoc.apiopenstudio.local;
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

### docker/nging/wiki.conf

Replace server_name with whatever domain you want to host locally.

    server {
        listen 80;
        server_name wiki.apiopenstudio.local;
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

    FROM php:7-fpm
    
    ARG WITH_XDEBUG=false
    
    RUN apt-get update \
    && apt-get install -y iputils-ping \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli;
    
    RUN if [ $WITH_XDEBUG = "true" ] ; then \
    echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
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
            env_file:
                .env
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
            env_file:
                .env
            environment:
                - VIRTUAL_HOST=${ADMIN_DOMAIN}
            depends_on:
                - php
                - composer
            networks:
                api_network:
                    aliases:
                        - ${ADMIN_DOMAIN}
                   
        # Generic PHP container.
        php:
            image: php:7-fpm
            container_name: "${APP_NAME}-php"
            build:
                 context: ./docker/php
            args:
                - WITH_XDEBUG=${WITH_XDEBUG}
            env_file:
                .env
            ports:
                - "9000:9000"
            volumes:
                - ./composer:/composer
                - .:/var/www/html
                - ./docker/php/php.conf:/usr/local/etc/php-fpm.d/zzz-phpSettings.conf
                - ./logs/php:/var/log
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
            env_file:
                .env
            restart: always
            networks:
                - api_network
                   
        # Email container.
        email:
            image: namshi/smtp:latest
            container_name: "${APP_NAME}-email"
            networks:
                - api_network
            # ports:
            #   - "25:25"
            environment:
                # MUST start with : e.g RELAY_NETWORKS=:192.168.0.0/24:10.0.0.0/16
                # if acting as a relay this or RELAY_DOMAINS must be filled out or incoming mail will be rejected
            #   - RELAY_NETWORKS= :192.168.0.0/24
                # what domains should be accepted to forward to lower distance MX server.
            #   - RELAY_DOMAINS= <domain1> : <domain2> : <domain3>
                # To act as a Gmail relay
                - GMAIL_USER=${EMAIL_USERNAME}
                - GMAIL_PASSWORD=${EMAIL_PASSWORD}
                # For use with Amazon SES relay
            #   - SES_USER=
            #   - SES_PASSWORD=
            #   - SES_REGION=
                # if provided will enable TLS support
            #   - KEY_PATH=certs/apiopenstudio.local
            #   - CERTIFICATE_PATH=certs/apiopenstudio.local.crt
                # the outgoing mail hostname
            #   - MAILNAME=admin.apiopenstudio.local
                # set this to any value to disable ipv6
            #   - DISABLE_IPV6=
                # Generic SMTP Relay
            #   - SMARTHOST_ADDRESS=
            #   - SMARTHOST_PORT=
            #   - SMARTHOST_USER=
            #   - SMARTHOST_PASSWORD=
            #   - SMARTHOST_ALIASES=
                   
        # # Uncomment this for compiling the wiki.
        # # Bookdown container.
        # bookdown:
        #     image: sandrokeil/bookdown
        #     container_name: "${APP_NAME}-bookdown"
        #     volumes:
        #         - ./src/wiki:/app
        #         - ./public/wiki:/wiki
        #     command: ["bookdown.json"]
        #     networks:
        #         - api_network
                   
        # # Uncomment this to serve the wiki locally.
        # # NGINX Wiki server.
        # wiki:
        #     image: nginx:stable
        #     container_name: "${APP_NAME}-wiki"
        #     hostname: "${WIKI_DOMAIN}"
        #     ports:
        #         - 80
        #     volumes:
        #         - ./docker/nginx/wiki.conf:/etc/nginx/conf.d/default.conf
        #         - ./public/wiki:/var/www/html
        #         - ./logs/wiki:/var/log/nginx
        #         - ./certs/ca.crt:/usr/local/share/ca-certificates/ca.crt
        #     environment:
        #         - VIRTUAL_HOST=${WIKI_DOMAIN}
        #     networks:
        #         api_network:
        #             aliases:
        #                 - ${WIKI_DOMAIN}
        
        # # Uncomment this for compiling the phpdoc API.
        # # PhpDocumentor container.
        # phpdocumentor:
        #     image: phpdoc/phpdoc
        #     container_name: "${APP_NAME}-phpdocumentor"
        #     volumes:
        #         - .:/data
        #     command: "run -d ./ -t public/phpdoc --ignore vendor/,tests/"
        #     networks:
        #         - api_network
  
        # # Uncomment this to serve the phpdoc locally.
        # # NGINX Wiki server.
        # phpdoc:
        #     image: nginx:stable
        #     container_name: "${APP_NAME}-phpdoc"
        #     hostname: "${PHPDOC_DOMAIN}"
        #     ports:
        #         - 80
        #     volumes:
        #         - ./docker/nginx/phpdoc.conf:/etc/nginx/conf.d/default.conf
        #         - ./public/phpdoc/html:/var/www/html
        #         - ./logs/phpdoc:/var/log/nginx
        #         - ./certs/ca.crt:/usr/local/share/ca-certificates/ca.crt
        #     environment:
        #         - VIRTUAL_HOST=${PHPDOC_DOMAIN}
        #     networks:
        #         api_network:
        #             aliases:
        #                 - ${PHPDOC_DOMAIN}
               
    networks:
        api_network:
            driver: bridge
        
Hosts file
----------

Update ```/etc/hosts``` to contain:

    127.0.0.1      admin.apiopenstudio.local
    127.0.0.1      api.apiopenstudio.local
    127.0.0.1      wiki.apiopenstudio.local

Spinning up docker
------------------

    $ docker-compose up -d
