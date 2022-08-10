Run linting outside GitLab Runner
=================================

Running them on your local computer
-----------------------------------

ensure you have run composer install locally. If you are not running docker:

    $ composer install

Run the following command:

    $ ./vendor/bin/phpcs --standard=PSR12 \
        includes/ \
        public/*.php \
        tests/api/ \
        tests/_support/Helper/ \
        tests/runner_generate_db.php

Running them inside docker
--------------------------

Clone, configure and run the
[ApiOpenStudio docker dev][docker_dev] repository.

SSH into the php container:

    $ docker exec -it apiopenstudio-php bash

Inside the container, run `phpcs` linting:

    $ ./api/vendor/bin/phpcs --standard=PSR12 \
        includes/ \
        public/*.php \
        tests/api/ \
        tests/_support/Helper/ \
        tests/runner_generate_db.php

Links
-----

- [ApiOpenStudio docker dev][docker_dev]
- [PHP CodeSniffer documentation][php_codesniffer_docs]

[docker_dev]: https://github.com/naala89/apiopenstudio_docker_dev

[php_codesniffer_docs]: https://github.com/squizlabs/PHP_CodeSniffer/wiki
