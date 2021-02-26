Developer environment setup
===========================

This is suitable for a local dev environment.

Checkout the docker repo
------------------------

    git clone git@gitlab.com:john89/api_open_studio_admin.git

Configuration
-------------

    cp example.env .env

It will work out of the box. However,
if you change any settings in api_open_studio or api_open_studio_admin,
such as the domains or SQL settings,
you will need to edit these in the ```.env``` file too.
        
Hosts file
----------

Update ```/etc/hosts``` to contain:

    127.0.0.1      admin.apiopenstudio.local
    127.0.0.1      api.apiopenstudio.local

Optional

    127.0.0.1      wiki.apiopenstudio.local
    127.0.0.1      phpdoc.apiopenstudio.local

Spinning up docker
------------------

    cd api_open_studio_docker
    docker-compose up -d
    docker exec -it apiopenstudio-php /bin/bash
    cd api
    ./includes/scripts/install.php
