Run linting outside GitLab Runner
=================================

Running them on your local computer
-----------------------------------

ensure you have run composer install locally:

    composer install

Run the following command:

    ./vendor/bin/phpcs --standard=PSR12 \
        includes/ \
        public/*.php \
        tests/api/ \
        tests/runner_generate_db.php

Running them inside docker
--------------------------

Clone, configure and run the 
[ApiOpenStudio docker dev][apiopenstudio_docker_dev] repository.

SSH into the php container:

    docker exec -it apiopenstudio-php /bin/bash

Navigate to the API docroot and run ```phpcs``` linting:

    cd api
    ./vendor/bin/phpcs --standard=PSR12 \
        includes/ \
        public/*.php \
        tests/api/ \
        tests/runner_generate_db.php

[apiopenstudio_docker_dev]: https://github.com/naala89/apiopenstudio_docker_dev
