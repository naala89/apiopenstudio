Linting
=======

Composer will install `squizlabs/php_codesniffer` package by default,
which will install `phpcs`.

Run:

    $ ./vendor/bin/phpcs --standard=PSR12 \
        includes/ \
        public/*.php \
        tests/api/ \
        tests/_support/Helper/ \
        tests/runner_generate_db.php

Ensure that all code passes linting tests,
because the CI will fail any pull request or merge that fails the linting.
