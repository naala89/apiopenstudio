FROM existenz/webstack:7.4

RUN apk -U --no-cache add \
    openssl \
    libzip-dev \
    openssh-client \
    rsync \
    libxslt-dev \
    unzip \
    zip \
    iproute2 \
    mariadb-client \
    php-cli \
    php7-ctype \
    php7-curl \
    php7-openssl \
    php7-json \
    php7-phar \
    php7-iconv \
    php7-mbstring \
    php7-sodium \
    php7-tokenizer \
    php7-xmlwriter \
    php7-simplexml \
    php7-dom \
    php7-xml \
    php7-mysqli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./nginx.conf.template /etc/nginx/nginx.conf
