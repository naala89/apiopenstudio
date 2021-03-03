Testing
=======

Testing is done with [Codeception](http://codeception.com/).
From Docroot, run:

```$ vendor/bin/codecept -v```

If you are running testcase first time in api suite, then in your api directory you will not have api tester file. You need to generate that so run following command:

```$ vendor/bin/codecept build```

Config
------

Domains for testing in environments are stored in ```/tests/api.suite.yml```

Tests require a .env file:

    API_DOMAIN=api.apiopenstudio.local

The configuration defined in ```api.suite.yml``` will dynamically pull the domain name for the
local domain for the API from the ```.env``` file.

Testing user
------------

The following testing credentials  are stored in /tests/_support/Helper/api.php

* Account: apiopenstudio
* Application: Testing
* Username: tester
* Password: tester_pass

When you create the database, ensure that you include the tester credentials at the prompt.

Creating Tests
--------------

```$ vendor/bin/codecept generate:cept TestName```

Create api test
---------------

```$ vendor/bin/codecept generate:cept api TestName```

Test YAML files
---------------

These are in ```/tests/_data/```

Running tests
-------------

### Run all tests

```vendor/bin/codecept run --env staging|local|prod```

### Run all api tests

```vendor/bin/codecept run --env staging|local|prod api```

### Run a specific test

```vendor/bin/codecept run --env staging|local|prod api testName```

### Run all unit tests

```vendor/bin/codecept run unit --env staging|local|prod```

### Run all functional api tests

```vendor/bin/codecept run api --env staging|local|prod```
