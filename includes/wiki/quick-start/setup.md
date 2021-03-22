Setup
=====

Install ApiOpenStudio and ApiOpenStudio Admin
---------------------------------------------

Install [docker](https://docs.docker.com/get-docker/).

Install [git](https://github.com/git-guides/install-git).

    cd /path/to/my/development/directory
    composer create-project apiopenstudio/apiopenstudio:1.0.0-alpha
    composer create-project apiopenstudio/apiopenstudio_admin:1.0.0-alpha
    git clone git@github.com:naala89/api_open_studio_docker.git

### Setup ApiOpenStudio

    cd /path/to/my/development/directory/api_open_studio
    cp example.settings.yml settings.yml

Leave all settings as default

### Setup ApiOpenStudio Admin

    cd /path/to/my/development/directory/api_open_studio_admin
    cp example.settings.yml settings.yml

Leave all settings as default

### Setup Docker

    cd /path/to/my/development/directory/api_open_studio_docker
    cp example.env .env

Edit ```.env```

Set to values for ```API_CODEBASE``` and ```ADMIN_CODEBASE``` to point to your
git clones, e.g.:

    API_CODEBASE=/path/to/my/development/directory/api_open_studio
    ADMIN_CODEBASE=/path/to/my/development/directory/api_open_studio_admin

### Setup SSL certificates

#### Mac

    brew install mkcert nss
    mkcert -install

    cd <apiopenstudio>/certs
    mkcert -cert-file apiopenstudio.local.crt -key-file apiopenstudio.local.key "*.apiopenstudio.local"
    cp "$(mkcert -CAROOT)/rootCA.pem" ca.crt

#### Linux

    sudo apt-get update
    sudo apt-get install wget libnss3-tools
    export VER="v1.3.0"
    wget -O mkcert https://github.com/FiloSottile/mkcert/releases/download/${VER}/mkcert-${VER}-linux-amd64
    chmod +x  mkcert
    sudo mv mkcert /usr/local/bin
    
    cd <apiopenstudio>/certs
    mkcert -cert-file apiopenstudio.local.crt -key-file apiopenstudio.local.key "*.apiopenstudio.local"
    cp "$(mkcert -CAROOT)/rootCA.pem" ca.crt

#### Enable the wiki and phpdoc (optional).

Edit ```docker-composer.yml```

Uncomment the container blocks for:

* wiki
* phpdocumentor
* phpdoc

### Let your computer know where the hostnames reside

Edit ```/etc/hosts``` and add the following:

    127.0.0.1      admin.apiopenstudio.local
    127.0.0.1      api.apiopenstudio.local
    127.0.0.1      wiki.apiopenstudio.local
    127.0.0.1      phpdoc.apiopenstudio.local

### Start docker

    docker-compose up -d

### Compile the wiki (optional)

    cd <project_root>
    export CSS_BOOTSWATCH=spacelab && export CSS_PRISM=prism && MENU_LOGO=/img/api_open_studio_logo_name_colour.png && php ./vendor/bin/bookdown includes/wiki/bookdown.json

### Setup the database

    docker-compose exec -it apiopenstudio-php
    cd api
    ./includes/scripts/install.php

Follow all command prompts.

Congratulations!
----------------

You should now be able to visit the following URLs in your browser:

* [https://admin.apiopenstudio.local](https://admin.apiopenstudio.local)
* [https://wiki.apiopenstudio.local](https://wiki.apiopenstudio.local)
* [https://phpdoc.apiopenstudio.local](https://phpdoc.apiopenstudio.local)
