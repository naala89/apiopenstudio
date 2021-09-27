Testing
=======

The test suite is designed to be run locally and on GitLab CI.

Running locally
---------------

There are 2 ways of running the test suite locally...

### Using GitLab runner

See
[Setting up a local GitLab runner](/developers/sys-admin/setting-up-local-gitlab-runner)
for details on how to setup a local GitLab runner instance.

You will need to add the following config to the root level of
```gitlab-ci.yml```:

    variables:
        SSH_PRIVATE_KEY: "<flattened SSH key>"

e.g.:

    SSH_PRIVATE_KEY: "-----BEGIN OPENSSH PRIVATE KEY-----\nfoobar\n-----END OPENSSH PRIVATE KEY-----\n"

Add the following to the ```tests``` section of ```gitlab-ci.yml```:

    variables:
        MYSQL_ROOT_PASSWORD: apiopenstudio
        MYSQL_USERNAME: apiopenstudio
        MYSQL_PASSWORD: apiopenstudio

Once set up, you can run the tests like so:

    $ gitlab-runner exec docker tests

#### Config

Running the test suite will overwrite the ```.env``` and ```settings.yml```
files.

**MAKE SURE YOU BACKUP YOUR MAIN ```.env``` AND ```settings.yml```
FILES BEFORE RUNNING TESTS.**

### Using the locally installed codecept

You need to have a fully functioning ApiOpenStudio, with test users installed in
the database.

If you are running testcase first time in api suite, then in your api directory
you will not have api tester file. You need to generate that so run following
command:

    $ vendor/bin/codecept build

Testing is done with [Codeception](http://codeception.com/). From Docroot, run:

    $ vendor/bin/codecept -v

#### Running tests

##### Run all tests

    $ ./vendor/bin/codecept run --env staging|local|prod

##### Run all api tests

    $ ./vendor/bin/codecept run --env staging|local|prod api

##### Run a specific test

    $ ./vendor/bin/codecept run --env staging|local|prod api testName

##### Run all unit tests

    $ ./vendor/bin/codecept run unit --env staging|local|prod

##### Run all functional api tests

    $ ./vendor/bin/codecept run api --env staging|local|prod

Creating Tests
--------------

    $ vendor/bin/codecept generate:cept TestName

Create api test
---------------

    $ vendor/bin/codecept generate:cept api TestName

Test YAML files
---------------

These are in ```/tests/_data/```
