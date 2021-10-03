Run Codeception outside GitLab Runner
=====================================

Although it is possible to run tests locally, it is much easier to do it using
docker.

Running tests inside docker
---------------------------

clone, configure and run the [ApiOpenStudio docker dev][apiopenstudio_docker_dev]
repository.

At a bare minimum, you will need the following containers:

- db
- composer
- nginx-proxy
- php
- api

SSH into the php container:

    $ docker exec -it apiopenstudio-php /bin/bash

Navigate to the API docroot:

    $ cd api

Install the database (ensure you say 'Y' to installing the test users):

    $ ./includes/scripts/install.php

The log path streams are configured for GitLab CI, which uses the internal
docker instance as the root. To see the logs, you will need to update
```debug.handlers.api_log_file.stream``` and 
```debug.handlers.db_log_file.stream``` to use and absolute path in
```settings.yml```. For example (so that you can view the logs in your local
development area):

    stream: /var/www/html/api/log/api.log

    stream: /var/www/html/api/log/db.log

To run unit tests:

    $ ./vendor/bin/codecept run --env ci unit

To run functional tests:

    $ ./vendor/bin/codecept run --env ci api

To run specific functional tests, for example ```LoginCept```:

    $ ./vendor/bin/codecept run --env ci api LoginCept

Failed test logs are stored at ```tests/_output/```

Links
-----

- [codeception docs][codeception_docs]

[apiopenstudio_docker_dev]: https://github.com/naala89/apiopenstudio_docker_dev
[codeception_docs]: https://codeception.com/docs/
